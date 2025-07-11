<?php

use App\Http\Controllers\Admin\WalletController;
use Illuminate\Support\Facades\Route;

Route::resource('wallets', WalletController::class)->names([
    'index' => 'admin.wallets.index',
])->only(['index']);
