<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/start', [ChatController::class, 'startChat'])->name('start');
    Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
    Route::post('/{conversation}/messages', [ChatController::class, 'store'])->name('messages.store');
    Route::get('/{conversation}/messages/older', [ChatController::class, 'getOlderMessages'])->name('messages.older');
    Route::post('/{conversation}/mark-read', [ChatController::class, 'markAsRead'])->name('mark-read');
    Route::post('/{conversation}/typing', [ChatController::class, 'typing'])->name('typing');
    Route::post('/{conversation}/stop-typing', [ChatController::class, 'stopTyping'])->name('stop-typing');
});