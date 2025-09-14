<?php


namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Requests\Wallet\TopUpRequest;
use App\Services\WalletService;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionResource;
use App\Requests\Wallet\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ApiSellerWalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Dapatkan informasi dompet dan beberapa transaksi terakhir.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $wallet = $this->walletService->getOrCreateWallet($user);
            
            $recentTransactions = $this->walletService->getTransactionHistory($user, 5);

            return response()->json([
                'success' => true,
                'message' => 'Data dompet berhasil diambil',
                'data' => [
                    'wallet' => new WalletResource($wallet),
                    'recent_transactions' => WalletTransactionResource::collection($recentTransactions),
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->handleError($e, 'Gagal mengambil data dompet');
        }
    }

    /**
     * Dapatkan riwayat transaksi dengan pagination dan filter.
     */
    public function getTransactions(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $wallet = $this->walletService->getOrCreateWallet($user);
            
            $perPage = $request->get('per_page', 10);
            $type = $request->get('type'); 
            $status = $request->get('status'); 

            $query = $wallet->transactions();

            if ($type) {
                $query->where('type', $type);
            }

            if ($status) {
                $query->where('status', $status);
            }

            $transactions = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

            return response()->json([
                'success' => true,
                'message' => 'Riwayat transaksi berhasil diambil',
                'data' => [
                    'transactions' => WalletTransactionResource::collection($transactions->items()),
                    'pagination' => [
                        'current_page' => $transactions->currentPage(),
                        'last_page' => $transactions->lastPage(),
                        'per_page' => $transactions->perPage(),
                        'total' => $transactions->total(),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return $this->handleError($e, 'Gagal mengambil riwayat transaksi');
        }
    }

    /**
     * Tampilkan detail satu transaksi.
     */
    public function showTransaction(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $transaction = $this->walletService->getTransactionById($user, $id);

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail transaksi berhasil diambil',
                'data' => new WalletTransactionResource($transaction)
            ], 200);

        } catch (\Exception $e) {
            return $this->handleError($e, 'Gagal mengambil detail transaksi');
        }
    }

    /**
     * Proses permintaan top up.
     */
    public function topUp(TopUpRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $amount = $request->validated()['amount'];
            $paymentMethods = $request->validated()['payment_methods'] ?? [];

            $result = $this->walletService->createTopUpTransaction($user, $amount, $paymentMethods);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi top up berhasil dibuat. Silakan lanjutkan pembayaran.',
                'data' => [
                    'snap_token' => $result['snap_token'],
                    'snap_url' => $result['snap_url'],
                    'order_id' => $result['order_id']
                ]
            ], 201);

        } catch (ValidationException $e) {
            return $this->handleValidationError($e);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Gagal membuat transaksi top up');
        }
    }
    
    /**
     * Proses permintaan withdraw.
     */
    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $data = $request->validated();

            $transaction = $this->walletService->createWithdrawRequest($user, $data['amount'], [
                'bank_name' => $data['bank_name'],
                'account_name' => $data['account_name'],
                'account_number' => $data['account_number'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan penarikan berhasil dibuat dan sedang diproses.',
                'data' => new WalletTransactionResource($transaction)
            ], 201);

        } catch (ValidationException $e) {
            return $this->handleValidationError($e);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Gagal membuat permintaan penarikan');
        }
    }

    /**
     * Periksa status transaksi secara manual.
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $request->validate(['order_id' => 'required|string']);
        
        try {
            $orderId = $request->input('order_id');
            $user = $request->user();
            
            
            $transaction = $this->walletService->getTransactionByOrderId($orderId);
            if (!$transaction || $transaction->wallet->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
            }

            
            $updatedTransaction = $this->walletService->finishTransaction($orderId);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memeriksa status transaksi',
                'data' => new WalletTransactionResource($updatedTransaction)
            ], 200);

        } catch (\Exception $e) {
            return $this->handleError($e, 'Gagal memeriksa status transaksi');
        }
    }
    
    /**
     * Batalkan transaksi yang pending.
     */
    public function cancelTransaction(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $this->walletService->cancelTransaction($user, $id);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan.'
            ], 200);
            
        } catch (ValidationException $e) {
            return $this->handleValidationError($e);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Gagal membatalkan transaksi');
        }
    }

    /**
     * Endpoint untuk menerima notifikasi dari Midtrans.
     */
    public function midtransNotification(Request $request): JsonResponse
    {
        try {
            $result = $this->walletService->handleMidtransNotification($request->all());
            if ($result) {
                return response()->json(['success' => true, 'message' => 'Notification processed.']);
            }
            return response()->json(['success' => false, 'message' => 'Notification failed to process.'], 400);
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Internal server error.'], 500);
        }
    }

    /**
     * Helper untuk menangani error validasi.
     */
    private function handleValidationError(ValidationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Data tidak valid',
            'errors' => $e->errors()
        ], 422);
    }

    /**
     * Helper untuk menangani error umum.
     */
    private function handleError(\Exception $e, string $message): JsonResponse
    {
        Log::error("$message: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json([
            'success' => false,
            'message' => $message,
            
        ], 500);
    }
}