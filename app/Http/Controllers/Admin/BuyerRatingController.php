<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BuyerRatingService;
use App\Requests\StoreBuyerRatingRequest as RequestsStoreBuyerRatingRequest;
use App\Requests\UpdateBuyerRatingRequest as RequestsUpdateBuyerRatingRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BuyerRatingController extends Controller
{
    protected BuyerRatingService $buyerRatingService;

    public function __construct(BuyerRatingService $buyerRatingService)
    {
        $this->buyerRatingService = $buyerRatingService;
    }

    /**
     * Menampilkan daftar semua buyer rating
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        if ($search) {
            $ratings = $this->buyerRatingService->searchRatings($search, $perPage);
        } else {
            $ratings = $this->buyerRatingService->getPaginatedRatings($perPage);
        }

        $stats = $this->buyerRatingService->getRatingStats();
        $highRiskBuyers = $this->buyerRatingService->getHighRiskBuyers();

        return view('admin.buyer-rating.index', compact('ratings', 'stats', 'highRiskBuyers', 'search'));
    }

    /**
     * Menampilkan form untuk membuat buyer rating baru
     */
    public function create(): View
    {
        return view('admin.buyer-rating.create');
    }

    /**
     * Menyimpan buyer rating baru ke database
     */
    public function store(RequestsStoreBuyerRatingRequest $request): RedirectResponse
    {
        try {
            $this->buyerRatingService->createRating($request->validated());
            
            return redirect()
                ->route('admin.buyer-ratings.index')
                ->with('success', 'Buyer rating berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan buyer rating: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail buyer rating
     */
    public function show(int $id): View
    {
        $rating = $this->buyerRatingService->getRatingById($id);
        
        if (!$rating) {
            abort(404, 'Buyer rating tidak ditemukan.');
        }

        return view('admin.buyer-rating.show', compact('rating'));
    }

    /**
     * Menampilkan form untuk mengedit buyer rating
     */
    public function edit(int $id): View
    {
        $rating = $this->buyerRatingService->getRatingById($id);
        
        if (!$rating) {
            abort(404, 'Buyer rating tidak ditemukan.');
        }

        return view('admin.buyer-rating.edit', compact('rating'));
    }

    /**
     * Mengupdate buyer rating di database
     */
    public function update(RequestsUpdateBuyerRatingRequest $request, int $id): RedirectResponse
    {
        try {
            $rating = $this->buyerRatingService->updateRating($id, $request->validated());
            
            if (!$rating) {
                return redirect()
                    ->route('admin.buyer-ratings.index')
                    ->with('error', 'Buyer rating tidak ditemukan.');
            }

            return redirect()
                ->route('admin.buyer-ratings.index')
                ->with('success', 'Buyer rating berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui buyer rating: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus buyer rating dari database
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $deleted = $this->buyerRatingService->deleteRating($id);
            
            if (!$deleted) {
                return redirect()
                    ->route('admin.buyer-ratings.index')
                    ->with('error', 'Buyer rating tidak ditemukan.');
            }

            return redirect()
                ->route('admin.buyer-ratings.index')
                ->with('success', 'Buyer rating berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.buyer-ratings.index')
                ->with('error', 'Gagal menghapus buyer rating: ' . $e->getMessage());
        }
    }
}