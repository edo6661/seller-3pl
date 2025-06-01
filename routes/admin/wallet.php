<?php
use App\Http\Controllers\Admin\WalletController;
use Illuminate\Support\Facades\Route;

Route::resource('wallets', WalletController::class)->names([
    'index' => 'admin.wallets.index',
    'create' => 'admin.wallets.create',
    'store' => 'admin.wallets.store',
    'show' => 'admin.wallets.show',
    'edit' => 'admin.wallets.edit',
    'update' => 'admin.wallets.update',
    'destroy' => 'admin.wallets.destroy',
]);