<?php
use App\Http\Controllers\Admin\PickupRequestController;
use Illuminate\Support\Facades\Route;

Route::resource('pickup-requests', PickupRequestController::class)->names([
    'index' => 'admin.pickup-requests.index',
    'create' => 'admin.pickup-requests.create',
    'store' => 'admin.pickup-requests.store',
    'show' => 'admin.pickup-requests.show',
    'edit' => 'admin.pickup-requests.edit',
    'update' => 'admin.pickup-requests.update',
    'destroy' => 'admin.pickup-requests.destroy',
]);