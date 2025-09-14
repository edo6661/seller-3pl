<?php
namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Requests\StoreProductRequest;
use App\Requests\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiSellerProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Get all products untuk seller yang sedang login dengan pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $search = $request->get('search');
            $status = $request->get('status');
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            
            $query = Product::where('user_id', $userId);

            
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }

            
            $products = $query->orderBy('created_at', 'desc')
                             ->paginate($perPage)
                             ->withQueryString();

            
            $stats = [
                'total' => Product::where('user_id', $userId)->count(),
                'active' => Product::where('user_id', $userId)->where('is_active', true)->count(),
                'inactive' => Product::where('user_id', $userId)->where('is_active', false)->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => [
                    'products' => ProductResource::collection($products->items()),
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                        'from' => $products->firstItem(),
                        'to' => $products->lastItem(),
                    ],
                    'stats' => $stats,
                    'filters' => [
                        'search' => $search,
                        'status' => $status
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new product
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $product = $this->productService->createProduct($data);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dibuat',
                'data' => [
                    'product' => new ProductResource($product)
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
                'message' => 'Terjadi kesalahan saat membuat produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific product by ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            if ($product->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke produk ini'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => [
                    'product' => new ProductResource($product)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            if ($product->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke produk ini'
                ], 403);
            }

            $data = $request->validated();
            $updatedProduct = $this->productService->updateProduct($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui',
                'data' => [
                    'product' => new ProductResource($updatedProduct)
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
                'message' => 'Terjadi kesalahan saat memperbarui produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete product
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            if ($product->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke produk ini'
                ], 403);
            }

            $deleted = $this->productService->deleteProduct($id);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil dihapus atau dinonaktifkan'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus produk'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle product status (active/inactive)
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            if ($product->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke produk ini'
                ], 403);
            }

            $updatedProduct = $this->productService->toggleProductStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Status produk berhasil diubah',
                'data' => [
                    'product' => new ProductResource($updatedProduct)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search products dengan pagination
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $search = $request->get('search', '');
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            
            $query = Product::where('user_id', $userId);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            
            $products = $query->orderBy('created_at', 'desc')
                             ->paginate($perPage)
                             ->withQueryString();

            return response()->json([
                'success' => true,
                'message' => 'Pencarian produk berhasil',
                'data' => [
                    'products' => ProductResource::collection($products->items()),
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                        'from' => $products->firstItem(),
                        'to' => $products->lastItem(),
                    ],
                    'filters' => [
                        'search' => $search
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product statistics
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $stats = $this->productService->getProductStats($userId);

            return response()->json([
                'success' => true,
                'message' => 'Statistik produk berhasil diambil',
                'data' => [
                    'stats' => $stats
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}