<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')->name('profile.')->group(function () {
  Route::get('/', [ProfileController::class, 'index'])->name('index');
  Route::get('/create', [ProfileController::class, 'create'])->name('create');
  Route::post('/', [ProfileController::class, 'store'])->name('store');
  Route::get('/{id}', [ProfileController::class, 'show'])->name('show');
  Route::get('/{id}/edit', [ProfileController::class, 'edit'])->name('edit');
  Route::patch('/{id}', [ProfileController::class, 'update'])->name('update');
});
