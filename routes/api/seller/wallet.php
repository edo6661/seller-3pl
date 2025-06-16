<?php

use App\Http\Controllers\Api\Seller\ApiSellerWalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('wallet')->group(function () {
    Route::get('/', [ApiSellerWalletController::class, 'index']);

    Route::get('/transactions', [ApiSellerWalletController::class, 'getTransactions']);

    Route::get('/transactions/{id}', [ApiSellerWalletController::class, 'showTransaction']);

    Route::post('/transactions/{id}/cancel', [ApiSellerWalletController::class, 'cancelTransaction']);

    Route::post('/transactions/check-status', [ApiSellerWalletController::class, 'checkStatus']);

    Route::post('/topup', [ApiSellerWalletController::class, 'topUp']);

    Route::post('/withdraw', [ApiSellerWalletController::class, 'withdraw']);
});