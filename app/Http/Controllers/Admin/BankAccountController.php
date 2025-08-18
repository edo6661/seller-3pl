<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\BankAccount;
use App\Models\User;
use App\Services\WalletService;
use App\Enums\WalletTransactionType;
use App\Enums\WalletTransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class BankAccountController extends Controller
{
    /**
     * Manage bank accounts
     */
    public function bankAccounts(): View
    {
        $bankAccounts = BankAccount::orderBy('created_at', 'desc')->get();
        return view('admin.wallet.bank-accounts', [
            'bankAccounts' => $bankAccounts
        ]);
    }
    /**
     * Store bank account
     */
    public function storeBankAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
            'qr_code' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        try {
            $data = $request->only(['bank_name', 'account_number', 'account_name']);
            if ($request->hasFile('qr_code')) {
                $data['qr_code_path'] = $request->file('qr_code')->store('qr-codes', 'r2');
            }
            BankAccount::create($data);
            return back()->with('success', 'Rekening bank berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Store bank account error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan rekening bank.');
        }
    }
    /**
     * Update bank account
     */
    public function updateBankAccount(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
            'qr_code' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'boolean',
        ]);
        try {
            $bankAccount = BankAccount::findOrFail($id);
            $data = $request->only(['bank_name', 'account_number', 'account_name']);
            $data['is_active'] = $request->boolean('is_active');
            if ($request->hasFile('qr_code')) {
                if ($bankAccount->qr_code_path) {
                    Storage::disk('r2')->delete($bankAccount->qr_code_path);
                }
                $data['qr_code_path'] = $request->file('qr_code')->store('qr-codes', 'r2');
            }
            $bankAccount->update($data);
            return back()->with('success', 'Rekening bank berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Update bank account error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui rekening bank.');
        }
    }
    /**
     * Delete bank account
     */
    public function deleteBankAccount(int $id): RedirectResponse
    {
        try {
            $bankAccount = BankAccount::findOrFail($id);
            if ($bankAccount->qr_code_path) {
                Storage::disk('r2')->delete($bankAccount->qr_code_path);
            }
            $bankAccount->delete();
            return back()->with('success', 'Rekening bank berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Delete bank account error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus rekening bank.');
        }
    }
    
}