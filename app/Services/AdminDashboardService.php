<?php
namespace App\Services;
use App\Models\PickupRequest;
use App\Models\Product;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class AdminDashboardService
{
    public function getOverallStats(): array
    {
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalPickupRequests = PickupRequest::count();
        $totalRevenue = PickupRequest::where('status', 'delivered')->sum('total_amount');
        return [
            'total_users' => $totalUsers,
            'total_products' => $totalProducts,
            'total_pickup_requests' => $totalPickupRequests,
            'total_revenue' => $totalRevenue,
        ];
    }
    public function getRecentPickupRequests(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return PickupRequest::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    public function getRecentWalletTransactions(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return WalletTransaction::with(['wallet.user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    public function getBestSellingProducts(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $productColumns = Schema::getColumnListing('products');
        return Product::with(['user'])
            ->select('products.*', DB::raw('SUM(pickup_request_items.quantity) as total_sold'))
            ->join('pickup_request_items', 'products.id', '=', 'pickup_request_items.product_id')
            ->join('pickup_requests', 'pickup_request_items.pickup_request_id', '=', 'pickup_requests.id')
            ->where('pickup_requests.status', 'delivered')
            ->groupBy($productColumns)
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();
    }
    public function getPickupRequestStatsByStatus(): array
    {
        $statusCounts = PickupRequest::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        return [
            'pending' => $statusCounts['pending'] ?? 0,
            'confirmed' => $statusCounts['confirmed'] ?? 0,
            'pickup_scheduled' => $statusCounts['pickup_scheduled'] ?? 0,
            'picked_up' => $statusCounts['picked_up'] ?? 0,
            'in_transit' => $statusCounts['in_transit'] ?? 0,
            'delivered' => $statusCounts['delivered'] ?? 0,
            'failed' => $statusCounts['failed'] ?? 0,
            'cancelled' => $statusCounts['cancelled'] ?? 0,
        ];
    }
    public function getWalletTransactionStats(): array
    {
        $stats = WalletTransaction::select('type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(amount) as total_amount')
            ->where('status', 'success')
            ->groupBy('type')
            ->get();
        $result = [
            'topup' => ['count' => 0, 'total_amount' => 0],
            'withdraw' => ['count' => 0, 'total_amount' => 0],
            'payment' => ['count' => 0, 'total_amount' => 0],
            'refund' => ['count' => 0, 'total_amount' => 0],
        ];
        foreach ($stats as $stat) {
            $result[$stat->type->value] = [
                'count' => $stat->count,
                'total_amount' => $stat->total_amount,
            ];
        }
        return $result;
    }
    public function getMonthlyRevenue(int $year = null): array
    {
        $year = $year ?? now()->year;
        $monthlyData = PickupRequest::where('status', 'delivered')
            ->whereYear('delivered_at', $year)
            ->selectRaw('MONTH(delivered_at) as month, COUNT(*) as total_orders, SUM(total_amount) as total_revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $monthlyData->firstWhere('month', $i);
            $result[] = [
                'month' => $i,
                'month_name' => now()->month($i)->format('F'),
                'total_orders' => $monthData->total_orders ?? 0,
                'total_revenue' => $monthData->total_revenue ?? 0,
            ];
        }
        return $result;
    }
    public function getTopPerformingUsers(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return User::select('users.*', DB::raw('COUNT(pickup_requests.id) as total_orders'), DB::raw('SUM(pickup_requests.total_amount) as total_revenue'))
            ->join('pickup_requests', 'users.id', '=', 'pickup_requests.user_id')
            ->where('pickup_requests.status', 'delivered')
            ->groupBy('users.id')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }
    public function getDailyStats(): array
    {
        $stats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $pickupRequests = PickupRequest::whereDate('created_at', $dateStr)->count();
            $walletTransactions = WalletTransaction::whereDate('created_at', $dateStr)->count();
            $revenue = PickupRequest::where('status', 'delivered')
                ->whereDate('delivered_at', $dateStr)
                ->sum('total_amount');
            $stats[] = [
                'date' => $dateStr,
                'date_formatted' => $date->format('d M'),
                'pickup_requests' => $pickupRequests,
                'wallet_transactions' => $walletTransactions,
                'revenue' => $revenue,
            ];
        }
        return $stats;
    }
    public function getActiveUsersCount(): int
    {
        return User::whereHas('pickupRequests', function($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })->count();
    }
    public function getTotalWalletBalance(): float
    {
        return DB::table('wallets')->sum('balance');
    }
}