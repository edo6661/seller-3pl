<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\PickupRequestService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected ProductService $productService;
    protected PickupRequestService $pickupRequestService;
    protected WalletService $walletService;

    public function __construct(
        ProductService $productService,
        PickupRequestService $pickupRequestService,
        WalletService $walletService
    ) {
        $this->productService = $productService;
        $this->pickupRequestService = $pickupRequestService;
        $this->walletService = $walletService;
    }

    public function index()
    {
        $user = Auth::user();
        
        
        $recentPickupRequests = $this->pickupRequestService->getUserPickupRequests($user->id)
            ->take(5);
        
        
        $recentWalletTransactions = $this->walletService->getTransactionHistory($user, 5);
        
        
        $bestSellingProducts = $this->getBestSellingProducts($user->id);
        
        
        $pickupStats = $this->pickupRequestService->getPickupRequestStats($user->id);
        $productStats = $this->productService->getProductStats($user->id);
        $revenueStats = $this->pickupRequestService->getTotalRevenue($user->id);
        
        
        $wallet = $this->walletService->getOrCreateWallet($user);
        
        
        $monthlyStats = $this->getMonthlyStats($user->id);
        
        return view('seller.dashboard', compact(
            'recentPickupRequests',
            'recentWalletTransactions',
            'bestSellingProducts',
            'pickupStats',
            'productStats',
            'revenueStats',
            'wallet',
            'monthlyStats'
        ));
    }

    private function getBestSellingProducts($userId, $limit = 5)
    {
        return DB::table('pickup_request_items')
            ->join('products', 'pickup_request_items.product_id', '=', 'products.id')
            ->join('pickup_requests', 'pickup_request_items.pickup_request_id', '=', 'pickup_requests.id')
            ->where('products.user_id', $userId)
            ->where('pickup_requests.status', 'delivered')
            ->select(
                'products.id',
                'products.name',
                'products.price',
                DB::raw('SUM(pickup_request_items.quantity) as total_sold'),
                DB::raw('SUM(pickup_request_items.total_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getMonthlyStats($userId)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        
        $thisMonthOrders = DB::table('pickup_requests')
            ->where('user_id', $userId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();
            
        $thisMonthRevenue = DB::table('pickup_requests')
            ->where('user_id', $userId)
            ->where('status', 'delivered')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('product_total');
            
        
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthOrders = DB::table('pickup_requests')
            ->where('user_id', $userId)
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();
            
        $lastMonthRevenue = DB::table('pickup_requests')
            ->where('user_id', $userId)
            ->where('status', 'delivered')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('product_total');
            
        
        $ordersGrowth = $lastMonthOrders > 0 
            ? (($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100 
            : 0;
            
        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;
        
        return [
            'this_month_orders' => $thisMonthOrders,
            'this_month_revenue' => $thisMonthRevenue,
            'last_month_orders' => $lastMonthOrders,
            'last_month_revenue' => $lastMonthRevenue,
            'orders_growth' => $ordersGrowth,
            'revenue_growth' => $revenueGrowth,
        ];
    }
}