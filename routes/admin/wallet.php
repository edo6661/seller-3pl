<?php
use App\Http\Controllers\Admin\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('/wallet')->group(function () {
    // Dashboard
    Route::get('/', [WalletController::class, 'index'])->name('admin.wallets.index');
    
    // Top Up Management
    Route::post('/topup/{id}/approve', [WalletController::class, 'approveTopUp'])->name('admin.wallets.topup.approve');
    Route::post('/topup/{id}/reject', [WalletController::class, 'rejectTopUp'])->name('admin.wallets.topup.reject');
    
    // Withdraw Management
    Route::post('/withdraw/{id}/process', [WalletController::class, 'processWithdraw'])->name('admin.wallets.withdraw.process');
    
    // Transaction Detail
    Route::get('/transaction/{id}', [WalletController::class, 'transactionDetail'])->name('admin.wallets.transaction.detail');
    
    // Bank Account Management
    Route::get('/bank-accounts', [WalletController::class, 'bankAccounts'])->name('admin.wallets.bank-accounts');
    Route::post('/bank-accounts', [WalletController::class, 'storeBankAccount'])->name('admin.wallets.bank-accounts.store');
    Route::put('/bank-accounts/{id}', [WalletController::class, 'updateBankAccount'])->name('admin.wallets.bank-accounts.update');
    Route::delete('/bank-accounts/{id}', [WalletController::class, 'deleteBankAccount'])->name('admin.wallets.bank-accounts.delete');
    Route::get('/load-bank-accounts', [WalletController::class, 'loadBankAccounts'])->name('admin.wallets.bank-accounts.load');

});