<?php
namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Requests\PickupRequest\SchedulePickupRequest;
use App\Requests\PickupRequest\StorePickupRequestRequest;
use App\Requests\PickupRequest\UpdatePickupRequestRequest;
use App\Services\GoogleMapsService;
use App\Services\PickupRequestService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PickupRequestController extends Controller
{
    protected $pickupRequestService;
    protected $productService;
    protected $googleMapsService;


    public function __construct(PickupRequestService $pickupRequestService, ProductService $productService, GoogleMapsService $googleMapsService)
    {
        $this->pickupRequestService = $pickupRequestService;
        $this->productService = $productService;
        $this->googleMapsService = $googleMapsService;
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

        public function store(StorePickupRequestRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Auto-fill koordinat untuk alamat penerima jika belum ada
            if (empty($data['recipient_latitude']) || empty($data['recipient_longitude'])) {
                $recipientAddress = $this->buildFullAddress(
                    $data['recipient_address'],
                    $data['recipient_city'],
                    $data['recipient_province'],
                    $data['recipient_postal_code']
                );
                
                $recipientGeocode = $this->googleMapsService->geocodeAddress($recipientAddress);
                if ($recipientGeocode) {
                    $data['recipient_latitude'] = $recipientGeocode['latitude'];
                    $data['recipient_longitude'] = $recipientGeocode['longitude'];
                }
            }

            // Auto-fill koordinat untuk alamat pickup jika belum ada
            if (empty($data['pickup_latitude']) || empty($data['pickup_longitude'])) {
                $pickupAddress = $this->buildFullAddress(
                    $data['pickup_address'],
                    $data['pickup_city'],
                    $data['pickup_province'],
                    $data['pickup_postal_code']
                );
                
                $pickupGeocode = $this->googleMapsService->geocodeAddress($pickupAddress);
                if ($pickupGeocode) {
                    $data['pickup_latitude'] = $pickupGeocode['latitude'];
                    $data['pickup_longitude'] = $pickupGeocode['longitude'];
                }
            }

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
        /**
     * API endpoint untuk geocoding
     */
    public function geocodeAddress(Request $request)
    {
        // Validasi input
        $request->validate([
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10'
        ]);

        // Pastikan minimal ada satu field yang diisi
        if (empty($request->input('address')) && empty($request->input('city'))) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat atau kota harus diisi'
            ], 400);
        }

        // Validasi API key
        if (!$this->googleMapsService->validateApiKey()) {
            return response()->json([
                'success' => false,
                'message' => 'Google Maps API tidak dikonfigurasi dengan benar'
            ], 500);
        }

        $address = $request->input('address');
        $city = $request->input('city');
        $province = $request->input('province');
        $postalCode = $request->input('postal_code');
        
        $fullAddress = $this->buildFullAddress($address, $city, $province, $postalCode);
        $result = $this->googleMapsService->geocodeAddress($fullAddress);
        
        if ($result) {
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Alamat tidak ditemukan atau terjadi kesalahan pada layanan peta'
        ], 404);
    }

    public function reverseGeocode(Request $request)
    {
        // Validasi input
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        // Validasi API key
        if (!$this->googleMapsService->validateApiKey()) {
            return response()->json([
                'success' => false,
                'message' => 'Google Maps API tidak dikonfigurasi dengan benar'
            ], 500);
        }

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        
        $result = $this->googleMapsService->reverseGeocode($latitude, $longitude);
        
        if ($result) {
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Koordinat tidak valid atau terjadi kesalahan pada layanan peta'
        ], 404);
    }
    private function buildFullAddress($address, $city, $province, $postalCode)
    {
        $parts = array_filter([$address, $city, $province, $postalCode]);
        return implode(', ', $parts) . ', Indonesia';
    }

}