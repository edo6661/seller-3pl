<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\ManualWalletService;
use App\Models\ManualTopUpRequest;
use App\Models\WithdrawRequest;
use App\Models\BankAccount;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
class AdminManualWalletController extends Controller
{
    protected ManualWalletService $manualWalletService;
    protected WalletService $walletService;
    public function __construct(ManualWalletService $manualWalletService, WalletService $walletService)
    {
        $this->manualWalletService = $manualWalletService;
        $this->walletService = $walletService;
    }
    /**
     * Admin dashboard manual wallet
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status', 'waiting_approval');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $perPage = $request->get('per_page', 15);
        $topUpQuery = ManualTopUpRequest::with(['user'])
            ->select('manual_top_up_requests.*')
            ->join('users', 'manual_top_up_requests.user_id', '=', 'users.id');
        if ($search) {
            $topUpQuery->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('manual_top_up_requests.request_code', 'like', "%{$search}%");
            });
        }
        if ($status !== 'all') {
            $topUpQuery->where('manual_top_up_requests.status', $status);
        }
        if ($dateFrom) {
            $topUpQuery->whereDate('manual_top_up_requests.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $topUpQuery->whereDate('manual_top_up_requests.created_at', '<=', $dateTo);
        }
        $topUpRequests = $topUpQuery->orderBy('manual_top_up_requests.created_at', 'desc')
                                   ->paginate($perPage, ['*'], 'topup_page');
        $withdrawQuery = WithdrawRequest::with(['user'])
            ->select('withdraw_requests.*')
            ->join('users', 'withdraw_requests.user_id', '=', 'users.id');
        if ($search) {
            $withdrawQuery->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('withdraw_requests.withdrawal_code', 'like', "%{$search}%");
            });
        }
        $withdrawQuery->where('withdraw_requests.status', 'pending');
        if ($dateFrom) {
            $withdrawQuery->whereDate('withdraw_requests.created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $withdrawQuery->whereDate('withdraw_requests.created_at', '<=', $dateTo);
        }
        $withdrawRequests = $withdrawQuery->orderBy('withdraw_requests.created_at', 'desc')
                                         ->paginate($perPage, ['*'], 'withdraw_page');
        $stats = [
            'pending_topup' => ManualTopUpRequest::where('status', 'waiting_approval')->count(),
            'pending_topup_amount' => ManualTopUpRequest::where('status', 'waiting_approval')->sum('amount'),
            'pending_withdraw' => WithdrawRequest::where('status', 'pending')->count(),
            'pending_withdraw_amount' => WithdrawRequest::where('status', 'pending')->sum('amount'),
            'today_approved_topup' => ManualTopUpRequest::where('status', 'approved')
                ->whereDate('approved_at', today())->count(),
            'today_completed_withdraw' => WithdrawRequest::where('status', 'completed')
                ->whereDate('completed_at', today())->count(),
        ];
        return view('admin.wallet.manual-index', compact(
            'topUpRequests',
            'withdrawRequests', 
            'stats',
            'search',
            'status',
            'dateFrom',
            'dateTo',
            'perPage'
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
            $topUpRequest = ManualTopUpRequest::findOrFail($id);
            $this->manualWalletService->approveTopUp($topUpRequest, $request->admin_notes);
            return back()->with('success', 'Top up request berhasil disetujui.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
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
            $topUpRequest = ManualTopUpRequest::findOrFail($id);
            $this->manualWalletService->rejectTopUp($topUpRequest, $request->admin_notes);
            return back()->with('success', 'Top up request berhasil ditolak.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
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
            'status' => 'required|in:processing,completed,failed',
            'admin_notes' => 'nullable|string|max:500'
        ]);
        try {
            $withdrawRequest = WithdrawRequest::findOrFail($id);
            $this->manualWalletService->processManualWithdraw(
                $withdrawRequest, 
                $request->status, 
                $request->admin_notes
            );
            $statusText = match($request->status) {
                'processing' => 'diproses',
                'completed' => 'diselesaikan',
                'failed' => 'ditolak',
            };
            return back()->with('success', "Permintaan penarikan berhasil {$statusText}.");
        } catch (\Exception $e) {
            Log::error('Process withdraw error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses permintaan penarikan.');
        }
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
    /**
     * View top up request detail
     */
    public function topUpDetail(int $id): View
    {
        $topUpRequest = ManualTopUpRequest::with(['user'])->findOrFail($id);
        return view('admin.wallet.topup-detail', [
            'topUpRequest' => $topUpRequest
        ]);
    }
    /**
     * View withdraw request detail  
     */
    public function withdrawDetail(int $id): View
    {
        $withdrawRequest = WithdrawRequest::with(['user'])->findOrFail($id);
        return view('admin.wallet.withdraw-detail', [
            'withdrawRequest' => $withdrawRequest
        ]);
    }
    /**
     * Manual withdraw (sama seperti sebelumnya tapi menggunakan ManualWalletService)
     */
    public function manualWithdraw(): View
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateWallet($user);
        return view('seller.wallet.manual-withdraw', [
            'wallet' => $wallet
        ]);
    }
    /**
     * Proses manual withdraw
     */
    public function manualWithdrawSubmit(WithdrawRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            $amount = $request->amount;
            $bankDetails = [
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number
            ];
            if ($request->use_passbook_data && $user->sellerProfile && $user->sellerProfile->passbook_image_path) {
            }
            $withdrawRequest = $this->manualWalletService->createManualWithdrawRequest($user, $amount, $bankDetails);
            return redirect()
                ->route('seller.wallet.index')
                ->with('success', 'Permintaan penarikan berhasil dibuat. Dana akan diproses dalam 1-3 hari kerja.');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Manual withdraw error: ' . $e->getMessage());
            return back()
                ->with('error', 'Terjadi kesalahan saat membuat permintaan penarikan.')
                ->withInput();
        }
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
        } catch (\Exception $e) {
            Log::error('Cancel top up request error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membatalkan permintaan top up.');
        }
    }
}