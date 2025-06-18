<x-layouts.plain-app>
    <x-slot name="title">Admin - Pickup Requests</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pickup Requests Management</h1>
            <p class="mt-2 text-gray-600">Kelola semua permintaan pickup dari seluruh pengguna</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V8a1 1 0 011-1h4z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">In Progress</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pickup_scheduled'] + $stats['picked_up'] + $stats['in_transit']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Delivered</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['delivered']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($revenue['total_amount'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.pickup-requests.index') }}" class="space-y-4">
                    <!-- Search Input -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $request->search }}" 
                                   placeholder="Cari berdasarkan kode pickup, nama penerima, telepon, user..." 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Cari
                        </button>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $request->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="pickup_scheduled" {{ $request->status === 'pickup_scheduled' ? 'selected' : '' }}>Pickup Scheduled</option>
                                <option value="picked_up" {{ $request->status === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                                <option value="in_transit" {{ $request->status === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="delivered" {{ $request->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="failed" {{ $request->status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="cancelled" {{ $request->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select name="payment_method" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Payment</option>
                                <option value="balance" {{ $request->payment_method === 'balance' ? 'selected' : '' }}>Balance</option>
                                <option value="wallet" {{ $request->payment_method === 'wallet' ? 'selected' : '' }}>Wallet</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" 
                                   name="date_from" 
                                   value="{{ $request->date_from }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" 
                                   name="date_to" 
                                   value="{{ $request->date_to }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('admin.pickup-requests.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pickup Requests Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($pickupRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode & User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Penerima
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pengirim
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payment
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pickupRequests as $pickupRequest)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $pickupRequest->pickup_code }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $pickupRequest->user->name ?? 'N/A' }}
                                        </div>
                                        @if($pickupRequest->courier_tracking_number)
                                            <div class="text-xs text-blue-600">
                                                {{ $pickupRequest->courier_tracking_number }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $pickupRequest->recipient_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $pickupRequest->recipient_phone }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $pickupRequest->recipient_city }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $pickupRequest->pickup_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $pickupRequest->pickup_phone }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $pickupRequest->pickup_city }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'confirmed' => 'bg-blue-100 text-blue-800',
                                                'pickup_scheduled' => 'bg-purple-100 text-purple-800',
                                                'picked_up' => 'bg-indigo-100 text-indigo-800',
                                                'in_transit' => 'bg-orange-100 text-orange-800',
                                                'delivered' => 'bg-green-100 text-green-800',
                                                'failed' => 'bg-red-100 text-red-800',
                                                'cancelled' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'confirmed' => 'Dikonfirmasi',
                                                'pickup_scheduled' => 'Dijadwalkan',
                                                'picked_up' => 'Diambil',
                                                'in_transit' => 'Dalam Perjalanan',
                                                'delivered' => 'Terkirim',
                                                'failed' => 'Gagal',
                                                'cancelled' => 'Dibatalkan',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$pickupRequest->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$pickupRequest->status] ?? ucfirst($pickupRequest->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $paymentColors = [
                                                'balance' => 'bg-blue-100 text-blue-800',
                                                'wallet' => 'bg-green-100 text-green-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $paymentColors[$pickupRequest->payment_method] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($pickupRequest->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            Rp {{ number_format($pickupRequest->total_amount, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $pickupRequest->items->count() }} item(s)
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            Produk: Rp {{ number_format($pickupRequest->product_total, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $pickupRequest->created_at->format('d M Y') }}</div>
                                        <div class="text-xs">{{ $pickupRequest->created_at->format('H:i') }}</div>
                                        @if($pickupRequest->pickup_scheduled_at)
                                            <div class="text-xs text-purple-600">
                                                Jadwal: {{ $pickupRequest->pickup_scheduled_at->format('d M H:i') }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pickupRequests->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pickup request ditemukan</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($request->anyFilled(['search', 'status', 'payment_method', 'date_from', 'date_to']))
                            Coba ubah kriteria pencarian atau filter Anda.
                        @else
                            Belum ada pickup request yang dibuat.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</x-layouts.plain-app>