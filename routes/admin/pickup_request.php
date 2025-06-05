<?php
use App\Http\Controllers\Admin\PickupRequestController;
use Illuminate\Support\Facades\Route;

Route::resource('pickup-requests', PickupRequestController::class)->names([
    'index' => 'admin.pickup-requests.index',
])->only(['index']);