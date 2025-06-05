<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupRequest;
use Illuminate\Http\Request;

class PickupRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PickupRequest::with(['user', 'items.product']);

        // Search functionality
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

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort by latest
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $pickupRequests = $query->paginate(20)->withQueryString();

        // Statistics
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

        // Revenue stats
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
}