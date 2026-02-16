<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AirlineController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar maskapai berhasil dimuat',
            'data' => Airline::paginate(10)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:airlines',
            'name' => 'required|string',
            'logo' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $airline = Airline::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Maskapai berhasil ditambahkan',
            'data' => $airline
        ], 201);
    }

    public function show($id)
    {
        $airline = Airline::find($id);
        if (!$airline) {
            return response()->json([
                'success' => false,
                'message' => 'Maskapai tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail maskapai berhasil dimuat',
            'data' => $airline
        ]);
    }

    public function update(Request $request, $id)
    {
        $airline = Airline::find($id);
        if (!$airline) {
            return response()->json([
                'success' => false,
                'message' => 'Maskapai tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|required|string|unique:airlines,code,' . $id,
            'name' => 'sometimes|required|string',
            'logo' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $airline->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Maskapai berhasil diperbarui',
            'data' => $airline
        ]);
    }

    public function destroy($id)
    {
        $airline = Airline::find($id);
        if (!$airline) {
            return response()->json([
                'success' => false,
                'message' => 'Maskapai tidak ditemukan'
            ], 404);
        }

        $airline->delete();

        return response()->json([
            'success' => true,
            'message' => 'Maskapai berhasil dihapus'
        ]);
    }
}
