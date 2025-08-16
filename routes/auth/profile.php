<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/{id}/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/{id}', [ProfileController::class, 'update'])->name('update');
    
    Route::get('/change-password', [ProfileController::class, 'changePasswordForm'])->name('change-password.form');
    Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change-password')->middleware('passwordValidation');

    Route::get('/resubmit-verification', [ProfileController::class, 'resubmitVerificationForm'])->name('verification.resubmit');
    Route::post('/resubmit-verification', [ProfileController::class, 'processResubmission'])->name('verification.process');

});