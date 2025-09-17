<?php

use App\Http\Controllers\Seller\PickupRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('pickup-request')->name('seller.pickup-request.')->group(function () {
    Route::get('/', [PickupRequestController::class, 'index'])
        ->middleware('teamPermission:pickup.view')
        ->name('index');
    
    Route::get('/dashboard', [PickupRequestController::class, 'dashboard'])
        ->middleware('teamPermission:pickup.view')
        ->name('dashboard');
    
    Route::get('/create', [PickupRequestController::class, 'create'])
        ->middleware('teamPermission:pickup.create')
        ->name('create');
    
    Route::post('/', [PickupRequestController::class, 'store'])
        ->middleware('teamPermission:pickup.create')
        ->name('store');
    
    Route::get('/{id}', [PickupRequestController::class, 'show'])
        ->middleware('teamPermission:pickup.view')
        ->name('show');
    
    Route::get('/{id}/edit', [PickupRequestController::class, 'edit'])
        ->middleware('teamPermission:pickup.manage')
        ->name('edit');
    
    Route::put('/{id}', [PickupRequestController::class, 'update'])
        ->middleware('teamPermission:pickup.manage')
        ->name('update');
    
    Route::post('/{id}/cancel', [PickupRequestController::class, 'cancel'])
        ->middleware('teamPermission:pickup.manage')
        ->name('cancel');
    
    Route::post('/{id}/confirm', [PickupRequestController::class, 'confirm'])
        ->middleware('teamPermission:pickup.manage')
        ->name('confirm');
    
    Route::post('/{id}/schedule', [PickupRequestController::class, 'schedulePickup'])
        ->middleware('teamPermission:pickup.manage')
        ->name('schedule');
    
    Route::post('/check-wallet-balance', [PickupRequestController::class, 'checkWalletBalance'])
        ->middleware('teamPermission:pickup.create')
        ->name('check-wallet-balance');
    
    Route::post('/{id}/start-delivery', [PickupRequestController::class, 'startDelivery'])
        ->middleware('teamPermission:pickup.manage')
        ->name('start-delivery');
    Route::get('/{id}/create-ticket', [PickupRequestController::class, 'createTicketFromPickup'])
        ->middleware('teamPermission:pickup.view')
        ->name('create-ticket');
    });