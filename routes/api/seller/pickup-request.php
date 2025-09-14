<?php
use App\Http\Controllers\Api\Seller\ApiSellerPickupRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('pickup-request')->group(function () {
    Route::get('/', [ApiSellerPickupRequestController::class, 'index']);
    Route::post('/', [ApiSellerPickupRequestController::class, 'store']);
    Route::get('/dashboard', [ApiSellerPickupRequestController::class, 'dashboard']);
    Route::get('/stats', [ApiSellerPickupRequestController::class, 'stats']);
    Route::get('/{id}', [ApiSellerPickupRequestController::class, 'show']);
    Route::put('/{id}', [ApiSellerPickupRequestController::class, 'update']);
    Route::patch('/{id}/cancel', [ApiSellerPickupRequestController::class, 'cancel']);
    Route::patch('/{id}/confirm', [ApiSellerPickupRequestController::class, 'confirm']);
    Route::patch('/{id}/schedule', [ApiSellerPickupRequestController::class, 'schedulePickup']);
    Route::get('/search/{query}', [ApiSellerPickupRequestController::class, 'search']);
});