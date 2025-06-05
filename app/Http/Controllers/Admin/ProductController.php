<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request): View
    {
        $search = $request->get('search');
        $userId = $request->get('user_id');
        $status = $request->get('status');
        
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
                         ->paginate(10)
                         ->withQueryString();
        
        
        $stats = [
            'total' => Product::count(),
            'active' => Product::active()->count(),
            'inactive' => Product::where('is_active', false)->count(),
            'users_with_products' => Product::distinct('user_id')->count('user_id')
        ];
        
        $users = User::whereHas('products')->get();
        
        return view('admin.products.index', compact('products', 'stats', 'search', 'userId', 'status', 'users'));
    }

}