<?php

namespace App\Http\Controllers\Api\Admin\BuyerRating;

use App\Http\Controllers\Controller;
use App\Http\Resources\BuyerRatingResource;
use App\Requests\StoreBuyerRatingRequest;
use App\Requests\UpdateBuyerRatingRequest;
use App\Services\BuyerRatingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ApiBuyerRatingController extends Controller
{
    protected BuyerRatingService $buyerRatingService;

    public function __construct(BuyerRatingService $buyerRatingService)
    {
        $this->buyerRatingService = $buyerRatingService;
    }

    /**
     * Menampilkan daftar buyer rating dengan paginasi dan filter.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search');
            $perPage = $request->get('per_page', 10);

            $ratings = $search
                ? $this->buyerRatingService->searchRatings($search, $perPage)
                : $this->buyerRatingService->getPaginatedRatings($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data rating pembeli berhasil diambil.',
                'data' => [
                    'ratings' => BuyerRatingResource::collection($ratings->items()),
                    'pagination' => [
                        'current_page' => $ratings->currentPage(),
                        'last_page' => $ratings->lastPage(),
                        'per_page' => $ratings->perPage(),
                        'total' => $ratings->total(),
                        'from' => $ratings->firstItem(),
                        'to' => $ratings->lastItem(),
                    ],
                    'filters' => ['search' => $search]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data rating pembeli.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan buyer rating baru.
     */
    public function store(StoreBuyerRatingRequest $request): JsonResponse
    {
        try {
            $rating = $this->buyerRatingService->createRating($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Rating pembeli berhasil dibuat.',
                'data' => new BuyerRatingResource($rating)
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat rating pembeli.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail buyer rating.
     */
    public function show(int $id): JsonResponse
    {
        $rating = $this->buyerRatingService->getRatingById($id);

        if (!$rating) {
            return response()->json([
                'success' => false,
                'message' => 'Rating pembeli tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail rating pembeli berhasil diambil.',
            'data' => new BuyerRatingResource($rating)
        ]);
    }

    /**
     * Memperbarui buyer rating.
     */
    public function update(UpdateBuyerRatingRequest $request, int $id): JsonResponse
    {
        try {
            $rating = $this->buyerRatingService->updateRating($id, $request->validated());
            
            if (!$rating) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Rating pembeli tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Rating pembeli berhasil diperbarui.',
                'data' => new BuyerRatingResource($rating->fresh())
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui rating pembeli.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus buyer rating.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->buyerRatingService->deleteRating($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Rating pembeli tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Rating pembeli berhasil dihapus.'
        ]);
    }

    /**
     * Mendapatkan statistik buyer rating.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->buyerRatingService->getRatingStats();
            return response()->json([
                'success' => true,
                'message' => 'Statistik berhasil diambil.',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar buyer dengan risiko tinggi.
     */
    public function getHighRiskBuyers(): JsonResponse
    {
        try {
            $highRiskBuyers = $this->buyerRatingService->getHighRiskBuyers();
            return response()->json([
                'success' => true,
                'message' => 'Data pembeli berisiko tinggi berhasil diambil.',
                'data' => BuyerRatingResource::collection($highRiskBuyers)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pembeli berisiko tinggi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
