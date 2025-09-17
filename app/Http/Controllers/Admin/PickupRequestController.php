<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PickupRequest;
use App\Services\PickupRequestService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class PickupRequestController extends Controller
{
    protected $pickupRequestService;
    public function __construct(PickupRequestService $pickupRequestService)
    {
        $this->pickupRequestService = $pickupRequestService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PickupRequest::with(['user', 'items.product']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pickup_code', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_phone', 'like', "%{$search}%")
                  ->orWhere('pickup_name', 'like', "%{$search}%")
                  ->orWhere('pickup_phone', 'like', "%{$search}%")
                  ->orWhere('courier_tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        $query->orderBy('created_at', 'desc');
        $pickupRequests = $query->paginate(20)->withQueryString();
        $stats = [
            'total' => PickupRequest::count(),
            'pending' => PickupRequest::where('status', 'pending')->count(),
            'confirmed' => PickupRequest::where('status', 'confirmed')->count(),
            'pickup_scheduled' => PickupRequest::where('status', 'pickup_scheduled')->count(),
            'picked_up' => PickupRequest::where('status', 'picked_up')->count(),
            'in_transit' => PickupRequest::where('status', 'in_transit')->count(),
            'delivered' => PickupRequest::where('status', 'delivered')->count(),
            'failed' => PickupRequest::where('status', 'failed')->count(),
            'cancelled' => PickupRequest::where('status', 'cancelled')->count(),
        ];
        $revenue = [
            'total_revenue' => PickupRequest::where('status', 'delivered')->sum('product_total'),
            'total_shipping' => PickupRequest::where('status', 'delivered')->sum('shipping_cost'),
            'total_service_fee' => PickupRequest::where('status', 'delivered')->sum('service_fee'),
            'total_amount' => PickupRequest::where('status', 'delivered')->sum('total_amount'),
        ];
        return view('admin.pickup_request.index', compact(
            'pickupRequests',
            'stats',
            'revenue',
            'request'
        ));
    }
    /**
     * Update status pickup request menjadi confirmed
     */
    public function confirm(Request $request, $id): JsonResponse
    {
        try {
            $pickupRequest = $this->pickupRequestService->confirmPickupRequest($id);
            return response()->json([
                'success' => true,
                'message' => 'Pickup request berhasil dikonfirmasi',
                'data' => [
                    'id' => $pickupRequest->id,
                    'status' => $pickupRequest->status,
                    'pickup_code' => $pickupRequest->pickup_code
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    /**
     * Schedule pickup untuk pickup request
     */
    public function schedulePickup(Request $request, $id): JsonResponse
    {
        $request->validate([
            'pickup_scheduled_at' => 'required|date|after:now'
        ], [
            'pickup_scheduled_at.required' => 'Tanggal dan waktu pickup harus diisi',
            'pickup_scheduled_at.date' => 'Format tanggal tidak valid',
            'pickup_scheduled_at.after' => 'Waktu pickup harus di masa depan'
        ]);
        try {
            $pickupRequest = $this->pickupRequestService->schedulePickup($id, $request->pickup_scheduled_at);
            return response()->json([
                'success' => true,
                'message' => 'Pickup berhasil dijadwalkan',
                'data' => [
                    'id' => $pickupRequest->id,
                    'status' => $pickupRequest->status,
                    'pickup_code' => $pickupRequest->pickup_code,
                    'pickup_scheduled_at' => $pickupRequest->pickup_scheduled_at->format('d M Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    /**
     * Mark pickup request sebagai picked up
     */
    public function markAsPickedUp(Request $request, $id): JsonResponse
    {
        $request->validate([
            'courier_tracking_number' => 'nullable|string|max:255',
            'courier_response' => 'nullable|array'
        ]);
        try {
            $courierData = [];
            if ($request->filled('courier_tracking_number')) {
                $courierData['tracking_number'] = $request->courier_tracking_number;
            }
            if ($request->filled('courier_response')) {
                $courierData['response'] = $request->courier_response;
            }
            $pickupRequest = $this->pickupRequestService->markAsPickedUp($id, $courierData);
            return response()->json([
                'success' => true,
                'message' => 'Pickup request berhasil ditandai sebagai diambil',
                'data' => [
                    'id' => $pickupRequest->id,
                    'status' => $pickupRequest->status,
                    'pickup_code' => $pickupRequest->pickup_code,
                    'picked_up_at' => $pickupRequest->picked_up_at->format('d M Y H:i'),
                    'courier_tracking_number' => $pickupRequest->courier_tracking_number
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    /**
     * Mark pickup request sebagai in transit
     */
    public function markAsInTransit(Request $request, $id): JsonResponse
    {
        try {
            $pickupRequest = $this->pickupRequestService->markAsInTransit($id);
            return response()->json([
                'success' => true,
                'message' => 'Pickup request berhasil ditandai sedang dalam perjalanan',
                'data' => [
                    'id' => $pickupRequest->id,
                    'status' => $pickupRequest->status,
                    'pickup_code' => $pickupRequest->pickup_code
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    /**
     * Mark pickup request sebagai delivered
     */
    public function markAsDelivered(Request $request, $id): JsonResponse
    {
        try {
            $pickupRequest = $this->pickupRequestService->markAsDelivered($id);
            return response()->json([
                'success' => true,
                'message' => 'Pickup request berhasil ditandai sebagai terkirim',
                'data' => [
                    'id' => $pickupRequest->id,
                    'status' => $pickupRequest->status,
                    'pickup_code' => $pickupRequest->pickup_code,
                    'delivered_at' => $pickupRequest->delivered_at->format('d M Y H:i'),
                    'cod_collected_at' => $pickupRequest->cod_collected_at ? $pickupRequest->cod_collected_at->format('d M Y H:i') : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    /**
     * Mark pickup request sebagai failed
     */
    public function markAsFailed(Request $request, $id): JsonResponse
    {
        $request->validate([
            'failure_reason' => 'nullable|string|max:500'
        ]);
        try {
            $pickupRequest = $this->pickupRequestService->markAsFailed($id, $request->failure_reason);
            return response()->json([
                'success' => true,
                'message' => 'Pickup request berhasil ditandai sebagai gagal',
                'data' => [
                    'id' => $pickupRequest->id,
                    'status' => $pickupRequest->status,
                    'pickup_code' => $pickupRequest->pickup_code,
                    'failure_reason' => $pickupRequest->notes
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    /**
     * Cancel pickup request
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            $pickupRequest = $this->pickupRequestService->cancelPickupRequest($id);
            return response()->json([
                'success' => true,
                'message' => 'Pickup request berhasil dibatalkan',
                'data' => [
                    'id' => $pickupRequest->id,
                    'status' => $pickupRequest->status,
                    'pickup_code' => $pickupRequest->pickup_code
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    /**
     * Get pickup request detail
     */
    public function show($id)
    {
        try {
            $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
            if (!$pickupRequest) {
                abort(404, 'Pickup request tidak ditemukan');
            }
            return view('admin.pickup_request.show', compact('pickupRequest'));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    // public function show($id): JsonResponse
    // {
    //     try {
    //         $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);
    //         if (!$pickupRequest) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Pickup request tidak ditemukan'
    //             ], 404);
    //         }
    //         return response()->json([
    //             'success' => true,
    //             'data' => [
    //                 'id' => $pickupRequest->id,
    //                 'pickup_code' => $pickupRequest->pickup_code,
    //                 'status' => $pickupRequest->status,
    //                 'delivery_type' => $pickupRequest->delivery_type,
    //                 'payment_method' => $pickupRequest->payment_method,
    //                 'recipient_name' => $pickupRequest->recipient_name,
    //                 'recipient_phone' => $pickupRequest->recipient_phone,
    //                 'recipient_address' => $pickupRequest->getFullRecipientAddressAttribute(),
    //                 'pickup_address' => $pickupRequest->getFullPickupAddressAttribute(),
    //                 'total_amount' => $pickupRequest->total_amount,
    //                 'items_count' => $pickupRequest->items->count(),
    //                 'courier_service' => $pickupRequest->courier_service,
    //                 'courier_tracking_number' => $pickupRequest->courier_tracking_number,
    //                 'user' => [
    //                     'name' => $pickupRequest->user->name,
    //                     'email' => $pickupRequest->user->email
    //                 ],
    //                 'created_at' => $pickupRequest->created_at->format('d M Y H:i'),
    //                 'pickup_scheduled_at' => $pickupRequest->pickup_scheduled_at ? $pickupRequest->pickup_scheduled_at->format('d M Y H:i') : null,
    //                 'picked_up_at' => $pickupRequest->picked_up_at ? $pickupRequest->picked_up_at->format('d M Y H:i') : null,
    //                 'delivered_at' => $pickupRequest->delivered_at ? $pickupRequest->delivered_at->format('d M Y H:i') : null,
    //                 'can_be_cancelled' => $pickupRequest->canBeCancelled(),
    //                 'can_be_scheduled' => $pickupRequest->canBeScheduled(),
    //                 'can_be_picked_up' => $pickupRequest->canBePickedUp(),
    //                 'is_pickup_type' => $pickupRequest->isPickupType(),
    //                 'is_drop_off_type' => $pickupRequest->isDropOffType()
    //             ]
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}