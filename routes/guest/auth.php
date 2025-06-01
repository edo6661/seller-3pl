<?php

use App\Http\Controllers\Guest\AuthController;
use App\Http\Controllers\Guest\AuthProviderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])
    ->group(function () {
        Route::prefix('auth')
        ->name('guest.auth.')
        ->group(function() {
                Route::get('/redirect/{provider}', [AuthProviderController::class, 'redirect'])->name('redirect');
                Route::get('/callback/{provider}', action: [AuthProviderController::class,'callback'])->name('callback');
                
                Route::get('/login', [AuthController::class, 'login'])
                    ->name('login');
                Route::post('/login', [AuthController::class, 'loginSubmit'])
                    ->name('login.submit');
                
                
                Route::get('/register', [AuthController::class, 'register'])
                    ->name('register');
                Route::post('/register', [AuthController::class, 'registerSubmit'])
                    ->name('register.submit');
                
                
                Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])
                    ->name('forgot-password');
                Route::post('/forgot-password', [AuthController::class, 'forgotPasswordSubmit'])
                    ->name('forgot-password.submit');
                
                
                Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])
                    ->name('reset-password');
                Route::post('/reset-password', [AuthController::class, 'resetPasswordSubmit'])
                    ->name('reset-password.submit');
                
                
                Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
                    ->name('verification.verify');
                Route::post('/resend-verification', [AuthController::class, 'resendVerification'])
                    ->name('verification.resend');
            });
    });


Route::middleware(['auth'])
    ->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');
    });