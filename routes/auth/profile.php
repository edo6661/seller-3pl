<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/{id}/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/{id}', [ProfileController::class, 'update'])->name('update');
    
    // Change Password Routes
    Route::get('/change-password', [ProfileController::class, 'changePasswordForm'])->name('change-password.form');
    Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change-password')->middleware('passwordValidation');
});