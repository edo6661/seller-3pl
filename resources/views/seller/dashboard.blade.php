<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900 mb-2">Dashboard Seller</h1>
            <p class="text-neutral-600">Selamat datang kembali, {{ auth()->user()->name }}!</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Saldo Wallet -->
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primary-200 text-sm">Saldo Wallet</p>
                        <p class="text-2xl font-bold">{{ $wallet->formatted_balance }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                        <i class="fas fa-wallet text-2xl text-warning-500"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('seller.wallet.index') }}" class="text-sm hover:underline">
                        Kelola Wallet →
                    </a>
                </div>
            </div>

            <!-- Total Pesanan -->
            <div class="bg-white rounded-xl p-6 shadow-lg border border-neutral-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-neutral-600 text-sm">Total Pesanan</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ $pickupStats['total'] }}</p>
                        <div class="flex items-center mt-1">
                            <span class="text-sm {{ $monthlyStats['orders_growth'] >= 0 ? 'text-success-600' : 'text-error-600' }}">
                                <i class="fas fa-arrow-{{ $monthlyStats['orders_growth'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ abs(round($monthlyStats['orders_growth'], 1)) }}%
                            </span>
                            <span class="text-neutral-500 text-sm ml-2">dari bulan lalu</span>
                        </div>
                    </div>
                    <div class="bg-secondary-100 text-secondary-600 p-3 rounded-lg">
                        <i class="fas fa-box text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Pendapatan -->
            <div class="bg-white rounded-xl p-6 shadow-lg border border-neutral-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-neutral-600 text-sm">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-neutral-900">
                            Rp {{ number_format($revenueStats['total_revenue'], 0, ',', '.') }}
                        </p>
                        <div class="flex items-center mt-1">
                            <span class="text-sm {{ $monthlyStats['revenue_growth'] >= 0 ? 'text-success-600' : 'text-error-600' }}">
                                <i class="fas fa-arrow-{{ $monthlyStats['revenue_growth'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ abs(round($monthlyStats['revenue_growth'], 1)) }}%
                            </span>
                            <span class="text-neutral-500 text-sm ml-2">dari bulan lalu</span>
                        </div>
                    </div>
                    <div class="bg-success-100 text-success-600 p-3 rounded-lg">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Produk -->
            <div class="bg-white rounded-xl p-6 shadow-lg border border-neutral-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-neutral-600 text-sm">Total Produk</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ $productStats['total'] }}</p>
                        <p class="text-sm text-neutral-500">{{ $productStats['active'] }} aktif</p>
                    </div>
                    <div class="bg-warning-100 text-warning-600 p-3 rounded-lg">
                        <i class="fas fa-cubes text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-lg border border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Status Pesanan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Pending</span>
                        <span class="bg-warning-100 text-warning-700 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $pickupStats['pending'] }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Terkonfirmasi</span>
                        <span class="bg-info-100 text-info-700 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $pickupStats['confirmed'] }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Terkirim</span>
                        <span class="bg-success-100 text-success-700 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $pickupStats['delivered'] }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg border border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Performa Bulan Ini</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Pesanan</span>
                        <span class="text-lg font-bold text-neutral-900">{{ $monthlyStats['this_month_orders'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Pendapatan</span>
                        <span class="text-lg font-bold text-neutral-900">
                            Rp {{ number_format($monthlyStats['this_month_revenue'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg border border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Aksi Cepat</h3>
                <div class="space-y-3">
                    <a href="{{ route('seller.products.create') }}" 
                       class="flex items-center p-3 bg-primary-50 text-primary-700 rounded-lg hover:bg-primary-100 transition">
                        <i class="fas fa-plus mr-3"></i>
                        <span class="text-sm font-medium">Tambah Produk</span>
                    </a>
                    <a href="{{ route('seller.pickup-request.index') }}" 
                       class="flex items-center p-3 bg-secondary-50 text-secondary-700 rounded-lg hover:bg-secondary-100 transition">
                        <i class="fas fa-truck mr-3"></i>
                        <span class="text-sm font-medium">Kelola Pesanan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Pickup Requests -->
            <div class="bg-white rounded-xl shadow-lg border border-neutral-200">
                <div class="p-6 border-b border-neutral-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-neutral-900">Pickup Requests Terbaru</h3>
                        <a href="{{ route('seller.pickup-request.index') }}" 
                           class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            Lihat Semua →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if($recentPickupRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentPickupRequests as $request)
                                <div class="flex items-center justify-between p-4 bg-neutral-50 rounded-lg hover:bg-neutral-100 transition">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $request->status === 'pending' ? 'bg-warning-100 text-warning-800' : 
                                                       ($request->status === 'confirmed' ? 'bg-info-100 text-info-800' : 
                                                       ($request->status === 'delivered' ? 'bg-success-100 text-success-800' : 'bg-neutral-100 text-neutral-800')) }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-neutral-900 truncate">
                                                    {{ $request->pickup_code }}
                                                </p>
                                                <p class="text-sm text-neutral-500 truncate">
                                                    {{ $request->recipient_name }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-neutral-900">
                                            Rp {{ number_format($request->total_amount, 0, ',', '.') }}
                                        </p>
                                        <p class="text-xs text-neutral-500">
                                            {{ $request->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-neutral-300 text-4xl mb-4">
                                <i class="fas fa-truck"></i>
                            </div>
                            <p class="text-neutral-600">Belum ada pickup request</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Wallet Transactions -->
            <div class="bg-white rounded-xl shadow-lg border border-neutral-200">
                <div class="p-6 border-b border-neutral-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-neutral-900">Transaksi Wallet Terbaru</h3>
                        <a href="{{ route('seller.wallet.index') }}" 
                           class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            Lihat Semua →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if($recentWalletTransactions->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentWalletTransactions as $transaction)
                                <div class="flex xl:items-center justify-between p-4 bg-neutral-50 rounded-lg xl:flex-row flex-col">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                                {{ $transaction->type->value === 'topup' ? 'bg-success-100 text-success-600' : 'bg-error-100 text-error-600' }}">
                                                <i class="fas fa-arrow-{{ $transaction->type->value === 'topup' ? 'down' : 'up' }} text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-neutral-900 truncate">
                                                {{ $transaction->type_label }}
                                            </p>
                                            <p class="text-sm text-neutral-500 truncate">
                                                {{ Str::limit($transaction->description, 30) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium {{ $transaction->type->value === 'topup' ? 'text-success-600' : 'text-error-600' }}">
                                            {{ $transaction->formatted_amount }}
                                        </p>
                                        <p class="text-xs text-neutral-500">
                                            {{ $transaction->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-neutral-300 text-4xl mb-4">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <p class="text-neutral-600">Belum ada transaksi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Best Selling Products -->
        <div class="mt-8 bg-white rounded-xl shadow-lg border border-neutral-200">
            <div class="p-6 border-b border-neutral-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-neutral-900">Produk Terlaris</h3>
                    <a href="{{ route('seller.products.index') }}" 
                       class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        Lihat Semua →
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($bestSellingProducts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-neutral-200">
                                    <th class="text-left py-3 px-4 font-medium text-neutral-900">Produk</th>
                                    <th class="text-right py-3 px-4 font-medium text-neutral-900">Harga</th>
                                    <th class="text-right py-3 px-4 font-medium text-neutral-900">Terjual</th>
                                    <th class="text-right py-3 px-4 font-medium text-neutral-900">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bestSellingProducts as $product)
                                    <tr class="border-b border-neutral-100 hover:bg-neutral-50">
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-neutral-900">{{ $product->name }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-right text-neutral-900">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                                                {{ $product->total_sold }} pcs
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right font-medium text-success-600">
                                            Rp {{ number_format($product->total_revenue, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-neutral-300 text-4xl mb-4">
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="text-neutral-600">Belum ada produk terjual</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.plain-app>