<?php
use App\Http\Controllers\Seller\WalletController;
use Illuminate\Support\Facades\Route;
Route::prefix('/wallet')->group(function () {
    Route::get('/', [WalletController::class, 'index'])->name('seller.wallet.index');
    Route::get('/topup', [WalletController::class, 'topUp'])->name('seller.wallet.topup');
    Route::post('/topup', [WalletController::class, 'topUpSubmit'])->name('seller.wallet.topup.submit');
    Route::get('/topup/finish', [WalletController::class, 'topUpFinish'])->name('seller.wallet.topup.finish');
    Route::get('/withdraw', [WalletController::class, 'withdraw'])->name('seller.wallet.withdraw');
    Route::post('/withdraw', [WalletController::class, 'withdrawSubmit'])->name('seller.wallet.withdraw.submit');
    Route::get('/transaction/{id}', [WalletController::class, 'transactionDetail'])->name('seller.wallet.transaction.detail');
    Route::post('/transaction/{id}/cancel', [WalletController::class, 'cancelTransaction'])->name('seller.wallet.transaction.cancel');
    Route::get('/api/transactions', [WalletController::class, 'transactionHistory'])->name('seller.wallet.api.transactions');
    Route::post('/api/check-status', [WalletController::class, 'checkTransactionStatus'])->name('seller.wallet.api.check-status');
});
Route::post('/wallet/midtrans/notification', [WalletController::class, 'midtransNotification'])
    ->name('wallet.midtrans.notification')
    ->withoutMiddleware(['auth']);

require __DIR__ . '/manual-wallet.php';