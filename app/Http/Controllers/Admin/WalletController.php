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
use Illuminate\Http\JsonResponse; 
class WalletController extends Controller
{
    protected WalletService $walletService;
    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }
    /**
     * Display admin wallet dashboard
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $transactionType = $request->get('transaction_type');
        $transactionStatus = $request->get('transaction_status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $perPage = $request->get('per_page', 15);
        $withdrawStatus = $request->get('withdraw_status', '');
        $walletStats = $this->getWalletStatistics();
        $walletsQuery = Wallet::with(['user'])
            ->select('wallets.*')
            ->join('users', 'wallets.user_id', '=', 'users.id');
        if ($search) {
            $walletsQuery->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }
        $wallets = $walletsQuery->orderBy('wallets.balance', 'desc')
                              ->paginate($perPage, ['*'], 'wallets_page');
        $transactionsQuery = WalletTransaction::with(['wallet.user'])
            ->select('wallet_transactions.*')
            ->join('wallets', 'wallet_transactions.wallet_id', '=', 'wallets.id')
            ->join('users', 'wallets.user_id', '=', 'users.id');
        if ($search) {
            $transactionsQuery->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('wallet_transactions.reference_id', 'like', "%{$search}%");
            });
        }
        if ($transactionType) {
            $transactionsQuery->where('wallet_transactions.type', $transactionType);
        }
        if ($transactionStatus) {
            $transactionsQuery->where('wallet_transactions.status', $transactionStatus);
        }
        if ($dateFrom) {
            $transactionsQuery->whereDate('wallet_transactions.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $transactionsQuery->whereDate('wallet_transactions.created_at', '<=', $dateTo);
        }
        $transactions = $transactionsQuery->orderBy('wallet_transactions.created_at', 'desc')
                                        ->paginate($perPage, ['*'], 'transactions_page');
        $pendingTopUpQuery = WalletTransaction::with(['wallet.user'])
            ->where('type', WalletTransactionType::TOPUP)
            ->where('status', WalletTransactionStatus::PENDING)
            ->whereNotNull('payment_proof_path')
            ->select('wallet_transactions.*')
            ->join('wallets', 'wallet_transactions.wallet_id', '=', 'wallets.id')
            ->join('users', 'wallets.user_id', '=', 'users.id');
        if ($search) {
            $pendingTopUpQuery->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('wallet_transactions.reference_id', 'like', "%{$search}%");
            });
        }
        $pendingTopUps = $pendingTopUpQuery->orderBy('wallet_transactions.created_at', 'desc')
                                         ->paginate($perPage, ['*'], 'topup_page');
        $pendingWithdrawQuery = WalletTransaction::with(['wallet.user'])
            ->where('type', WalletTransactionType::WITHDRAW)
            ->where('status', WalletTransactionStatus::PENDING)
            ->select('wallet_transactions.*')
            ->join('wallets', 'wallet_transactions.wallet_id', '=', 'wallets.id')
            ->join('users', 'wallets.user_id', '=', 'users.id');
        if ($search) {
            $pendingWithdrawQuery->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('wallet_transactions.reference_id', 'like', "%{$search}%");
            });
        }
        $pendingWithdraws = $pendingWithdrawQuery->orderBy('wallet_transactions.created_at', 'desc')
                                               ->paginate($perPage, ['*'], 'withdraw_page');
        $withdrawRequests = $withdrawRequests = collect(); 
        if ($request->get('withdraw_status') === 'all') {
            $withdrawRequests = WalletTransaction::with(['wallet.user'])
                ->where('type', WalletTransactionType::WITHDRAW)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'withdraw_page');
        }
        return view('admin.wallet.index', compact(
            'walletStats',
            'wallets', 
            'transactions', 
            'pendingTopUps',
            'pendingWithdraws',
            'search',
            'transactionType',
            'transactionStatus',
            'dateFrom',
            'dateTo',
            'perPage',
            'withdrawStatus',
            'withdrawRequests'
        ));
    }
    /**
     * Approve top up request
     */
    public function approveTopUp(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500'
        ]);
        try {
            $transaction = WalletTransaction::findOrFail($id);
            $this->walletService->approveTopUp($transaction, $request->admin_notes);
            return back()->with('success', 'Top up request berhasil disetujui.');
        } catch (\Exception $e) {
            Log::error('Approve top up error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyetujui top up request.');
        }
    }
    /**
     * Reject top up request
     */
    public function rejectTopUp(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ], [
            'admin_notes.required' => 'Alasan penolakan harus diisi.'
        ]);
        try {
            $transaction = WalletTransaction::findOrFail($id);
            $this->walletService->rejectTopUp($transaction, $request->admin_notes);
            return back()->with('success', 'Top up request berhasil ditolak.');
        } catch (\Exception $e) {
            Log::error('Reject top up error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menolak top up request.');
        }
    }
    /**
     * Process withdraw request
     */
    public function processWithdraw(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:processing,completed,failed,cancelled',
            'admin_notes' => 'nullable|string|max:500'
        ]);
        try {
            $transaction = WalletTransaction::findOrFail($id);
            $this->walletService->processWithdrawRequest($transaction, $request->status, $request->admin_notes);
            $statusText = match($request->status) {
                'processing' => 'sedang diproses',
                'completed' => 'diselesaikan',
                'failed' => 'ditolak',
                'cancelled' => 'dibatalkan',
            };
            return back()->with('success', "Permintaan penarikan berhasil {$statusText}.");
        } catch (\Exception $e) {
            Log::error('Process withdraw error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses permintaan penarikan.');
        }
    }
    /**
     * View transaction detail
     */
    public function transactionDetail(int $id): View
    {
        $transaction = WalletTransaction::with(['wallet.user'])->findOrFail($id);
        return view('admin.wallet.transaction-detail', [
            'transaction' => $transaction
        ]);
    }
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
    public function loadBankAccounts(): JsonResponse
    {
        try {
            $bankAccounts = BankAccount::active()->orderBy('created_at', 'asc')->get();
            return response()->json($bankAccounts);
        } catch (\Exception $e) {
            Log::error('Load bank accounts error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data rekening bank.'], 500);
        }
    }
    /**
     * Get wallet statistics
     */
    private function getWalletStatistics(): array
    {
        return [
            'total_wallets' => Wallet::count(),
            'total_balance' => Wallet::sum('balance'),
            'total_pending_balance' => Wallet::sum('pending_balance'),
            'active_users' => Wallet::where('balance', '>', 0)->count(),
            'total_transactions_today' => WalletTransaction::whereDate('created_at', today())->count(),
            'total_topup_today' => WalletTransaction::where('type', 'topup')
                                                  ->whereDate('created_at', today())
                                                  ->where('status', 'success')
                                                  ->sum('amount'),
            'total_withdraw_today' => WalletTransaction::where('type', 'withdraw')
                                                     ->whereDate('created_at', today())
                                                     ->where('status', 'success')
                                                     ->sum('amount'),
            'pending_topups' => WalletTransaction::where('type', 'topup')
                                               ->where('status', 'pending')
                                               ->whereNotNull('payment_proof_path')
                                               ->count(),
            'pending_topup_amount' => WalletTransaction::where('type', 'topup')
                                                     ->where('status', 'pending')
                                                     ->whereNotNull('payment_proof_path')
                                                     ->sum('amount'),
            'pending_withdraws' => WalletTransaction::where('type', 'withdraw')
                                                  ->where('status', 'pending')
                                                  ->count(),
            'pending_withdraw_amount' => WalletTransaction::where('type', 'withdraw')
                                                        ->where('status', 'pending')
                                                        ->sum('amount'),
        ];
    }
}