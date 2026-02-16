<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Airport;

class AirportController extends Controller
{
    /**
     * Get list of airports for search selection.
     */
    public function index()
    {
        try {
            $airports = Airport::orderBy('city', 'asc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar bandara berhasil dimuat',
                'data' => $airports
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar bandara: ' . $e->getMessage()
            ], 500);
        }
    }
}
