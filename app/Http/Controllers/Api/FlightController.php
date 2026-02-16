<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Flight;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\FlightResource;

class FlightController extends Controller
{
    /**
     * Search for flights based on filters.
     */
    public function index(Request $request)
    {
        try {
            $query = Flight::with(['airline', 'segments.airport', 'classes.facilities']);

            // Filter by Departure Airport 
            if ($request->has('departure_airport_id')) {
                $query->whereHas('segments', function ($q) use ($request) {
                    $q->where('airport_id', $request->departure_airport_id)
                        ->where('sequence', 1);
                });
            }

            // Filter by Arrival Airport 
            if ($request->has('arrival_airport_id')) {
                $query->whereHas('segments', function ($q) use ($request) {
                    $q->where('airport_id', $request->arrival_airport_id);
                });
            }

            // Filter by Date 
            if ($request->has('date')) {
                $query->whereHas('segments', function ($q) use ($request) {
                    $q->where('sequence', 1)
                        ->whereDate('time', $request->date);
                });
            }

            $flights = $query->paginate(10);

            return FlightResource::collection($flights)->additional([
                'success' => true,
                'message' => 'Penerbangan berhasil dimuat',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari penerbangan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed information for a specific flight.
     */
    public function show($id)
    {
        try {
            $flight = Flight::with(['airline', 'segments.airport', 'classes.facilities', 'seats'])
                ->find($id);

            if (!$flight) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penerbangan tidak ditemukan',
                ], 404);
            }

            return (new FlightResource($flight))->additional([
                'success' => true,
                'message' => 'Detail penerbangan berhasil dimuat',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
