<?php
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::resource('users', UserController::class)->names([
    'index' => 'admin.users.index',
])->only(['index']);

Route::patch('users/{user}/approve', [UserController::class, 'approve'])->name('admin.users.approve');
Route::patch('users/{user}/reject', [UserController::class, 'reject'])->name('admin.users.reject');
