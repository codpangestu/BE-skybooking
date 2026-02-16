<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar transaksi berhasil dimuat',
            'data' => Transaction::with(['flight', 'flightClass', 'promoCode'])->paginate(10)
        ]);
    }

    public function show($id)
    {
        $transaction = Transaction::with(['flight', 'flightClass', 'promoCode', 'passengers'])->find($id);
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail transaksi berhasil dimuat',
            'data' => $transaction
        ]);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $transaction->update($request->only('payment_status'));

        return response()->json([
            'success' => true,
            'message' => 'Status transaksi berhasil diperbarui',
            'data' => $transaction
        ]);
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus'
        ]);
    }
}
