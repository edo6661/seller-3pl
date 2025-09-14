<?php

use App\Http\Controllers\Seller\TeamController;
use Illuminate\Support\Facades\Route;

Route::prefix('team')->name('seller.team.')->group(function () {
    Route::get('/', [TeamController::class, 'index'])
        ->middleware('teamPermission:team.view')
        ->name('index');
        
    Route::get('/create', [TeamController::class, 'create'])
        ->middleware('teamPermission:team.create')
        ->name('create');
        
    Route::post('/', [TeamController::class, 'store'])
        ->middleware('teamPermission:team.create')
        ->name('store');
        
    Route::get('/{id}/edit', [TeamController::class, 'edit'])
        ->middleware('teamPermission:team.edit')
        ->name('edit');
        
    Route::put('/{id}', [TeamController::class, 'update'])
        ->middleware('teamPermission:team.edit')
        ->name('update');
        
    Route::delete('/{id}', [TeamController::class, 'destroy'])
        ->middleware('teamPermission:team.delete')
        ->name('destroy');
        
    Route::patch('/{id}/toggle-status', [TeamController::class, 'toggleStatus'])
        ->middleware('teamPermission:team.edit')
        ->name('toggle-status');
});