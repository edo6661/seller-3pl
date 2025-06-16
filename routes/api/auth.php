<?php

use App\Http\Controllers\Api\Auth\ApiAuthController;
use App\Http\Controllers\Api\Auth\ApiAuthProviderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/login', [ApiAuthController::class,'login']);
    Route::post('/register', action: [ApiAuthController::class, 'register']);
    Route::post('/forgot-password', [ApiAuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [ApiAuthController::class, 'resetPassword']);
    
    Route::get('/verify-email/{id}/{hash}', [ApiAuthController::class, 'verifyEmail'])
        ->name('api.auth.verification.verify');
    Route::post('/resend-verification', [ApiAuthController::class, 'resendVerification']);
    
    Route::get('/redirect/{provider}', [ApiAuthProviderController::class, 'redirect']);
    Route::get('/callback/{provider}', [ApiAuthProviderController::class, 'callback']);
    
    Route::post('/social-login/{provider}', [ApiAuthProviderController::class, 'loginWithToken']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [ApiAuthController::class, 'logout']);
    Route::get('/auth/me', [ApiAuthController::class, 'me']);
    Route::post('/auth/refresh', [ApiAuthController::class, 'refresh']);
    
    Route::post('/auth/logout-all', [ApiAuthController::class, 'logoutAll']);
    
    Route::get('/auth/tokens', [ApiAuthController::class, 'getTokens']);
    
    Route::delete('/auth/tokens/{tokenId}', [ApiAuthController::class, 'revokeToken']);
});