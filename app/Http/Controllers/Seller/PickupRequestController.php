<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\PickupRequestService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PickupRequestController extends Controller
{
    protected $pickupRequestService;
    protected $productService;

    public function __construct(PickupRequestService $pickupRequestService, ProductService $productService)
    {
        $this->pickupRequestService = $pickupRequestService;
        $this->productService = $productService;
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
        $userId = Auth::id();
        $products = $this->productService->getActiveProducts($userId);

        return view('seller.pickup-request.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'recipient_city' => 'required|string|max:100',
            'recipient_province' => 'required|string|max:100',
            'recipient_postal_code' => 'required|string|max:10',
            'recipient_address' => 'required|string|max:500',
            'pickup_name' => 'required|string|max:255',
            'pickup_phone' => 'required|string|max:20',
            'pickup_city' => 'required|string|max:100',
            'pickup_province' => 'required|string|max:100',
            'pickup_postal_code' => 'required|string|max:10',
            'pickup_address' => 'required|string|max:500',
            'pickup_scheduled_at' => 'nullable|date|after:now',
            'payment_method' => ['required', Rule::in(['balance', 'wallet', 'cod'])],
            'shipping_cost' => 'required|numeric|min:0',
            'service_fee' => 'nullable|numeric|min:0',
            'courier_service' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();

            $pickupRequest = $this->pickupRequestService->createPickupRequest($data);

            return redirect()
                ->route('seller.pickup-request.show', $pickupRequest->id)
                ->with('success', 'Pickup request berhasil dibuat dengan kode: ' . $pickupRequest->pickup_code);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            return back()->with('error', 'Pickup request ini tidak dapat diedit');
        }

        $userId = Auth::id();
        $products = $this->productService->getActiveProducts($userId);

        return view('seller.pickup-request.edit', compact('pickupRequest', 'products'));
    }

    public function update(Request $request, $id)
    {
        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);

        if (!$pickupRequest || $pickupRequest->user_id !== Auth::id()) {
            abort(404);
        }

        if (!$pickupRequest->canBeCancelled()) {
            return back()->with('error', 'Pickup request ini tidak dapat diedit');
        }

        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'recipient_city' => 'required|string|max:100',
            'recipient_province' => 'required|string|max:100',
            'recipient_postal_code' => 'required|string|max:10',
            'recipient_address' => 'required|string|max:500',
            'pickup_name' => 'required|string|max:255',
            'pickup_phone' => 'required|string|max:20',
            'pickup_city' => 'required|string|max:100',
            'pickup_province' => 'required|string|max:100',
            'pickup_postal_code' => 'required|string|max:10',
            'pickup_address' => 'required|string|max:500',
            'pickup_scheduled_at' => 'nullable|date|after:now',
            'payment_method' => ['required', Rule::in(['balance', 'wallet', 'cod'])],
            'shipping_cost' => 'required|numeric|min:0',
            'service_fee' => 'nullable|numeric|min:0',
            'courier_service' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $updatedPickupRequest = $this->pickupRequestService->updatePickupRequest($id, $request->all());

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

    public function schedulePickup(Request $request, $id)
    {
        $request->validate([
            'pickup_scheduled_at' => 'required|date|after:now'
        ]);

        $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);

        if (!$pickupRequest || $pickupRequest->user_id !== Auth::id()) {
            abort(404);
        }

        try {
            $this->pickupRequestService->schedulePickup($id, $request->pickup_scheduled_at);

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