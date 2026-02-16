<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FlightController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar jadwal penerbangan berhasil dimuat',
            'data' => Flight::with(['airline', 'segments.airport', 'classes'])->paginate(10)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'flight_number' => 'required|string|unique:flights',
            'airline_id' => 'required|exists:airlines,id',
            'segments' => 'required|array|min:2',
            'segments.*.airport_id' => 'required|exists:airports,id',
            'segments.*.sequence' => 'required|integer',
            'segments.*.time' => 'required|date_format:Y-m-d H:i:s', 
            'classes' => 'required|array|min:1',
            'classes.*.class_type' => 'required|in:economy,bussiness',
            'classes.*.price' => 'required|integer',
            'classes.*.total_seats' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                // 1. Create Flight
                $flight = Flight::create([
                    'flight_number' => $request->flight_number,
                    'airline_id' => $request->airline_id,
                ]);

                // 2. Create Segments (Schedules)
                foreach ($request->segments as $segment) {
                    $flight->segments()->create($segment);
                }

                // 3. Create Classes (Availability)
                foreach ($request->classes as $class) {
                    $flight->classes()->create($class);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal penerbangan berhasil ditambahkan',
                    'data' => $flight->load(['segments', 'classes'])
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $flight = Flight::with(['airline', 'segments.airport', 'classes.facilities'])->find($id);
        if (!$flight) {
            return response()->json([
                'success' => false,
                'message' => 'Penerbangan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail jadwal berhasil dimuat',
            'data' => $flight
        ]);
    }

    public function update(Request $request, $id)
    {
        $flight = Flight::find($id);
        if (!$flight) {
            return response()->json([
                'success' => false,
                'message' => 'Penerbangan tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'flight_number' => 'sometimes|required|string|unique:flights,flight_number,' . $id,
            'airline_id' => 'sometimes|required|exists:airlines,id',
            'segments' => 'sometimes|array|min:2',
            'segments.*.airport_id' => 'required|exists:airports,id',
            'segments.*.sequence' => 'required|integer',
            'segments.*.time' => 'required|date_format:Y-m-d H:i:s',
            'classes' => 'sometimes|array|min:1',
            'classes.*.class_type' => 'required|in:economy,bussiness',
            'classes.*.price' => 'required|integer',
            'classes.*.total_seats' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $flight) {
                $flight->update($request->only(['flight_number', 'airline_id']));

                if ($request->has('segments')) {
                    $flight->segments()->delete();
                    foreach ($request->segments as $segment) {
                        $flight->segments()->create($segment);
                    }
                }

                if ($request->has('classes')) {
                    $flight->classes()->delete();
                    foreach ($request->classes as $class) {
                        $flight->classes()->create($class);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal penerbangan berhasil diperbarui',
                    'data' => $flight->load(['segments', 'classes'])
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $flight = Flight::find($id);
        if (!$flight) {
            return response()->json([
                'success' => false,
                'message' => 'Penerbangan tidak ditemukan'
            ], 404);
        }

        $flight->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal penerbangan berhasil dihapus'
        ]);
    }
}
