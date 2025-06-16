<?php

namespace App\Http\Controllers\Api\Admin\PickupRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\PickupRequestResource;
use App\Models\PickupRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiPickupRequestController extends Controller
{
    /**
     * Menampilkan daftar semua pickup request dengan filter, paginasi, dan statistik.
     */
    public function index(Request $request): JsonResponse
    {
        try {
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

            
            $perPage = $request->get('per_page', 20);
            $pickupRequests = $query->paginate($perPage)->withQueryString();

            
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
                'total_revenue' => (float) PickupRequest::where('status', 'delivered')->sum('product_total'),
                'total_shipping' => (float) PickupRequest::where('status', 'delivered')->sum('shipping_cost'),
                'total_service_fee' => (float) PickupRequest::where('status', 'delivered')->sum('service_fee'),
                'total_amount' => (float) PickupRequest::where('status', 'delivered')->sum('total_amount'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data pickup request berhasil diambil',
                'data' => [
                    'pickup_requests' => PickupRequestResource::collection($pickupRequests->items()),
                    'pagination' => [
                        'current_page' => $pickupRequests->currentPage(),
                        'last_page' => $pickupRequests->lastPage(),
                        'per_page' => $pickupRequests->perPage(),
                        'total' => $pickupRequests->total(),
                        'from' => $pickupRequests->firstItem(),
                        'to' => $pickupRequests->lastItem(),
                    ],
                    'stats' => $stats,
                    'revenue' => $revenue,
                    'filters' => $request->only(['search', 'status', 'payment_method', 'date_from', 'date_to'])
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data pickup request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
