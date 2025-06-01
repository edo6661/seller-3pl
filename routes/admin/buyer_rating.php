<?php
use App\Http\Controllers\Admin\BuyerRatingController;
use Illuminate\Support\Facades\Route;

Route::resource('buyer-ratings', BuyerRatingController::class)->names([
    'index' => 'admin.buyer-ratings.index',
    'create' => 'admin.buyer-ratings.create',
    'store' => 'admin.buyer-ratings.store',
    'show' => 'admin.buyer-ratings.show',
    'edit' => 'admin.buyer-ratings.edit',
    'update' => 'admin.buyer-ratings.update',
    'destroy' => 'admin.buyer-ratings.destroy',
]);