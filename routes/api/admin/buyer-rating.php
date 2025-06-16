<?php

use App\Http\Controllers\Api\Admin\BuyerRating\ApiBuyerRatingController;
use Illuminate\Support\Facades\Route;



Route::prefix('buyer-ratings')->group(function () {
    Route::get('/', [ApiBuyerRatingController::class, 'index']);

    Route::post('/', [ApiBuyerRatingController::class, 'store']);

    Route::get('/{id}', [ApiBuyerRatingController::class, 'show']);

    Route::put('/{id}', [ApiBuyerRatingController::class, 'update']);
    
    Route::delete('/{id}', [ApiBuyerRatingController::class, 'destroy']);

    Route::get('/stats/overview', [ApiBuyerRatingController::class, 'stats']);

    Route::get('/high-risk/list', [ApiBuyerRatingController::class, 'getHighRiskBuyers']);
});
