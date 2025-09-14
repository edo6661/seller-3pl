<?php
use App\Http\Controllers\Api\Seller\ApiSellerProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function () {
    Route::get('/', [ApiSellerProductController::class, 'index']);
    Route::post('/', [ApiSellerProductController::class, 'store']);
    Route::get('/{id}', [ApiSellerProductController::class, 'show']);
    Route::put('/{id}', [ApiSellerProductController::class, 'update']);
    Route::delete('/{id}', [ApiSellerProductController::class, 'destroy']);
    Route::patch('/{id}/toggle-status', [ApiSellerProductController::class, 'toggleStatus']);
    Route::get('/search/{query}', [ApiSellerProductController::class, 'search']);
    Route::get('/stats/overview', [ApiSellerProductController::class, 'stats']);
});
 