<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Requests\StoreProductRequest;
use App\Requests\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Menampilkan daftar semua produk milik user yang sedang login
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $userId = auth()->id();
        
        if ($search) {
            $products = $this->productService->searchProducts($search, $userId);
        } else {
            $products = $this->productService->getUserProducts($userId);
        }
        
        $stats = $this->productService->getProductStats($userId);
        
        return view('seller.products.index', compact('products', 'stats', 'search'));
    }

    /**
     * Menampilkan form untuk membuat produk baru
     */
    public function create()
    {
        return view('seller.products.create');
    }

    /**
     * Menyimpan produk baru ke database
     */
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

    /**
     * Menampilkan detail produk
     */
    public function show(int $id)
    {
        $product = $this->productService->getProductById($id);
        
        if (!$product || $product->user_id !== auth()->id()) {
            abort(404, 'Produk tidak ditemukan.');
        }
        
        return view('seller.products.show', compact('product'));
    }

    /**
     * Menampilkan form untuk mengedit produk
     */
    public function edit(int $id)
    {
        $product = $this->productService->getProductById($id);
        
        if (!$product || $product->user_id !== auth()->id()) {
            abort(404, 'Produk tidak ditemukan.');
        }
        
        return view('seller.products.edit', compact('product'));
    }

    /**
     * Mengupdate produk di database
     */
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

    /**
     * Menghapus produk dari database
     */
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

    /**
     * Toggle status aktif/nonaktif produk
     */
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
}