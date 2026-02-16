<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\AirportController as AdminAirportController;
use App\Http\Controllers\Api\Admin\AirlineController as AdminAirlineController;
use App\Http\Controllers\Api\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\Api\Admin\PromoCodeController as AdminPromoCodeController;
use App\Http\Controllers\Api\Admin\FlightController as AdminFlightController;
use App\Http\Controllers\Api\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;

use App\Http\Controllers\Api\AirportController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\PromoCodeController;
use App\Http\Controllers\Api\TransactionController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Discovery
Route::get('/airports', [AirportController::class, 'index']);
Route::get('/airlines', [AdminAirlineController::class, 'index']);
Route::get('/airlines/{id}', [AdminAirlineController::class, 'show']);
Route::get('/flights', [FlightController::class, 'index']);
Route::get('/flights/{id}', [FlightController::class, 'show']);


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (USER & ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // 🔹 CURRENT LOGIN USER (PROFILE)
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil dimuat',
            'data' => $request->user()
        ]);
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // User Side (Booking & Transactions)
    Route::get('/airlines', [AdminAirlineController::class, 'index']);
    Route::post('/promo-codes/check', [PromoCodeController::class, 'check']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::post('/transactions', [TransactionController::class, 'store']);
});


/*
|--------------------------------------------------------------------------
| ADMIN ONLY ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {

    // Airports (Admin CRUD)
    Route::post('/airports', [AdminAirportController::class, 'store']);
    Route::put('/airports/{id}', [AdminAirportController::class, 'update']);
    Route::delete('/airports/{id}', [AdminAirportController::class, 'destroy']);

    // Airlines (Admin CRUD)
    Route::post('/airlines', [AdminAirlineController::class, 'store']);
    Route::put('/airlines/{id}', [AdminAirlineController::class, 'update']);
    Route::delete('/airlines/{id}', [AdminAirlineController::class, 'destroy']);

    // Facilities (Admin CRUD)
    Route::get('/facilities', [AdminFacilityController::class, 'index']);
    Route::post('/facilities', [AdminFacilityController::class, 'store']);
    Route::put('/facilities/{id}', [AdminFacilityController::class, 'update']);
    Route::delete('/facilities/{id}', [AdminFacilityController::class, 'destroy']);

    // Promo Codes (Admin CRUD)
    Route::get('/promo-codes', [AdminPromoCodeController::class, 'index']);
    Route::post('/promo-codes', [AdminPromoCodeController::class, 'store']);
    Route::put('/promo-codes/{id}', [AdminPromoCodeController::class, 'update']);
    Route::delete('/promo-codes/{id}', [AdminPromoCodeController::class, 'destroy']);

    // Flights (Admin Management)
    Route::post('/flights', [AdminFlightController::class, 'store']);
    Route::put('/flights/{id}', [AdminFlightController::class, 'update']);
    Route::delete('/flights/{id}', [AdminFlightController::class, 'destroy']);

    // Transactions History (Admin)
    Route::get('/admin/transactions', [AdminTransactionController::class, 'index']);
    Route::put('/admin/transactions/{id}', [AdminTransactionController::class, 'update']);
    Route::delete('/admin/transactions/{id}', [AdminTransactionController::class, 'destroy']);

    // User Management
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::put('/users/{id}/role', [AdminUserController::class, 'updateRole']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);
});
