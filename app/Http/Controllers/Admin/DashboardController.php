<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;

class DashboardController extends Controller
{
    protected AdminDashboardService $adminDashboardService;

    public function __construct(AdminDashboardService $adminDashboardService)
    {
        $this->adminDashboardService = $adminDashboardService;
    }

    public function index()
    {
        $overallStats = $this->adminDashboardService->getOverallStats();
        $recentPickupRequests = $this->adminDashboardService->getRecentPickupRequests(8);
        $recentWalletTransactions = $this->adminDashboardService->getRecentWalletTransactions(8);
        $bestSellingProducts = $this->adminDashboardService->getBestSellingProducts(5);
        $pickupRequestStats = $this->adminDashboardService->getPickupRequestStatsByStatus();
        $walletTransactionStats = $this->adminDashboardService->getWalletTransactionStats();
        $monthlyRevenue = $this->adminDashboardService->getMonthlyRevenue();
        $topPerformingUsers = $this->adminDashboardService->getTopPerformingUsers(5);
        $dailyStats = $this->adminDashboardService->getDailyStats();
        $activeUsersCount = $this->adminDashboardService->getActiveUsersCount();
        $totalWalletBalance = $this->adminDashboardService->getTotalWalletBalance();
        return view('admin.dashboard', compact(
            'overallStats',
            'recentPickupRequests',
            'recentWalletTransactions',
            'bestSellingProducts',
            'pickupRequestStats',
            'walletTransactionStats',
            'monthlyRevenue',
            'topPerformingUsers',
            'dailyStats',
            'activeUsersCount',
            'totalWalletBalance'
        ));
    }
}