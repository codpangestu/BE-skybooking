<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PromoCode;
use Carbon\Carbon;

class PromoCodeController extends Controller
{
    /**
     * Validate a promo code.
     */
    public function check(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string'
            ]);

            $promo = PromoCode::where('code', $request->code)->first();

            if (!$promo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Promo code not found'
                ], 404);
            }

            // Check if expired
            if (Carbon::parse($promo->valid_until)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Promo code has expired'
                ], 400);
            }

            // Check if already used (based on the boolean in schema)
            if ($promo->is_used) {
                return response()->json([
                    'success' => false,
                    'message' => 'Promo code has already been used'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Promo code is valid',
                'data' => $promo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
