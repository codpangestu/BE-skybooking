<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromoCodeController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar kode promo berhasil dimuat',
            'data' => PromoCode::paginate(10)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:promo_codes',
            'discount_type' => 'required|in:fixed,percentage',
            'discount' => 'required|integer',
            'valid_until' => 'required|date',
            'is_used' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $promoCode = PromoCode::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kode promo berhasil ditambahkan',
            'data' => $promoCode
        ], 201);
    }

    public function show($id)
    {
        $promoCode = PromoCode::find($id);
        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kode promo berhasil dimuat',
            'data' => $promoCode
        ]);
    }

    public function update(Request $request, $id)
    {
        $promoCode = PromoCode::find($id);
        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|required|string|unique:promo_codes,code,' . $id,
            'discount_type' => 'sometimes|required|in:fixed,percentage',
            'discount' => 'sometimes|required|integer',
            'valid_until' => 'sometimes|required|date',
            'is_used' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $promoCode->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kode promo berhasil diperbarui',
            'data' => $promoCode
        ]);
    }

    public function destroy($id)
    {
        $promoCode = PromoCode::find($id);
        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak ditemukan'
            ], 404);
        }

        $promoCode->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kode promo berhasil dihapus'
        ]);
    }
}
