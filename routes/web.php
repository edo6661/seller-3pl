<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('guest.home');
})->name('guest.home');

require __DIR__.'/guest/auth.php';

Route::middleware(['auth'])->group(function () {
    require __DIR__ . '/auth/profile.php';
    Route::middleware(['isAdmin'])->group(function () {
        Route::prefix('/admin')->group(function () {
            Route::get('/dashboard', function () {
                return view('admin.dashboard');
            })->name('admin.dashboard');
            require __DIR__ . '/admin/buyer_rating.php';
            require __DIR__ . '/admin/pickup_request.php';
            require __DIR__ . '/admin/products.php';
            require __DIR__ . '/admin/user.php';
            require __DIR__ . '/admin/wallet.php';
        });
    });
    Route::prefix('/seller')->group(function () {
        Route::get('/dashboard', function () {
            return view('seller.dashboard');
        })->name('seller.dashboard');
        require __DIR__ . '/seller/wallet.php';
        require __DIR__ . '/seller/products.php';
        require __DIR__ . '/seller/pickup_request.php';
    });
});
