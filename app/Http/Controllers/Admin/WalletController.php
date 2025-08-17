<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawRequest;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    public function index(Request $request)
    {
        $search = $request->get('search');
        $transactionType = $request->get('transaction_type');
        $transactionStatus = $request->get('transaction_status');
        $withdrawStatus = $request->get('withdraw_status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $perPage = $request->get('per_page', 15);
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
        $withdrawRequestsQuery = WithdrawRequest::with(['user'])
            ->select('withdraw_requests.*')
            ->join('users', 'withdraw_requests.user_id', '=', 'users.id');
        if ($search) {
            $withdrawRequestsQuery->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('withdraw_requests.withdrawal_code', 'like', "%{$search}%");
            });
        }
        if ($withdrawStatus) {
            $withdrawRequestsQuery->where('withdraw_requests.status', $withdrawStatus);
        } else {
            $withdrawRequestsQuery->where('withdraw_requests.status', 'pending');
        }
        if ($dateFrom) {
            $withdrawRequestsQuery->whereDate('withdraw_requests.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $withdrawRequestsQuery->whereDate('withdraw_requests.created_at', '<=', $dateTo);
        }
        $withdrawRequests = $withdrawRequestsQuery->orderBy('withdraw_requests.created_at', 'desc')
                                                ->paginate($perPage, ['*'], 'withdraws_page');
        return view('admin.wallet.index', compact(
            'walletStats',
            'wallets', 
            'transactions', 
            'withdrawRequests',
            'search',
            'transactionType',
            'transactionStatus', 
            'withdrawStatus',
            'dateFrom',
            'dateTo',
            'perPage'
        ));
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
            'pending_withdraws' => WithdrawRequest::where('status', 'pending')->count(),
            'pending_withdraw_amount' => WithdrawRequest::where('status', 'pending')->sum('amount'),
        ];
    }
}