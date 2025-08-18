<?php

use App\Http\Controllers\Seller\TeamController;
use Illuminate\Support\Facades\Route;

Route::prefix('team')->name('seller.team.')->group(function () {
    Route::get('/', [TeamController::class, 'index'])->name('index');
    Route::get('/create', [TeamController::class, 'create'])->name('create');
    Route::post('/', [TeamController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [TeamController::class, 'edit'])->name('edit');
    Route::put('/{id}', [TeamController::class, 'update'])->name('update');
    Route::delete('/{id}', [TeamController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/toggle-status', [TeamController::class, 'toggleStatus'])->name('toggle-status');
});