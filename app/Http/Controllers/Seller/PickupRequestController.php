<?php
namespace App\Http\Controllers\Seller;
use App\Http\Controllers\Controller;
use App\Requests\PickupRequest\SchedulePickupRequest;
use App\Requests\PickupRequest\StorePickupRequestRequest;
use App\Requests\PickupRequest\UpdatePickupRequestRequest;
use App\Services\PickupRequestService;
use App\Services\ProductService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class PickupRequestController extends Controller
{
    protected $pickupRequestService;
    protected $productService;
    protected $walletService;
    public function __construct(PickupRequestService $pickupRequestService, ProductService $productService, WalletService $walletService)
    {
        $this->pickupRequestService = $pickupRequestService;
        $this->productService = $productService;
        $this->walletService = $walletService;
    }
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->get('search');
        $status = $request->get('status');
        if ($search) {
            $pickupRequests = $this->pickupRequestService->searchPickupRequests($search, $userId);
        } elseif ($status) {
            $pickupRequests = $this->pickupRequestService->getPickupRequestsByStatus($userId, $status);
        } else {
            $pickupRequests = $this->pickupRequestService->getUserPickupRequests($userId);
        }
        $stats = $this->pickupRequestService->getPickupRequestStats($userId);
        $revenue = $this->pickupRequestService->getTotalRevenue($userId);
        return view('seller.pickup-request.index', compact('pickupRequests', 'stats', 'revenue', 'search', 'status'));
    }
    public function create()
    {
        $user = Auth::user();
        $products = $this->productService->getActiveProducts($user->id);
        $wallet = $this->walletService->getOrCreateWallet(
            $user
        );
        $addresses = $user->addresses()->get();
        return view('seller.pickup-request.create', compact('products', 'wallet','addresses'));
    }
    public function checkWalletBalance(Request $request)
    {
        try {
            $totalAmount = $request->input('total_amount');
            $wallet = $this->walletService->getOrCreateWallet(auth()->user());
            $hasSufficientBalance = $wallet->hasSufficientBalance($totalAmount);
            return response()->json([
                'success' => true,
                'has_sufficient_balance' => $hasSufficientBalance,
                'current_balance' => $wallet->available_balance,
                'formatted_balance' => $wallet->getFormattedAvailableBalanceAttribute(),
                'required_amount' => $totalAmount,
                'formatted_required_amount' => 'Rp ' . number_format($totalAmount, 0, ',', '.'),
                'message' => $hasSufficientBalance 
                    ? 'Saldo wallet mencukupi' 
                    : 'Saldo wallet tidak mencukupi untuk transaksi ini'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengecek saldo wallet'
            ], 500);
        }
    }
    public function store(StorePickupRequestRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $data['requested_at'] = now();
            
            $pickupRequest = $this->pickupRequestService->createPickupRequest($data);
            
            return redirect()
                ->route('seller.pickup-request.show', $pickupRequest)
                ->with('success', 'Pickup request berhasil dibuat!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    public function show($id)
    {
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== Auth::id()) {
            abort(404);
        }
        return view('seller.pickup-request.show', compact('pickupRequest'));
    }
    public function edit($id)
    {
        
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== Auth::id()) {
            abort(404);
        }
        if (!$pickupRequest->canBeCancelled()) {
            return back()->with('error', 'Pickup request ini tidak dapat diedit karena statusnya.');
        }
        $user = Auth::user(); 
        $products = $this->productService->getActiveProducts($user->id);
        $wallet = $this->walletService->getOrCreateWallet($user);
        $addresses = $user->addresses()->get();

        return view('seller.pickup-request.edit', compact('pickupRequest', 'products', 'wallet', 'addresses'));
    }
    public function update(UpdatePickupRequestRequest $request, $id)
    {
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== Auth::id()) {
            abort(404);
        }
        if (!$pickupRequest->canBeCancelled()) {
            return back()->with('error', 'Pickup request ini tidak dapat diedit');
        }
        try {
            $updatedPickupRequest = $this->pickupRequestService->updatePickupRequest($id, $request->validated());
            return redirect()
                ->route('seller.pickup-request.show', $updatedPickupRequest->id)
                ->with('success', 'Pickup request berhasil diupdate');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function cancel($id)
    {
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== Auth::id()) {
            abort(404);
        }
        try {
            $this->pickupRequestService->cancelPickupRequest($id);
            return back()->with('success', 'Pickup request berhasil dibatalkan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function confirm($id)
    {
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== Auth::id()) {
            abort(404);
        }
        try {
            $this->pickupRequestService->confirmPickupRequest($id);
            return back()->with('success', 'Pickup request berhasil dikonfirmasi');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function schedulePickup(SchedulePickupRequest $request, $id)
    {
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== Auth::id()) {
            abort(404);
        }
        try {
            $this->pickupRequestService->schedulePickup($id, $request->validated()['pickup_scheduled_at']);
            return back()->with('success', 'Jadwal pickup berhasil diatur');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function dashboard()
    {
        $userId = Auth::id();
        $stats = $this->pickupRequestService->getPickupRequestStats($userId);
        $revenue = $this->pickupRequestService->getTotalRevenue($userId);
        $monthlyStats = $this->pickupRequestService->getMonthlyStats($userId);
        $recentPickupRequests = $this->pickupRequestService->getUserPickupRequests($userId)->take(5);
        return view('seller.pickup-request.dashboard', compact('stats', 'revenue', 'monthlyStats', 'recentPickupRequests'));
    }
}