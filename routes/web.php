<?php

use App\Collection\UserCollection;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\ChatController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('guest.home');
})->name('guest.home');

require __DIR__.'/guest/auth.php';
Route::get('chat/unread-count', [ChatController::class, 'getUnreadCount'])->name('unread-count');


Route::middleware(['auth'])->group(function () {
    require __DIR__ . '/auth/profile.php';
    
    require __DIR__ . '/chat.php';
    
    Route::middleware(['isAdmin'])->group(function () {
        Route::prefix('/admin')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
            require __DIR__ . '/admin/buyer_rating.php';
            require __DIR__ . '/admin/pickup_request.php';
            require __DIR__ . '/admin/products.php';
            require __DIR__ . '/admin/user.php';
            require __DIR__ . '/admin/wallet.php';
        });
    });
    
    Route::prefix('/seller')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('seller.dashboard');
        require __DIR__ . '/seller/wallet.php';
        require __DIR__ . '/seller/products.php';
        require __DIR__ . '/seller/pickup_request.php';
    });
});