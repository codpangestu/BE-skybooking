<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacilityController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar fasilitas berhasil dimuat',
            'data' => Facility::paginate(10)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $facility = Facility::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil ditambahkan',
            'data' => $facility
        ], 201);
    }

    public function show($id)
    {
        $facility = Facility::find($id);
        if (!$facility) {
            return response()->json([
                'success' => false,
                'message' => 'Fasilitas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail fasilitas berhasil dimuat',
            'data' => $facility
        ]);
    }

    public function update(Request $request, $id)
    {
        $facility = Facility::find($id);
        if (!$facility) {
            return response()->json([
                'success' => false,
                'message' => 'Fasilitas tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'sometimes|required|string',
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $facility->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil diperbarui',
            'data' => $facility
        ]);
    }

    public function destroy($id)
    {
        $facility = Facility::find($id);
        if (!$facility) {
            return response()->json([
                'success' => false,
                'message' => 'Fasilitas tidak ditemukan'
            ], 404);
        }

        $facility->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fasilitas berhasil dihapus'
        ]);
    }
}
