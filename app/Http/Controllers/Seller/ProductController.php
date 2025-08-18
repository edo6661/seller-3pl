<?php
namespace App\Http\Controllers\Seller;
use App\Http\Controllers\Controller;
use App\Requests\StoreProductRequest;
use App\Requests\UpdateProductRequest;
use App\Services\ProductService;
use App\Services\ProductExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class ProductController extends Controller
{
    protected ProductService $productService;
    protected ProductExportService $exportService;
    public function __construct(ProductService $productService, ProductExportService $exportService)
    {
        $this->productService = $productService;
        $this->exportService = $exportService;
    }
     private function getSellerId()
    {
        $user = auth()->user();
        if ($user->isSeller()) {
            return $user->id;
        }
        if ($user->isTeamMember()) {
            return $user->memberOf()->first()->seller_id;
        }
        abort(403, 'Akses tidak diizinkan.');
    }
     public function index(Request $request)
    {
        $search = $request->get('search');
        $sellerId = $this->getSellerId(); 
        if ($search) {
            $products = $this->productService->searchProducts($search, $sellerId);
        } else {
            $products = $this->productService->getUserProducts($sellerId);
        }
        $stats = $this->productService->getProductStats($sellerId);
        return view('seller.products.index', compact('products', 'stats', 'search'));
    }
    public function create()
    {
        return view('seller.products.create');
    }
    public function store(StoreProductRequest $request)
    {
        try {
            $this->productService->createProduct(
                array_merge($request->validated(), ['user_id' => auth()->id()])
            );
            return redirect()
                ->route('seller.products.index')
                ->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan produk: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'data' => $request->all(),
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan produk. Silakan coba lagi.');
        }
    }
    public function show(int $id)
    {
        $product = $this->productService->getProductById($id);
        if (!$product || $product->user_id !== auth()->id()) {
            abort(404, 'Produk tidak ditemukan.');
        }
        return view('seller.products.show', compact('product'));
    }
    public function edit(int $id)
    {
        $product = $this->productService->getProductById($id);
        if (!$product || $product->user_id !== auth()->id()) {
            abort(404, 'Produk tidak ditemukan.');
        }
        return view('seller.products.edit', compact('product'));
    }
    public function update(UpdateProductRequest $request, int $id)
    {
        try {
            $product = $this->productService->getProductById($id);
            if (!$product || $product->user_id !== auth()->id()) {
                abort(404, 'Produk tidak ditemukan.');
            }
            $this->productService->updateProduct($id, $request->validated());
            return redirect()
                ->route('seller.products.index')
                ->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui produk. Silakan coba lagi.');
        }
    }
    public function destroy(int $id)
    {
        try {
            $product = $this->productService->getProductById($id);
            if (!$product || $product->user_id !== auth()->id()) {
                abort(404, 'Produk tidak ditemukan.');
            }
            $deleted = $this->productService->deleteProduct($id);
            if ($deleted) {
                return redirect()
                    ->route('seller.products.index')
                    ->with('success', 'Produk berhasil dihapus!');
            } else {
                return redirect()
                    ->back()
                    ->with('error', 'Gagal menghapus produk.');
            }
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus produk. Silakan coba lagi.');
        }
    }
    public function toggleStatus(int $id)
    {
        try {
            $product = $this->productService->getProductById($id);
            if (!$product || $product->user_id !== auth()->id()) {
                abort(404, 'Produk tidak ditemukan.');
            }
            $updatedProduct = $this->productService->toggleProductStatus($id);
            $status = $updatedProduct->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()
                ->route('seller.products.index')
                ->with('success', "Produk berhasil {$status}!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengubah status produk. Silakan coba lagi.');
        }
    }
    public function bulkDelete(Request $request)
    {
        try {
            $productIds = $request->input('product_ids', []);
            if (empty($productIds)) {
                return redirect()
                    ->back()
                    ->with('error', 'Tidak ada produk yang dipilih.');
            }
            $deletedCount = $this->productService->bulkDeleteProducts($productIds, auth()->id());
            return redirect()
                ->route('seller.products.index')
                ->with('success', "{$deletedCount} produk berhasil dihapus!");
        } catch (\Exception $e) {
            Log::error('Gagal menghapus produk secara bulk: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'product_ids' => $request->input('product_ids', []),
            ]);
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus produk. Silakan coba lagi.');
        }
    }
    public function bulkToggleStatus(Request $request)
    {
        try {
            $productIds = $request->input('product_ids', []);
            $action = $request->input('action');
            if (empty($productIds)) {
                return redirect()
                    ->back()
                    ->with('error', 'Tidak ada produk yang dipilih.');
            }
            if (!in_array($action, ['activate', 'deactivate'])) {
                return redirect()
                    ->back()
                    ->with('error', 'Aksi tidak valid.');
            }
            $updatedCount = $this->productService->bulkToggleProductStatus($productIds, $action, auth()->id());
            $statusText = $action === 'activate' ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()
                ->route('seller.products.index')
                ->with('success', "{$updatedCount} produk berhasil {$statusText}!");
        } catch (\Exception $e) {
            Log::error('Gagal mengubah status produk secara bulk: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'product_ids' => $request->input('product_ids', []),
                'action' => $request->input('action'),
            ]);
            return redirect()
                ->back()
                ->with('error', 'Gagal mengubah status produk. Silakan coba lagi.');
        }
    }
    public function export(Request $request)
    {
        try {
            $search = $request->get('search');
            $userId = auth()->id();
            if ($search) {
                $products = $this->productService->searchProducts($search, $userId);
            } else {
                $products = $this->productService->getUserProducts($userId);
            }
            return $this->exportService->exportToExcel($products, auth()->user()->name);
        } catch (\Exception $e) {
            Log::error('Gagal export produk: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'search' => $search,
            ]);
            return redirect()
                ->back()
                ->with('error', 'Gagal mengexport data produk. Silakan coba lagi.');
        }
    }
    public function exportSelected(Request $request)
    {
        try {
            $productIds = $request->input('product_ids', []);
            if (empty($productIds)) {
                return redirect()
                    ->back()
                    ->with('error', 'Tidak ada produk yang dipilih untuk diexport.');
            }
            $products = $this->productService->getProductsByIds($productIds, auth()->id());
            return $this->exportService->exportToExcel($products, auth()->user()->name);
        } catch (\Exception $e) {
            Log::error('Gagal export produk terpilih: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'product_ids' => $request->input('product_ids', []),
            ]);
            return redirect()
                ->back()
                ->with('error', 'Gagal mengexport produk yang dipilih. Silakan coba lagi.');
        }
    }
}