<?php

use App\Http\Controllers\Seller\PickupRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('pickup-request')->name('seller.pickup-request.')->group(function () {
    Route::get('/', [PickupRequestController::class, 'index'])->name('index');
    Route::get('/dashboard', [PickupRequestController::class, 'dashboard'])->name('dashboard');
    Route::get('/create', [PickupRequestController::class, 'create'])->name('create');
    Route::post('/', [PickupRequestController::class, 'store'])->name('store');
    Route::get('/{id}', [PickupRequestController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PickupRequestController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PickupRequestController::class, 'update'])->name('update');
    Route::post('/{id}/cancel', [PickupRequestController::class, 'cancel'])->name('cancel');
    Route::post('/{id}/confirm', [PickupRequestController::class, 'confirm'])->name('confirm');
    Route::post('/{id}/schedule', [PickupRequestController::class, 'schedulePickup'])->name('schedule');
    Route::post('/check-wallet-balance', [PickupRequestController::class, 'checkWalletBalance'])->name('check-wallet-balance');
    Route::post('/{id}/start-delivery', [PickupRequestController::class, 'startDelivery'])->name('start-delivery');
});