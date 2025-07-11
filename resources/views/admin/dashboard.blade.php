<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900 mb-2">Dashboard Admin</h1>
            <p class="text-neutral-600">Selamat datang di panel admin, {{ auth()->user()->name }}!</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primary-200 text-sm">Total Users</p>
                        <p class="text-2xl font-bold">{{ number_format($overallStats['total_users']) }}</p>
                        <p class="text-primary-200 text-sm">{{ number_format($activeUsersCount) }} aktif (30 hari)</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                        <i class="fas fa-users text-2xl text-primary"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-success-500 to-success-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-success-200 text-sm">Total Revenue</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($overallStats['total_revenue'], 0, ',', '.') }}</p>
                        <p class="text-success-200 text-sm">dari pesanan selesai</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                        <i class="fas fa-chart-line text-success"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-success-500 to-success-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-info-200 text-sm">Total Pickup Requests</p>
                        <p class="text-2xl font-bold">{{ number_format($overallStats['total_pickup_requests']) }}</p>
                        <p class="text-info-200 text-sm">{{ number_format($pickupRequestStats['pending']) }} pending</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                        <i class="fas fa-truck text-2xl text-success"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-warning-500 to-warning-600 text-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-warning-200 text-sm">Total Wallet Balance</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($totalWalletBalance, 0, ',', '.') }}</p>
                        <p class="text-warning-200 text-sm">semua user</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                        <i class="fas fa-wallet text-2xl text-warning-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-lg border border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Status Pickup Requests</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Pending</span>
                        <span class="bg-warning-100 text-warning-700 px-3 py-1 rounded-full text-sm font-medium">
                            {{ number_format($pickupRequestStats['pending']) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Confirmed</span>
                        <span class="bg-info-100 text-info-700 px-3 py-1 rounded-full text-sm font-medium">
                            {{ number_format($pickupRequestStats['confirmed']) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Delivered</span>
                        <span class="bg-success-100 text-success-700 px-3 py-1 rounded-full text-sm font-medium">
                            {{ number_format($pickupRequestStats['delivered']) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Cancelled</span>
                        <span class="bg-error-100 text-error-700 px-3 py-1 rounded-full text-sm font-medium">
                            {{ number_format($pickupRequestStats['cancelled']) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg border border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Transaksi Wallet</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Top Up</span>
                        <div class="text-right">
                            <span class="text-sm font-medium text-neutral-900">{{ number_format($walletTransactionStats['topup']['count']) }}</span>
                            <p class="text-xs text-neutral-500">Rp {{ number_format($walletTransactionStats['topup']['total_amount'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Payment</span>
                        <div class="text-right">
                            <span class="text-sm font-medium text-neutral-900">{{ number_format($walletTransactionStats['payment']['count']) }}</span>
                            <p class="text-xs text-neutral-500">Rp {{ number_format($walletTransactionStats['payment']['total_amount'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-neutral-600">Withdraw</span>
                        <div class="text-right">
                            <span class="text-sm font-medium text-neutral-900">{{ number_format($walletTransactionStats['withdraw']['count']) }}</span>
                            <p class="text-xs text-neutral-500">Rp {{ number_format($walletTransactionStats['withdraw']['total_amount'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-lg border border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Top Performing Users</h3>
                <div class="space-y-3">
                    @foreach($topPerformingUsers->take(3) as $user)
                        <div class="flex justify-between items-center">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-neutral-900 truncate">{{ $user->name }}</p>
                                <p class="text-xs text-neutral-500">{{ $user->total_orders }} pesanan</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-neutral-900">Rp {{ number_format($user->total_revenue, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-xl shadow-lg border border-neutral-200">
                <div class="p-6 border-b border-neutral-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-neutral-900">Pickup Requests Terbaru</h3>
                        <a href="{{ route('admin.pickup-requests.index') }}" 
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
                                                <p class="text-xs text-neutral-500">
                                                    {{ $request->user->name }} → {{ $request->recipient_name }}
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

            <div class="bg-white rounded-xl shadow-lg border border-neutral-200">
                <div class="p-6 border-b border-neutral-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-neutral-900">Transaksi Wallet Terbaru</h3>
                        <a href="{{ route('admin.wallets.index') }}"
                           class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            Lihat Semua →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if($recentWalletTransactions->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentWalletTransactions as $transaction)
                                <div class="flex items-center justify-between p-4 bg-neutral-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                                {{ $transaction->type->value === 'topup' ? 'bg-success-100 text-success-600' : 
                                                   ($transaction->type->value === 'payment' ? 'bg-error-100 text-error-600' : 'bg-warning-100 text-warning-600') }}">
                                                <i class="fas fa-{{ $transaction->type->value === 'topup' ? 'plus' : ($transaction->type->value === 'payment' ? 'minus' : 'exchange-alt') }} text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-neutral-900 truncate">
                                                {{ $transaction->type_label }}
                                            </p>
                                            <p class="text-xs text-neutral-500">
                                                {{ $transaction->wallet->user->name }}
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
                            <p class="text-neutral-600">Belum ada transaksi wallet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-neutral-200">
            <div class="p-6 border-b border-neutral-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-neutral-900">Produk Terlaris</h3>
                    <a href="{{ route('admin.products.index') }}" 
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
                                    <th class="text-left py-3 px-4 text-sm font-medium text-neutral-600">Produk</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-neutral-600">Seller</th>
                                    <th class="text-right py-3 px-4 text-sm font-medium text-neutral-600">Harga</th>
                                    <th class="text-right py-3 px-4 text-sm font-medium text-neutral-600">Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bestSellingProducts as $product)
                                    <tr class="border-b border-neutral-100 hover:bg-neutral-50">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-neutral-900 truncate">{{ $product->name }}</p>
                                                    <p class="text-xs text-neutral-500 truncate">{{ Str::limit($product->description, 50) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <p class="text-sm text-neutral-900">{{ $product->user->name }}</p>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <p class="text-sm font-medium text-neutral-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <span class="bg-success-100 text-success-700 px-2 py-1 rounded-full text-xs font-medium">
                                                {{ number_format($product->total_sold) }} pcs
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-neutral-300 text-4xl mb-4">
                            <i class="fas fa-box"></i>
                        </div>
                        <p class="text-neutral-600">Belum ada produk terjual</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.plain-app>