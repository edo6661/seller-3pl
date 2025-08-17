<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ManualTopUpStatus;
use App\Http\Controllers\Controller;
use App\Requests\Wallet\ManualTopUpRequest;
use App\Requests\Wallet\PaymentProofUploadRequest;
use App\Requests\Wallet\ManualWithdrawRequest;
use App\Services\ManualWalletService;
use App\Services\WalletService; 
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ManualWalletController extends Controller
{
    protected ManualWalletService $manualWalletService;
    protected WalletService $walletService; 

    public function __construct(ManualWalletService $manualWalletService, WalletService $walletService)
    {
        $this->manualWalletService = $manualWalletService;
        $this->walletService = $walletService;
    }

    /**
     * Tampilkan halaman manual top up
     */
    public function manualTopUp(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateWallet($user);
        $topUpRequests = $this->manualWalletService->getUserTopUpRequests($user, 5);
        
        // Ganti variable name dan method
        $resumableRequests = $this->manualWalletService->getResumableTopUpRequests($user);
        
        return view('seller.wallet.manual-topup', [
            'wallet' => $wallet,
            'topUpRequests' => $topUpRequests,
            'resumableRequests' => $resumableRequests // Ganti nama variable
        ]);
    }


    public function manualTopUpSubmit(ManualTopUpRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            
            // Cek apakah ada permintaan yang belum selesai
            $existingRequests = $this->manualWalletService->getResumableTopUpRequests($user);
            
            if ($existingRequests->count() > 0) {
                return redirect()
                    ->route('seller.wallet.manual-topup')
                    ->with('warning', 'Anda memiliki ' . $existingRequests->count() . ' permintaan top up yang belum diselesaikan. Silakan selesaikan atau batalkan terlebih dahulu.');
            }
            
            $amount = $request->amount;
            $topUpRequest = $this->manualWalletService->createManualTopUpRequest($user, $amount);

            return redirect()
                ->route('seller.wallet.manual-topup.payment', $topUpRequest->request_code)
                ->with('success', 'Permintaan top up berhasil dibuat. Silakan pilih metode pembayaran.');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Manual top up request error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->with('error', 'Terjadi kesalahan saat membuat permintaan top up.')
                ->withInput();
        }
    }

    /**
     * Halaman pilih bank untuk pembayaran
     */
    public function manualTopUpPayment(string $requestCode): View
    {
        $user = Auth::user();
        $topUpRequest = $this->manualWalletService->getTopUpRequestByCode($requestCode);

        if (!$topUpRequest || $topUpRequest->user_id !== $user->id) {
            abort(404, 'Permintaan top up tidak ditemukan.');
        }

        $bankAccounts = $this->manualWalletService->getActiveBankAccounts();

        return view('seller.wallet.manual-topup-payment', [
            'topUpRequest' => $topUpRequest,
            'bankAccounts' => $bankAccounts
        ]);
    }

    /**
     * Set bank account untuk pembayaran
     */
    public function setTopUpBank(Request $request, string $requestCode): RedirectResponse
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id'
        ]);

        try {
            $user = Auth::user();
            $topUpRequest = $this->manualWalletService->getTopUpRequestByCode($requestCode);

            if (!$topUpRequest || $topUpRequest->user_id !== $user->id) {
                abort(404, 'Permintaan top up tidak ditemukan.');
            }

            $topUpRequest = $this->manualWalletService->setTopUpToWaitingPayment(
                $topUpRequest, 
                $request->bank_account_id
            );

            return redirect()
                ->route('seller.wallet.manual-topup.upload', $requestCode)
                ->with('success', 'Silakan lakukan pembayaran dan upload bukti transfer.');

        } catch (\Exception $e) {
            Log::error('Set top up bank error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses permintaan.');
        }
    }

    /**
     * Halaman upload bukti pembayaran
     */
    public function manualTopUpUpload(string $requestCode): View
    {
        $user = Auth::user();
        $topUpRequest = $this->manualWalletService->getTopUpRequestByCode($requestCode);

        if (!$topUpRequest || $topUpRequest->user_id !== $user->id) {
            abort(404, 'Permintaan top up tidak ditemukan.');
        }

        return view('seller.wallet.manual-topup-upload', [
            'topUpRequest' => $topUpRequest
        ]);
    }

    /**
     * Upload bukti pembayaran
     */
    public function uploadPaymentProof(PaymentProofUploadRequest $request, string $requestCode): RedirectResponse
    {
        try {
            $user = Auth::user();
            $topUpRequest = $this->manualWalletService->getTopUpRequestByCode($requestCode);

            if (!$topUpRequest || $topUpRequest->user_id !== $user->id) {
                abort(404, 'Permintaan top up tidak ditemukan.');
            }

            $this->manualWalletService->uploadPaymentProof(
                $topUpRequest,
                $request->file('payment_proof')
            );

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
     * Detail top up request
     */
    public function topUpRequestDetail(string $requestCode): View
    {
        $user = Auth::user();
        $topUpRequest = $this->manualWalletService->getTopUpRequestByCode($requestCode);

        if (!$topUpRequest || $topUpRequest->user_id !== $user->id) {
            abort(404, 'Permintaan top up tidak ditemukan.');
        }

        return view('seller.wallet.manual-topup-detail', [
            'topUpRequest' => $topUpRequest
        ]);
    }

    /**
     * Cancel top up request
     */
    public function cancelTopUpRequest(string $requestCode): RedirectResponse
    {
        try {
            $user = Auth::user();
            $topUpRequest = $this->manualWalletService->getTopUpRequestByCode($requestCode);

            if (!$topUpRequest || $topUpRequest->user_id !== $user->id) {
                abort(404, 'Permintaan top up tidak ditemukan.');
            }

            $this->manualWalletService->cancelTopUpRequest($topUpRequest);

            return redirect()
                ->route('seller.wallet.index')
                ->with('success', 'Permintaan top up berhasil dibatalkan.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Cancel top up request error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membatalkan permintaan top up.');
        }
    }

    /**
     * Tampilkan halaman manual withdraw
     */
    public function manualWithdraw(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateWallet($user);
        $withdrawRequests = $this->walletService->getWithdrawRequests($user, 5);
        
        return view('seller.wallet.manual-withdraw', [
            'wallet' => $wallet,
            'withdrawRequests' => $withdrawRequests
        ]);
    }

    /**
     * Proses request manual withdraw
     */
    public function manualWithdrawSubmit(ManualWithdrawRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            
            $bankDetails = [
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
            ];

            $withdrawRequest = $this->manualWalletService->createManualWithdrawRequest(
                $user, 
                $request->amount, 
                $bankDetails
            );

            return redirect()
                ->route('seller.wallet.index')
                ->with('success', 'Permintaan penarikan berhasil dibuat. Menunggu proses admin.');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Manual withdraw request error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->with('error', 'Terjadi kesalahan saat membuat permintaan penarikan.')
                ->withInput();
        }
    }
    
    // Tambahkan method baru untuk resume
    public function resumeTopUpProcess(string $requestCode): RedirectResponse
    {
        $user = Auth::user();
        $topUpRequest = $this->manualWalletService->getTopUpRequestByCode($requestCode);

        if (!$topUpRequest || $topUpRequest->user_id !== $user->id) {
            abort(404, 'Permintaan top up tidak ditemukan.');
        }

        // Redirect berdasarkan status
        if ($topUpRequest->status === ManualTopUpStatus::PENDING) {
            return redirect()->route('seller.wallet.manual-topup.payment', $requestCode);
        } elseif ($topUpRequest->status === ManualTopUpStatus::WAITING_PAYMENT) {
            return redirect()->route('seller.wallet.manual-topup.upload', $requestCode);
        }

        return redirect()->route('seller.wallet.manual-topup')
            ->with('error', 'Permintaan ini tidak dapat dilanjutkan.');
    }
}