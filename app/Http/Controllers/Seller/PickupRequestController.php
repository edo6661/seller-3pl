<?php
namespace App\Http\Controllers\Seller;
use App\Http\Controllers\Controller;
use App\Requests\PickupRequest\SchedulePickupRequest;
use App\Requests\PickupRequest\StorePickupRequestRequest;
use App\Requests\PickupRequest\UpdatePickupRequestRequest;
use App\Services\PickupRequestService;
use App\Services\ProductService;
use App\Services\WalletService;
use App\Models\User;
use App\Events\PickupRequestCreated;
use App\Events\PickupRequestStatusUpdated;
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
    private function getSellerId()
    {
        $user = Auth::user();
        $membership = $user->memberOf()->first();
        if ($membership) {
            return $membership->seller_id;
        }
        if ($user->isSeller()) {
            return $user->id;
        }
        abort(403, 'Akses tidak diizinkan.');
    }
    private function getSeller(): User
    {
        $sellerId = $this->getSellerId();
        return User::findOrFail($sellerId);
    }
    public function index(Request $request)
    {
        $sellerId = $this->getSellerId(); 
        $search = $request->get('search');
        $status = $request->get('status');
        if ($search) {
            $pickupRequests = $this->pickupRequestService->searchPickupRequests($search, $sellerId);
        } elseif ($status) {
            $pickupRequests = $this->pickupRequestService->getPickupRequestsByStatus($sellerId, $status);
        } else {
            $pickupRequests = $this->pickupRequestService->getUserPickupRequests($sellerId);
        }
        $stats = $this->pickupRequestService->getPickupRequestStats($sellerId);
        $revenue = $this->pickupRequestService->getTotalRevenue($sellerId);
        return view('seller.pickup-request.index', compact('pickupRequests', 'stats', 'revenue', 'search', 'status'));
    }
    public function create()
    {
        $seller = $this->getSeller(); 
        $products = $this->productService->getActiveProducts($seller->id);
        $wallet = $this->walletService->getOrCreateWallet($seller);
        $addresses = $seller->addresses()->get();
        return view('seller.pickup-request.create', compact('products', 'wallet','addresses'));
    }
    public function checkWalletBalance(Request $request)
    {
        try {
            $totalAmount = $request->input('total_amount');
            $seller = $this->getSeller(); 
            $wallet = $this->walletService->getOrCreateWallet($seller);
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
            $data['user_id'] = $this->getSellerId();
            $data['seller'] = $this->getSeller();
            $data['requested_at'] = now();
            $pickupRequest = $this->pickupRequestService->createPickupRequest($data);
            event(new PickupRequestCreated($pickupRequest, $data['seller']));
            return redirect()
                ->route('seller.pickup-request.show', $pickupRequest)
                ->with('success', 'Pickup request berhasil dibuat! Admin akan segera memproses permintaan Anda.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    public function show($id)
    {
        $sellerId = $this->getSellerId(); 
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== $sellerId) {
            abort(404);
        }
        return view('seller.pickup-request.show', compact('pickupRequest'));
    }
    public function edit($id)
    {
        $sellerId = $this->getSellerId(); 
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== $sellerId) {
            abort(404);
        }
        if (!$pickupRequest->canBeCancelled()) {
            return back()->with('error', 'Pickup request ini tidak dapat diedit karena statusnya.');
        }
        $seller = $this->getSeller(); 
        $products = $this->productService->getActiveProducts($seller->id);
        $wallet = $this->walletService->getOrCreateWallet($seller);
        $addresses = $seller->addresses()->get();
        return view('seller.pickup-request.edit', compact('pickupRequest', 'products', 'wallet', 'addresses'));
    }
    public function update(UpdatePickupRequestRequest $request, $id)
    {
        $sellerId = $this->getSellerId(); 
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== $sellerId) {
            abort(404);
        }
        if (!$pickupRequest->canBeCancelled()) {
            return back()->with('error', 'Pickup request ini tidak dapat diedit');
        }
        try {
            $oldStatus = $pickupRequest->status;
            $updatedPickupRequest = $this->pickupRequestService->updatePickupRequest($id, $request->validated());
            if ($oldStatus !== $updatedPickupRequest->status) {
                event(new PickupRequestStatusUpdated($updatedPickupRequest, $oldStatus));
            }
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
        $sellerId = $this->getSellerId(); 
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== $sellerId) {
            abort(404);
        }
        try {
            $oldStatus = $pickupRequest->status;
            $cancelledRequest = $this->pickupRequestService->cancelPickupRequest($id);
            event(new PickupRequestStatusUpdated($cancelledRequest, $oldStatus));
            return back()->with('success', 'Pickup request berhasil dibatalkan dan notifikasi telah dikirim');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function confirm($id)
    {
        $sellerId = $this->getSellerId(); 
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== $sellerId) {
            abort(404);
        }
        try {
            $oldStatus = $pickupRequest->status;
            $confirmedRequest = $this->pickupRequestService->confirmPickupRequest($id);
            event(new PickupRequestStatusUpdated($confirmedRequest, $oldStatus));
            return back()->with('success', 'Pickup request berhasil dikonfirmasi dan notifikasi telah dikirim');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function schedulePickup(SchedulePickupRequest $request, $id)
    {
        $sellerId = $this->getSellerId(); 
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== $sellerId) {
            abort(404);
        }
        try {
            $oldStatus = $pickupRequest->status;
            $scheduledRequest = $this->pickupRequestService->schedulePickup($id, $request->validated()['pickup_scheduled_at']);
            event(new PickupRequestStatusUpdated($scheduledRequest, $oldStatus));
            return back()->with('success', 'Jadwal pickup berhasil diatur dan notifikasi telah dikirim');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function dashboard()
    {
        $sellerId = $this->getSellerId(); 
        $stats = $this->pickupRequestService->getPickupRequestStats($sellerId);
        $revenue = $this->pickupRequestService->getTotalRevenue($sellerId);
        $monthlyStats = $this->pickupRequestService->getMonthlyStats($sellerId);
        $recentPickupRequests = $this->pickupRequestService->getUserPickupRequests($sellerId)->take(5);
        return view('seller.pickup-request.dashboard', compact('stats', 'revenue', 'monthlyStats', 'recentPickupRequests'));
    }
    public function startDelivery($id)
    {
        $sellerId = $this->getSellerId(); 
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== $sellerId) {
            abort(404);
        }
        if (!$pickupRequest->isDropOffType() || $pickupRequest->status !== 'confirmed') {
            return back()->with('error', 'Request ini tidak dapat dimulai pengirimannya');
        }
        try {
            $oldStatus = $pickupRequest->status;
            $inTransitRequest = $this->pickupRequestService->markAsInTransit($id);
            event(new PickupRequestStatusUpdated($inTransitRequest, $oldStatus));
            return back()->with('success', 'Pengiriman berhasil dimulai dan notifikasi telah dikirim');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function createTicketFromPickup($id)
    {
        $sellerId = $this->getSellerId(); 
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
        if (!$pickupRequest || $pickupRequest->user_id !== $sellerId) {
            abort(404);
        }
        $ticketData = [
            'pickup_code' => $pickupRequest->pickup_code,
            'tracking_number' => $pickupRequest->courier_tracking_number,
            'pickup_request' => $pickupRequest
        ];
        $seller = $this->getSeller();
        return view('seller.support.create', compact('ticketData'));
    }
}