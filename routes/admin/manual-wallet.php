<?php
use App\Http\Controllers\Admin\AdminManualWalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('/manual-wallet')->group(function () {
    // Dashboard
    Route::get('/', [AdminManualWalletController::class, 'index'])->name('admin.manual-wallet.index');
    
    // Top Up Management
    Route::post('/topup/{id}/approve', [AdminManualWalletController::class, 'approveTopUp'])->name('admin.manual-wallet.topup.approve');
    Route::post('/topup/{id}/reject', [AdminManualWalletController::class, 'rejectTopUp'])->name('admin.manual-wallet.topup.reject');
    Route::get('/topup/{id}/detail', [AdminManualWalletController::class, 'topUpDetail'])->name('admin.manual-wallet.topup.detail');
    
    // Withdraw Management
    Route::post('/withdraw/{id}/process', [AdminManualWalletController::class, 'processWithdraw'])->name('admin.manual-wallet.withdraw.process');
    Route::get('/withdraw/{id}/detail', [AdminManualWalletController::class, 'withdrawDetail'])->name('admin.manual-wallet.withdraw.detail');
    
    // Bank Account Management
    Route::get('/bank-accounts', [AdminManualWalletController::class, 'bankAccounts'])->name('admin.manual-wallet.bank-accounts');
    Route::post('/bank-accounts', [AdminManualWalletController::class, 'storeBankAccount'])->name('admin.manual-wallet.bank-accounts.store');
    Route::put('/bank-accounts/{id}', [AdminManualWalletController::class, 'updateBankAccount'])->name('admin.manual-wallet.bank-accounts.update');
    Route::delete('/bank-accounts/{id}', [AdminManualWalletController::class, 'deleteBankAccount'])->name('admin.manual-wallet.bank-accounts.delete');
});