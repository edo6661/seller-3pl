<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('guest.home');
})->name('guest.home');
require __DIR__.'/guest/auth.php';

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::prefix('/admin')->group(function () {
        require __DIR__.'/admin/buyer_rating.php';
        require __DIR__.'/admin/pickup_request.php';
        require __DIR__.'/admin/products.php';
        require __DIR__.'/admin/user.php';
        require __DIR__.'/admin/wallet.php';
    });
});
