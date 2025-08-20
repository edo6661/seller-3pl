<?php
use App\Http\Controllers\Seller\WalletController;
use Illuminate\Support\Facades\Route;
Route::prefix('/wallet')->name('seller.wallet.')->group(function () {
    Route::get('/', [WalletController::class, 'index'])
        ->middleware('teamPermission:wallet.view')
        ->name('index');
    Route::get('/topup', [WalletController::class, 'topUp'])
        ->middleware('teamPermission:wallet.transaction')
        ->name('topup');
    Route::post('/topup', [WalletController::class, 'topUpSubmit'])
        ->middleware('teamPermission:wallet.transaction')
        ->name('topup.submit');
    Route::get('/topup/{referenceId}/payment', [WalletController::class, 'topUpPayment'])
        ->middleware('teamPermission:wallet.transaction')
        ->name('topup.payment');
    Route::post('/topup/{referenceId}/set-bank', [WalletController::class, 'setTopUpBank'])
        ->middleware('teamPermission:wallet.transaction')
        ->name('topup.set-bank');
    Route::get('/topup/{referenceId}/upload', [WalletController::class, 'topUpUpload'])
        ->middleware('teamPermission:wallet.transaction')
        ->name('topup.upload');
    Route::post('/topup/{referenceId}/upload', [WalletController::class, 'uploadPaymentProof'])
        ->middleware('teamPermission:wallet.transaction')
        ->name('topup.upload.submit');
    Route::get('/topup/{referenceId}/resume', [WalletController::class, 'resumeTopUpProcess'])
        ->middleware('teamPermission:wallet.transaction')
        ->name('topup.resume');
    Route::post('/topup/{referenceId}/cancel', [WalletController::class, 'cancelTopUpRequest'])
        ->middleware('teamPermission:wallet.transaction')
        ->name('topup.cancel');
    Route::get('/withdraw', [WalletController::class, 'withdraw'])
        ->middleware('teamPermission:wallet.transaction')
        ->name('withdraw');
    Route::post('/withdraw', [WalletController::class, 'withdrawSubmit'])
        ->middleware('teamPermission:wallet.transaction')
        ->name('withdraw.submit');
    Route::get('/transaction/{id}', [WalletController::class, 'transactionDetail'])
        ->middleware('teamPermission:wallet.view')
        ->name('transaction.detail');
    Route::post('/transaction/{id}/cancel', [WalletController::class, 'cancelTransaction'])
        ->middleware('teamPermission:wallet.transaction')
        ->name('transaction.cancel');
});
Route::post('/wallet/midtrans/notification', [WalletController::class, 'midtransNotification'])
    ->name('wallet.midtrans.notification')
    ->withoutMiddleware(['auth']);