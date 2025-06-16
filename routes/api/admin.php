<?php 
use App\Http\Controllers\Api\Admin\Product\ApiProductController;
use App\Http\Controllers\Api\Admin\User\ApiUserController;
use App\Http\Controllers\Api\Admin\Wallet\ApiWalletController;
use Illuminate\Support\Facades\Route;
Route::middleware(['auth:sanctum','apiIsAdmin'])->prefix('admin')->group(function () {
    Route::get('/products', [ApiProductController::class, 'index']);
    
    Route::get('/users', [ApiUserController::class, 'index']);
    
    Route::get('/wallets', [ApiWalletController::class, 'index']);
});