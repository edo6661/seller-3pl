<?php
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('support')->name('admin.support.')->group(function () {
    Route::get('/', [AdminSupportTicketController::class, 'index'])->name('index');
    Route::get('/dashboard', [AdminSupportTicketController::class, 'dashboard'])->name('dashboard');
    Route::get('/{id}', [AdminSupportTicketController::class, 'show'])->name('show');
    
    // Admin actions
    Route::post('/{id}/assign', [AdminSupportTicketController::class, 'assign'])->name('assign');
    Route::post('/{id}/update-status', [AdminSupportTicketController::class, 'updateStatus'])->name('update-status');
    Route::post('/{id}/resolve', [AdminSupportTicketController::class, 'resolve'])->name('resolve');
    Route::post('/{id}/response', [AdminSupportTicketController::class, 'addResponse'])->name('add-response');
    
    // Bulk actions
    Route::post('/bulk-action', [AdminSupportTicketController::class, 'bulkAction'])->name('bulk-action');
    
    // Export
    Route::get('/export', [AdminSupportTicketController::class, 'export'])->name('export');
});