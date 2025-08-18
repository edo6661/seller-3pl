<?php
use App\Http\Controllers\Seller\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('/wallet')->group(function () {
    // Main wallet page
    Route::get('/', [WalletController::class, 'index'])->name('seller.wallet.index');
    
    // Top Up Routes
    Route::get('/topup', [WalletController::class, 'topUp'])->name('seller.wallet.topup');
    Route::post('/topup', [WalletController::class, 'topUpSubmit'])->name('seller.wallet.topup.submit');
    Route::get('/topup/{referenceId}/payment', [WalletController::class, 'topUpPayment'])->name('seller.wallet.topup.payment');
    Route::post('/topup/{referenceId}/set-bank', [WalletController::class, 'setTopUpBank'])->name('seller.wallet.topup.set-bank');
    Route::get('/topup/{referenceId}/upload', [WalletController::class, 'topUpUpload'])->name('seller.wallet.topup.upload');
    Route::post('/topup/{referenceId}/upload', [WalletController::class, 'uploadPaymentProof'])->name('seller.wallet.topup.upload.submit');
    Route::get('/topup/{referenceId}/resume', [WalletController::class, 'resumeTopUpProcess'])->name('seller.wallet.topup.resume');
    Route::post('/topup/{referenceId}/cancel', [WalletController::class, 'cancelTopUpRequest'])->name('seller.wallet.topup.cancel');
    
    // Withdraw Routes
    Route::get('/withdraw', [WalletController::class, 'withdraw'])->name('seller.wallet.withdraw');
    Route::post('/withdraw', [WalletController::class, 'withdrawSubmit'])->name('seller.wallet.withdraw.submit');
    
    // Transaction management
    Route::get('/transaction/{id}', [WalletController::class, 'transactionDetail'])->name('seller.wallet.transaction.detail');
    Route::post('/transaction/{id}/cancel', [WalletController::class, 'cancelTransaction'])->name('seller.wallet.transaction.cancel');
});
Route::post('/wallet/midtrans/notification', [WalletController::class, 'midtransNotification'])
    ->name('wallet.midtrans.notification')
    ->withoutMiddleware(['auth']);
