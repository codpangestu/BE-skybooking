<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\TransactionPassenger;
use App\Models\Flight;
use App\Models\FlightClass;
use App\Models\PromoCode;
use App\Models\FlightSeat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Http\Resources\TransactionResource;

class TransactionController extends Controller
{
    /**
     * Get transaction history for current user.
     */
    public function index(Request $request)
    {
        try {
            $transactions = Transaction::with(['flight.airline', 'flight.segments.airport', 'flightClass'])
                ->where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return TransactionResource::collection($transactions)->additional([
                'success' => true,
                'message' => 'Riwayat transaksi berhasil dimuat',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new booking transaction.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'flight_id' => 'required|exists:flights,id',
            'flight_class_id' => 'required|exists:flight_classes,id',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'promo_code' => 'sometimes|nullable|string',
            'payment_method' => 'required|string',
            'passengers' => 'required|array|min:1',
            'passengers.*.name' => 'required|string',
            'passengers.*.date_of_birth' => 'required|date',
            'passengers.*.nationality' => 'required|string',
            'passengers.*.flight_seat_id' => 'sometimes|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                $flightId = $request->flight_id;
                $flightClassId = $request->flight_class_id;
                $numPassengers = count($request->passengers);

                // 1. Validasi kecocokan Flight dan Class
                $flightClass = FlightClass::where('id', $flightClassId)
                    ->where('flight_id', $flightId)
                    ->first();

                if (!$flightClass) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kelas penerbangan tidak cocok dengan jadwal yang dipilih'
                    ], 422);
                }

                // 2. Cek Ketersediaan Kursi (Schedules Availability Validation)
                $bookedSeats = Transaction::where('flight_id', $flightId)
                    ->where('flight_class_id', $flightClassId)
                    ->where('payment_status', '!=', 'failed')
                    ->sum('number_of_passengers');

                if (($bookedSeats + $numPassengers) > $flightClass->total_seats) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kursi tidak mencukupi. Sisa kursi: ' . ($flightClass->total_seats - $bookedSeats)
                    ], 422);
                }

                // 3. Perhitungan Biaya
                $subtotal = $flightClass->price * $numPassengers;
                $grandtotal = $subtotal;

                // 4. Proses Promo Code
                $promoId = null;
                if ($request->promo_code) {
                    $promo = PromoCode::where('code', $request->promo_code)->first();
                    if ($promo && !$promo->is_used && \Carbon\Carbon::parse($promo->valid_until)->isFuture()) {
                        $promoId = $promo->id;
                        if ($promo->discount_type === 'fixed') {
                            $grandtotal -= $promo->discount;
                        } else {
                            $grandtotal -= ($subtotal * ($promo->discount / 100));
                        }
                        // Mark promo as used
                        $promo->update(['is_used' => true]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Kode promo tidak valid atau sudah kadaluarsa'
                        ], 422);
                    }
                }

                // 5. Buat Transaksi (Recording Transaction)
                $transaction = Transaction::create([
                    'code' => 'TRX-' . strtoupper(Str::random(10)),
                    'user_id' => $request->user() ? $request->user()->id : null,
                    'flight_id' => $flightId,
                    'flight_class_id' => $flightClassId,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'number_of_passengers' => $numPassengers,
                    'promo_code_id' => $promoId,
                    'payment_status' => 'paid',
                    'payment_method' => $request->payment_method,
                    'subtotal' => $subtotal,
                    'grandtotal' => max(0, $grandtotal),
                ]);

                // 6. Simpan Data Penumpang (Saving Booking Data)
                foreach ($request->passengers as $passengerData) {
                    // Cek ketersediaan seat jika dipilih
                    if (!empty($passengerData['flight_seat_id'])) {
                        $isSeatTaken = TransactionPassenger::where('flight_seat_id', $passengerData['flight_seat_id'])
                            ->whereHas('transaction', function ($q) use ($flightId) {
                                $q->where('flight_id', $flightId)
                                    ->where('payment_status', '!=', 'failed');
                            })->exists();

                        if ($isSeatTaken) {
                            throw new \Exception('Salah satu kursi yang dipilih sudah dipesan.');
                        }
                    }

                    TransactionPassenger::create([
                        'transaction_id' => $transaction->id,
                        'name' => $passengerData['name'],
                        'date_of_birth' => $passengerData['date_of_birth'],
                        'nationality' => $passengerData['nationality'],
                        'flight_seat_id' => $passengerData['flight_seat_id'] ?? null,
                    ]);

                    // 6.1 Selesaikan ketersediaan kursi
                    if (!empty($passengerData['flight_seat_id'])) {
                        FlightSeat::where('id', $passengerData['flight_seat_id'])->update(['is_available' => false]);
                    }
                }

                return (new TransactionResource($transaction->load(['passengers', 'flight.airline', 'flight.segments.airport', 'flightClass'])))->additional([
                    'success' => true,
                    'message' => 'Booking berhasil. Sisa kursi di kelas ini: ' . ($flightClass->total_seats - ($bookedSeats + $numPassengers)),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction detail.
     */
    public function show($id)
    {
        try {
            $transaction = Transaction::with(['passengers.flightSeat', 'flight.airline', 'flight.segments.airport', 'flightClass', 'promoCode'])
                ->find($id);

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }

            return (new TransactionResource($transaction))->additional([
                'success' => true,
                'message' => 'Detail transaksi berhasil dimuat',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
