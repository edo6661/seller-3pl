<?php
namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use App\Requests\Wallet\TopUpRequest;
use App\Requests\Wallet\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Tampilkan halaman dompet
     */
    public function index(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateWallet($user);
        $transactions = $this->walletService->getTransactionHistory($user, 10);

        return view('seller.wallet.index', [
            'wallet' => $wallet,
            'transactions' => $transactions
        ]);
    }

    /**
     * Tampilkan halaman top up
     */
    public function topUp(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateWallet($user);
        
        return view('seller.wallet.topup', [
            'wallet' => $wallet
        ]);
    }

    /**
     * Proses top up - Return ke view dengan snap token
     */
    public function topUpSubmit(TopUpRequest $request)
    {
        try {
            $user = Auth::user();
            $amount = $request->amount;
            $paymentMethods = $request->payment_methods ?? [];

            $result = $this->walletService->createTopUpTransaction($user, $amount, $paymentMethods);

            return view('seller.wallet.topup-payment', [
                'snap_token' => $result['snap_token'],
                'snap_url' => $result['snap_url'],
                'transaction_id' => $result['transaction_id'],
                'order_id' => $result['order_id'],
                'amount' => $amount
            ]);

        } catch (ValidationException $e) {
            Log::warning('Top up validation error', [
                'user_id' => Auth::id(),
                'errors' => $e->errors()
            ]);
            
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Top up submission error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Terjadi kesalahan saat membuat transaksi top up. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Halaman finish setelah pembayaran - UPDATED dengan status update otomatis
     */
    public function topUpFinish(Request $request): View
    {
        $orderId = $request->order_id;
        $status = $request->transaction_status;
        $transaction = null;
        $errorMessage = null;

        try {
            if ($orderId) {
                
                $transaction = $this->walletService->finishTransaction($orderId);
                
                Log::info('Transaction finished and status updated', [
                    'order_id' => $orderId,
                    'final_status' => $transaction->status->value,
                    'user_id' => Auth::id()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error processing finish transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            
            try {
                $transaction = $this->walletService->getTransactionByOrderId($orderId);
            } catch (\Exception $ex) {
                $errorMessage = 'Tidak dapat menemukan transaksi';
            }
            
            if (!$errorMessage) {
                $errorMessage = 'Terjadi kesalahan saat memproses transaksi';
            }
        }

        return view('seller.wallet.topup-finish', [
            'order_id' => $orderId,
            'status' => $status,
            'transaction' => $transaction,
            'error_message' => $errorMessage
        ]);
    }

    /**
     * Check status transaksi - AJAX endpoint untuk cek status real-time
     */
    public function checkTransactionStatus(Request $request): JsonResponse
    {
        try {
            $orderId = $request->input('order_id');
            
            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order ID diperlukan'
                ], 400);
            }

            $user = Auth::user();
            $transaction = $this->walletService->getTransactionByOrderId($orderId);

            if (!$transaction || $transaction->wallet->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }

            
            if ($transaction->status->value === 'pending') {
                try {
                    $statusResponse = $this->walletService->checkTransactionStatus($orderId);
                    
                    if ($statusResponse['success']) {
                        $midtransStatus = $statusResponse['data']['transaction_status'];
                        $transaction = $this->walletService->updateTransactionStatus($orderId, $midtransStatus);
                    }
                } catch (\Exception $e) {
                    Log::warning('Error checking Midtrans status', [
                        'order_id' => $orderId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'status' => $transaction->status->value,
                    'amount' => $transaction->amount,
                    'description' => $transaction->description,
                    'created_at' => $transaction->created_at->format('d/m/Y H:i:s'),
                    'updated_at' => $transaction->updated_at->format('d/m/Y H:i:s'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Check transaction status error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'order_id' => $request->input('order_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengecek status transaksi'
            ], 500);
        }
    }

    /**
     * Tampilkan halaman withdraw
     */
    public function withdraw(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateWallet($user);

        return view('seller.wallet.withdraw', [
            'wallet' => $wallet
        ]);
    }

    /**
     * Proses withdraw
     */
    public function withdrawSubmit(WithdrawRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            $amount = $request->amount;
            $bankDetails = [
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number
            ];

            $transaction = $this->walletService->createWithdrawRequest($user, $amount, $bankDetails);

            return redirect()
                ->route('seller.wallet.index')
                ->with('success', 'Permintaan penarikan berhasil dibuat. Dana akan diproses dalam 1-3 hari kerja.');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Withdraw error: ' . $e->getMessage());
            return back()
                ->with('error', 'Terjadi kesalahan saat membuat permintaan penarikan.')
                ->withInput();
        }
    }

    /**
     * Tampilkan detail transaksi
     */
    public function transactionDetail(Request $request, int $id): View
    {   
        try {
            $user = Auth::user();
            $transaction = $this->walletService->getTransactionById($user, $id);

            if (!$transaction) {
                abort(404, 'Transaksi tidak ditemukan.');
            }

            return view('seller.wallet.transaction-detail', [
                'transaction' => $transaction,
                'order_id' => $transaction->reference_id,
            ]);

        } catch (\Exception $e) {
            abort(404, 'Transaksi tidak ditemukan.');
        }
    }

    /**
     * Batalkan transaksi pending
     */
    public function cancelTransaction(Request $request, int $id): RedirectResponse
    {
        try {
            $user = Auth::user();
            $this->walletService->cancelTransaction($user, $id);

            return redirect()
                ->route('seller.wallet.index')
                ->with('success', 'Transaksi berhasil dibatalkan.');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors());

        } catch (\Exception $e) {
            Log::error('Cancel transaction error: ' . $e->getMessage());
            return back()
                ->with('error', 'Gagal membatalkan transaksi.');
        }
    }

    /**
     * Handle Midtrans notification/webhook - Tetap JSON untuk webhook
     */
    public function midtransNotification(Request $request): JsonResponse
    {
        try {
            $notification = $request->all();
            
            Log::info('Midtrans notification received', $notification);

            $result = $this->walletService->handleMidtransNotification($notification);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification processed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process notification'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}