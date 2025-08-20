<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use App\Requests\Wallet\TopUpRequest;
use App\Requests\Wallet\WithdrawRequest;
use App\Requests\Wallet\PaymentProofUploadRequest;
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

    // TAMBAHKAN HELPER METHOD INI SEPERTI DI AddressController
    private function getSellerId()
    {
        $user = auth()->user();
        $membership = $user->memberOf()->first();
        if ($membership) {
            return $membership->seller_id;
        }
        if ($user->isSeller()) {
            return $user->id;
        }
        abort(403, 'Akses tidak diizinkan.');
    }

    private function getSeller(): \App\Models\User
    {
        $sellerId = $this->getSellerId();
        return \App\Models\User::findOrFail($sellerId);
    }

    /**
     * Tampilkan halaman dompet
     */
    public function index(): View
    {
        // Gunakan seller yang benar
        $seller = $this->getSeller();
        $wallet = $this->walletService->getOrCreateWallet($seller);
        $transactions = $this->walletService->getTransactionHistory($seller, 15);
        
        // Dapatkan status permintaan pending
        $pendingRequests = $this->walletService->hasPendingRequests($seller);

        return view('seller.wallet.index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'pendingRequests' => $pendingRequests
        ]);
    }

    /**
     * Tampilkan halaman top up
     */
    public function topUp(): View
    {
        $seller = $this->getSeller();
        $wallet = $this->walletService->getOrCreateWallet($seller);
        $topUpRequests = $this->walletService->getTopUpRequests($seller, 5);
        $resumableRequests = $this->walletService->getResumableTopUpRequests($seller);
        
        return view('seller.wallet.topup', [
            'wallet' => $wallet,
            'topUpRequests' => $topUpRequests,
            'resumableRequests' => $resumableRequests
        ]);
    }

    /**
     * Proses top up - Buat transaksi baru
     */
    public function topUpSubmit(TopUpRequest $request): RedirectResponse
    {
        try {
            $seller = $this->getSeller();
            
            // Cek apakah ada permintaan yang belum selesai
            $existingRequests = $this->walletService->getResumableTopUpRequests($seller);
            
            if ($existingRequests->count() > 0) {
                return redirect()
                    ->route('seller.wallet.topup')
                    ->with('warning', 'Anda memiliki ' . $existingRequests->count() . ' permintaan top up yang belum diselesaikan.');
            }
            
            $amount = $request->amount;
            $transaction = $this->walletService->createTopUpTransaction($seller, $amount);

            return redirect()
                ->route('seller.wallet.topup.payment', $transaction->reference_id)
                ->with('success', 'Permintaan top up berhasil dibuat. Silakan pilih metode pembayaran.');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Top up submission error', [
                'user_id' => $this->getSellerId(),
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->with('error', 'Terjadi kesalahan saat membuat transaksi top up.')
                ->withInput();
        }
    }

    /**
     * Halaman pilih bank untuk pembayaran
     */
    public function topUpPayment(string $referenceId): View
    {
        $seller = $this->getSeller();
        $transaction = $this->walletService->getTransactionByReferenceId($referenceId);

        if (!$transaction || $transaction->wallet->user_id !== $seller->id) {
            abort(404, 'Transaksi top up tidak ditemukan.');
        }

        $bankAccounts = $this->walletService->getActiveBankAccounts();

        return view('seller.wallet.topup-payment', [
            'transaction' => $transaction,
            'bankAccounts' => $bankAccounts
        ]);
    }

    /**
     * Set bank account untuk pembayaran
     */
    public function setTopUpBank(Request $request, string $referenceId): RedirectResponse
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id'
        ]);

        try {
            $seller = $this->getSeller();
            $transaction = $this->walletService->getTransactionByReferenceId($referenceId);

            if (!$transaction || $transaction->wallet->user_id !== $seller->id) {
                abort(404, 'Transaksi top up tidak ditemukan.');
            }

            $this->walletService->setTopUpToWaitingPayment($transaction, $request->bank_account_id);

            return redirect()
                ->route('seller.wallet.topup.upload', $referenceId)
                ->with('success', 'Silakan lakukan pembayaran dan upload bukti transfer.');

        } catch (\Exception $e) {
            Log::error('Set top up bank error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses permintaan.');
        }
    }

    /**
     * Halaman upload bukti pembayaran
     */
    public function topUpUpload(string $referenceId): View
    {
        $seller = $this->getSeller();
        $transaction = $this->walletService->getTransactionByReferenceId($referenceId);

        if (!$transaction || $transaction->wallet->user_id !== $seller->id) {
            abort(404, 'Transaksi top up tidak ditemukan.');
        }

        return view('seller.wallet.topup-upload', [
            'transaction' => $transaction
        ]);
    }

    /**
     * Upload bukti pembayaran
     */
    public function uploadPaymentProof(PaymentProofUploadRequest $request, string $referenceId): RedirectResponse
    {
        try {
            $seller = $this->getSeller();
            $transaction = $this->walletService->getTransactionByReferenceId($referenceId);

            if (!$transaction || $transaction->wallet->user_id !== $seller->id) {
                abort(404, 'Transaksi top up tidak ditemukan.');
            }

            $this->walletService->uploadPaymentProof($transaction, $request->file('payment_proof'));

            return redirect()
                ->route('seller.wallet.index')
                ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());

        } catch (\Exception $e) {
            Log::error('Upload payment proof error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengupload bukti pembayaran.');
        }
    }

    /**
     * Resume top up process
     */
    public function resumeTopUpProcess(string $referenceId): RedirectResponse
    {
        $seller = $this->getSeller();
        $transaction = $this->walletService->getTransactionByReferenceId($referenceId);

        if (!$transaction || $transaction->wallet->user_id !== $seller->id) {
            abort(404, 'Transaksi top up tidak ditemukan.');
        }

        // Redirect berdasarkan status
        if (!$transaction->bank_name) {
            return redirect()->route('seller.wallet.topup.payment', $referenceId);
        } elseif (!$transaction->payment_proof_path) {
            return redirect()->route('seller.wallet.topup.upload', $referenceId);
        }

        return redirect()->route('seller.wallet.topup')
            ->with('error', 'Transaksi ini tidak dapat dilanjutkan.');
    }

    /**
     * Tampilkan halaman withdraw
     */
    public function withdraw(): View
    {
        $seller = $this->getSeller();
        $wallet = $this->walletService->getOrCreateWallet($seller);
        $withdrawRequests = $this->walletService->getWithdrawRequests($seller, 5);

        return view('seller.wallet.withdraw', [
            'wallet' => $wallet,
            'withdrawRequests' => $withdrawRequests
        ]);
    }

    /**
     * Proses withdraw
     */
    public function withdrawSubmit(WithdrawRequest $request): RedirectResponse
    {
        try {
            $seller = $this->getSeller();
            $amount = $request->amount;
            $bankDetails = [
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number
            ];

            $this->walletService->createWithdrawRequest($seller, $amount, $bankDetails);

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
            $seller = $this->getSeller();
            $transaction = $this->walletService->getTransactionById($seller, $id);

            if (!$transaction) {
                abort(404, 'Transaksi tidak ditemukan.');
            }

            return view('seller.wallet.transaction-detail', [
                'transaction' => $transaction,
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
            $seller = $this->getSeller();
            $this->walletService->cancelTransaction($seller, $id);

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
     * Cancel top up request
     */
    public function cancelTopUpRequest(string $referenceId): RedirectResponse
    {
        try {
            $seller = $this->getSeller();
            $transaction = $this->walletService->getTransactionByReferenceId($referenceId);

            if (!$transaction || $transaction->wallet->user_id !== $seller->id) {
                abort(404, 'Transaksi top up tidak ditemukan.');
            }

            $this->walletService->cancelTransaction($seller, $transaction->id);

            return redirect()
                ->route('seller.wallet.index')
                ->with('success', 'Permintaan top up berhasil dibatalkan.');

        } catch (\Exception $e) {
            Log::error('Cancel top up request error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membatalkan permintaan top up.');
        }
    }
}