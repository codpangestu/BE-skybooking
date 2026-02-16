<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AirportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $airports = Airport::paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Daftar bandara berhasil dimuat',
            'data' => $airports
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'iata_code' => 'required|string|unique:airports',
            'name' => 'required|string',
            'image' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $airport = Airport::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Bandara berhasil ditambahkan',
            'data' => $airport
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $airport = Airport::find($id);

        if (!$airport) {
            return response()->json([
                'success' => false,
                'message' => 'Bandara tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail bandara berhasil dimuat',
            'data' => $airport
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $airport = Airport::find($id);

        if (!$airport) {
            return response()->json([
                'success' => false,
                'message' => 'Bandara tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'iata_code' => 'sometimes|required|string|unique:airports,iata_code,' . $id,
            'name' => 'sometimes|required|string',
            'image' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'country' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $airport->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Bandara berhasil diperbarui',
            'data' => $airport
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $airport = Airport::find($id);

        if (!$airport) {
            return response()->json([
                'success' => false,
                'message' => 'Bandara tidak ditemukan'
            ], 404);
        }

        $airport->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bandara berhasil dihapus'
        ]);
    }
}