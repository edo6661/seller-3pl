<?php
use App\Http\Controllers\Seller\SupportTicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('support')->name('seller.support.')->group(function () {
    Route::get('/', [SupportTicketController::class, 'index'])->name('index');
    Route::get('/create', [SupportTicketController::class, 'create'])->name('create');
    Route::post('/', [SupportTicketController::class, 'store'])->name('store');
    Route::get('/{id}', [SupportTicketController::class, 'show'])->name('show');
    Route::post('/{id}/response', [SupportTicketController::class, 'addResponse'])->name('add-response');
    Route::post('/{id}/reopen', [SupportTicketController::class, 'reopen'])->name('reopen');
    
    // AJAX routes
    Route::get('/search/pickup-request', [SupportTicketController::class, 'searchPickupRequest'])->name('search-pickup');
    Route::get('/unread-count', [SupportTicketController::class, 'getUnreadCount'])->name('unread-count');
});
