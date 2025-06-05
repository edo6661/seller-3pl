<?php

use App\Http\Controllers\Guest\AuthController;
use App\Http\Controllers\Guest\AuthProviderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])
    ->group(function () {
        Route::prefix('auth')
        ->group(function() {
                Route::get('/redirect/{provider}', [AuthProviderController::class, 'redirect'])->name('guest.auth.redirect');
                Route::get('/callback/{provider}', action: [AuthProviderController::class,'callback'])->name('guest.auth.callback');
                
                Route::get('/login', [AuthController::class, 'login'])
                    ->name('guest.auth.login');
                Route::post('/login', [AuthController::class, 'loginSubmit'])
                    ->name('guest.auth.login.submit');
                Route::get('/register', [AuthController::class, 'register'])
                    ->name('guest.auth.register');
                Route::post('/register', [AuthController::class, 'registerSubmit'])
                    ->name('guest.auth.register.submit');
                Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])
                    ->name('guest.auth.forgot-password');
                Route::post('/forgot-password', [AuthController::class, 'forgotPasswordSubmit'])
                    ->name('guest.auth.forgot-password.submit');
                
                
                Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])
                    ->name('guest.auth.reset-password');
                Route::post('/reset-password', [AuthController::class, 'resetPasswordSubmit'])
                    ->name('guest.auth.reset-password.submit');
                
                
                Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
                    ->name('guest.auth.verification.verify');
                Route::post('/resend-verification', [AuthController::class, 'resendVerification'])
                    ->name('guest.auth.verification.resend');
            });
    });


Route::middleware(['auth'])
    ->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');
    });