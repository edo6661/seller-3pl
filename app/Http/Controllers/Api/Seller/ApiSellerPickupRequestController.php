<?php
namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\PickupRequestResource;
use App\Requests\PickupRequest\SchedulePickupRequest;
use App\Requests\PickupRequest\StorePickupRequestRequest;
use App\Requests\PickupRequest\UpdatePickupRequestRequest;
use App\Services\PickupRequestService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiSellerPickupRequestController extends Controller
{
    protected PickupRequestService $pickupRequestService;
    protected ProductService $productService;

    public function __construct(PickupRequestService $pickupRequestService, ProductService $productService)
    {
        $this->pickupRequestService = $pickupRequestService;
        $this->productService = $productService;
    }

    /**
     * Get all pickup requests untuk seller yang sedang login dengan pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $search = $request->get('search');
            $status = $request->get('status');
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            if ($search) {
                $pickupRequests = $this->pickupRequestService->searchPickupRequests($search, $userId);
            } elseif ($status) {
                $pickupRequests = $this->pickupRequestService->getPickupRequestsByStatus($userId, $status);
            } else {
                $pickupRequests = $this->pickupRequestService->getUserPickupRequests($userId);
            }
            // manual paginationnya karena collection
            $total = $pickupRequests->count();
            $offset = ($page - 1) * $perPage;
            $paginatedItems = $pickupRequests->slice($offset, $perPage);
            
            $lastPage = ceil($total / $perPage);
            $from = $offset + 1;
            $to = min($offset + $perPage, $total);

            $stats = $this->pickupRequestService->getPickupRequestStats($userId);
            $revenue = $this->pickupRequestService->getTotalRevenue($userId);

            return response()->json([
                'success' => true,
                'message' => 'Data pickup request berhasil diambil',
                'data' => [
                    'pickup_requests' => PickupRequestResource::collection($paginatedItems),
                    'pagination' => [
                        'current_page' => (int)$page,
                        'last_page' => $lastPage,
                        'per_page' => (int)$perPage,
                        'total' => $total,
                        'from' => $from,
                        'to' => $to,
                    ],
                    'stats' => $stats,
                    'revenue' => $revenue,
                    'filters' => [
                        'search' => $search,
                        'status' => $status
                    ]
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

    /**
     * Create new pickup request
     */
    public function store(StorePickupRequestRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $pickupRequest = $this->pickupRequestService->createPickupRequest($data);

            return response()->json([
                'success' => true,
                'message' => 'Pickup request berhasil dibuat dengan kode: ' . $pickupRequest->pickup_code,
                'data' => [
                    'pickup_request' => new PickupRequestResource($pickupRequest)
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat pickup request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific pickup request by ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);

            if (!$pickupRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pickup request tidak ditemukan'
                ], 404);
            }

            if ($pickupRequest->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke pickup request ini'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data pickup request berhasil diambil',
                'data' => [
                    'pickup_request' => new PickupRequestResource($pickupRequest)
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

    /**
     * Update pickup request
     */
    public function update(UpdatePickupRequestRequest $request, int $id): JsonResponse
    {
        try {
            $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);

            if (!$pickupRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pickup request tidak ditemukan'
                ], 404);
            }

            if ($pickupRequest->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke pickup request ini'
                ], 403);
            }

            if (!$pickupRequest->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pickup request ini tidak dapat diedit'
                ], 400);
            }

            $data = $request->validated();
            $updatedPickupRequest = $this->pickupRequestService->updatePickupRequest($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Pickup request berhasil diperbarui',
                'data' => [
                    'pickup_request' => new PickupRequestResource($updatedPickupRequest)
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui pickup request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel pickup request
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);

            if (!$pickupRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pickup request tidak ditemukan'
                ], 404);
            }

            if ($pickupRequest->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke pickup request ini'
                ], 403);
            }

            $cancelledPickupRequest = $this->pickupRequestService->cancelPickupRequest($id);

            return response()->json([
                'success' => true,
                'message' => 'Pickup request berhasil dibatalkan',
                'data' => [
                    'pickup_request' => new PickupRequestResource($cancelledPickupRequest)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan pickup request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm pickup request
     */
    public function confirm(Request $request, int $id): JsonResponse
    {
        try {
            $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);

            if (!$pickupRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pickup request tidak ditemukan'
                ], 404);
            }

            if ($pickupRequest->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke pickup request ini'
                ], 403);
            }

            $confirmedPickupRequest = $this->pickupRequestService->confirmPickupRequest($id);

            return response()->json([
                'success' => true,
                'message' => 'Pickup request berhasil dikonfirmasi',
                'data' => [
                    'pickup_request' => new PickupRequestResource($confirmedPickupRequest)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengkonfirmasi pickup request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Schedule pickup
     */
    public function schedulePickup(SchedulePickupRequest $request, int $id): JsonResponse
    {
        try {
            $pickupRequest = $this->pickupRequestService->getPickupRequestById($id);

            if (!$pickupRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pickup request tidak ditemukan'
                ], 404);
            }

            if ($pickupRequest->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke pickup request ini'
                ], 403);
            }

            $scheduledPickupRequest = $this->pickupRequestService->schedulePickup(
                $id, 
                $request->validated()['pickup_scheduled_at']
            );

            return response()->json([
                'success' => true,
                'message' => 'Jadwal pickup berhasil diatur',
                'data' => [
                    'pickup_request' => new PickupRequestResource($scheduledPickupRequest)
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengatur jadwal pickup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search pickup requests
     */
    public function search(Request $request, string $query): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            $pickupRequests = $this->pickupRequestService->searchPickupRequests($query, $userId);

            $total = $pickupRequests->count();
            $offset = ($page - 1) * $perPage;
            $paginatedItems = $pickupRequests->slice($offset, $perPage);
            
            $lastPage = ceil($total / $perPage);
            $from = $offset + 1;
            $to = min($offset + $perPage, $total);

            return response()->json([
                'success' => true,
                'message' => 'Pencarian pickup request berhasil',
                'data' => [
                    'pickup_requests' => PickupRequestResource::collection($paginatedItems),
                    'pagination' => [
                        'current_page' => (int)$page,
                        'last_page' => $lastPage,
                        'per_page' => (int)$perPage,
                        'total' => $total,
                        'from' => $from,
                        'to' => $to,
                    ],
                    'filters' => [
                        'search' => $query
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari pickup request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pickup request statistics
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $stats = $this->pickupRequestService->getPickupRequestStats($userId);
            $revenue = $this->pickupRequestService->getTotalRevenue($userId);

            return response()->json([
                'success' => true,
                'message' => 'Statistik pickup request berhasil diambil',
                'data' => [
                    'stats' => $stats,
                    'revenue' => $revenue
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik pickup request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard data
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $stats = $this->pickupRequestService->getPickupRequestStats($userId);
            $revenue = $this->pickupRequestService->getTotalRevenue($userId);
            $monthlyStats = $this->pickupRequestService->getMonthlyStats($userId);
            
            $recentPickupRequests = $this->pickupRequestService->getUserPickupRequests($userId)->take(5);

            return response()->json([
                'success' => true,
                'message' => 'Data dashboard berhasil diambil',
                'data' => [
                    'stats' => $stats,
                    'revenue' => $revenue,
                    'monthly_stats' => $monthlyStats,
                    'recent_pickup_requests' => PickupRequestResource::collection($recentPickupRequests)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}