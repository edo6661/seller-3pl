<?php

namespace App\Http\Controllers\Api\Admin\Wallet;


use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionResource;
use App\Http\Resources\WithdrawRequestResource;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawRequest;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiWalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get wallet dashboard data (Admin API)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            
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
                                  ->paginate($perPage, ['wallets.*']);

            
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
                                            ->paginate($perPage, ['wallet_transactions.*']);

            
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
                                                    ->paginate($perPage, ['withdraw_requests.*']);

            return response()->json([
                'success' => true,
                'message' => 'Data wallet berhasil diambil',
                'data' => [
                    'stats' => $walletStats,
                    'wallets' => [
                        'data' => WalletResource::collection($wallets->items()),
                        'pagination' => [
                            'current_page' => $wallets->currentPage(),
                            'last_page' => $wallets->lastPage(),
                            'per_page' => $wallets->perPage(),
                            'total' => $wallets->total(),
                            'from' => $wallets->firstItem(),
                            'to' => $wallets->lastItem(),
                        ]
                    ],
                    'transactions' => [
                        'data' => WalletTransactionResource::collection($transactions->items()),
                        'pagination' => [
                            'current_page' => $transactions->currentPage(),
                            'last_page' => $transactions->lastPage(),
                            'per_page' => $transactions->perPage(),
                            'total' => $transactions->total(),
                            'from' => $transactions->firstItem(),
                            'to' => $transactions->lastItem(),
                        ]
                    ],
                    'withdraw_requests' => [
                        'data' => WithdrawRequestResource::collection($withdrawRequests->items()),
                        'pagination' => [
                            'current_page' => $withdrawRequests->currentPage(),
                            'last_page' => $withdrawRequests->lastPage(),
                            'per_page' => $withdrawRequests->perPage(),
                            'total' => $withdrawRequests->total(),
                            'from' => $withdrawRequests->firstItem(),
                            'to' => $withdrawRequests->lastItem(),
                        ]
                    ],
                    'filters' => [
                        'search' => $search,
                        'transaction_type' => $transactionType,
                        'transaction_status' => $transactionStatus,
                        'withdraw_status' => $withdrawStatus,
                        'date_from' => $dateFrom,
                        'date_to' => $dateTo
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data wallet',
                'error' => $e->getMessage()
            ], 500);
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
            'pending_withdraws' => WithdrawRequest::where('status', 'pending')->count(),
            'pending_withdraw_amount' => WithdrawRequest::where('status', 'pending')->sum('amount'),
        ];
    }
}