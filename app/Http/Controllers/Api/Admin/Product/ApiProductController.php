<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Get all products with filters (Admin API)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search');
            $userId = $request->get('user_id');
            $status = $request->get('status');
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            $query = Product::with('user');

            
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            
            if ($userId) {
                $query->where('user_id', $userId);
            }

            
            if ($status === 'active') {
                $query->active();
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }

            $products = $query->orderBy('created_at', 'desc')
                             ->paginate($perPage)
                             ->withQueryString();

            
            $stats = [
                'total' => Product::count(),
                'active' => Product::active()->count(),
                'inactive' => Product::where('is_active', false)->count(),
                'users_with_products' => Product::distinct('user_id')->count('user_id')
            ];

            
            $users = User::whereHas('products')
                        ->select('id', 'name', 'email')
                        ->get();

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
                    'users' => $users,
                    'filters' => [
                        'search' => $search,
                        'user_id' => $userId,
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
}