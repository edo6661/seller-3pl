<?php

use App\Http\Controllers\Seller\AddressController;
use Illuminate\Support\Facades\Route;

Route::prefix('addresses')->name('seller.addresses.')->group(function () {
    Route::get('/', [AddressController::class, 'index'])->name('index');
    Route::get('/create', [AddressController::class, 'create'])->name('create');
    Route::post('/', action: [AddressController::class, 'store'])->name('store');
    Route::get('/{address}', [AddressController::class, 'show'])->name('show');
    Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
    Route::put('/{address}', [AddressController::class, 'update'])->name('update');
    Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
    Route::post('/{address}/set-default', [AddressController::class, 'setDefault'])
        ->name('set-default');

    Route::get('api/addresses', [AddressController::class, 'getAddresses'])
        ->name('api');
});