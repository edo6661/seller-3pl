<?php

use App\Http\Controllers\Seller\AddressController;
use Illuminate\Support\Facades\Route;

Route::prefix('addresses')->name('seller.addresses.')->group(function () {
    Route::get('/', [AddressController::class, 'index'])
        ->middleware('teamPermission:addresses.view')
        ->name('index');

    Route::get('/create', [AddressController::class, 'create'])
        ->middleware('teamPermission:addresses.create')
        ->name('create');

    Route::post('/', [AddressController::class, 'store'])
        ->middleware('teamPermission:addresses.create')
        ->name('store');

    Route::get('/{address}', [AddressController::class, 'show'])
        ->middleware('teamPermission:addresses.view')
        ->name('show');

    Route::get('/{address}/edit', [AddressController::class, 'edit'])
        ->middleware('teamPermission:addresses.edit')
        ->name('edit');

    Route::put('/{address}', [AddressController::class, 'update'])
        ->middleware('teamPermission:addresses.edit')
        ->name('update');

    Route::delete('/{address}', [AddressController::class, 'destroy'])
        ->middleware('teamPermission:addresses.delete')
        ->name('destroy');

    Route::post('/{address}/set-default', [AddressController::class, 'setDefault'])
        ->middleware('teamPermission:addresses.edit')
        ->name('set-default');

    Route::get('api/addresses', [AddressController::class, 'getAddresses'])
        ->middleware('teamPermission:addresses.view')
        ->name('api');
});
