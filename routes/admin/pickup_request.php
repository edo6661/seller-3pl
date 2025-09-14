<?php
use App\Http\Controllers\Admin\PickupRequestController;
use Illuminate\Support\Facades\Route;

Route::resource('pickup-requests', PickupRequestController::class)->names([
    'index' => 'admin.pickup-requests.index',
    'show' => 'admin.pickup-requests.show',
])->only(['index', 'show']);

// Status Update Routes
Route::prefix('pickup-requests')->name('admin.pickup-requests.')->group(function () {
    Route::post('{id}/confirm', [PickupRequestController::class, 'confirm'])->name('confirm');
    Route::post('{id}/schedule-pickup', [PickupRequestController::class, 'schedulePickup'])->name('schedule-pickup');
    Route::post('{id}/mark-picked-up', [PickupRequestController::class, 'markAsPickedUp'])->name('mark-picked-up');
    Route::post('{id}/mark-in-transit', [PickupRequestController::class, 'markAsInTransit'])->name('mark-in-transit');
    Route::post('{id}/mark-delivered', [PickupRequestController::class, 'markAsDelivered'])->name('mark-delivered');
    Route::post('{id}/mark-failed', [PickupRequestController::class, 'markAsFailed'])->name('mark-failed');
    Route::post('{id}/cancel', [PickupRequestController::class, 'cancel'])->name('cancel');
});