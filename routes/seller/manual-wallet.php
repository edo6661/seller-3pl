<?php

// routes/seller/manual-wallet.php
use App\Http\Controllers\Seller\ManualWalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('/manual-wallet')->group(function () {
    // Manual Top Up Routes
    Route::get('/topup', [ManualWalletController::class, 'manualTopUp'])->name('seller.wallet.manual-topup');
    Route::post('/topup', [ManualWalletController::class, 'manualTopUpSubmit'])->name('seller.wallet.manual-topup.submit');
    Route::get('/topup/{requestCode}/payment', [ManualWalletController::class, 'manualTopUpPayment'])->name('seller.wallet.manual-topup.payment');
    Route::post('/topup/{requestCode}/set-bank', [ManualWalletController::class, 'setTopUpBank'])->name('seller.wallet.manual-topup.set-bank');
    Route::get('/topup/{requestCode}/upload', [ManualWalletController::class, 'manualTopUpUpload'])->name('seller.wallet.manual-topup.upload');
    Route::post('/topup/{requestCode}/upload', [ManualWalletController::class, 'uploadPaymentProof'])->name('seller.wallet.manual-topup.upload.submit');
    Route::get('/topup/{requestCode}/detail', [ManualWalletController::class, 'topUpRequestDetail'])->name('seller.wallet.manual-topup.detail');
    Route::post('/topup/{requestCode}/cancel', [ManualWalletController::class, 'cancelTopUpRequest'])->name('seller.wallet.manual-topup.cancel');
    
    // Manual Withdraw Routes
    Route::get('/withdraw', [ManualWalletController::class, 'manualWithdraw'])->name('seller.wallet.manual-withdraw');
    Route::post('/withdraw', [ManualWalletController::class, 'manualWithdrawSubmit'])->name('seller.wallet.manual-withdraw.submit');

    Route::get('/topup/{requestCode}/resume', [ManualWalletController::class, 'resumeTopUpProcess'])->name('seller.wallet.manual-topup.resume');

});
