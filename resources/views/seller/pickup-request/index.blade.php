<x-layouts.plain-app>
    <x-slot name="title">Daftar Pickup Request</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header with gradient background -->
        <div class="mb-8 p-6 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl text-white">
            <h1 class="text-2xl font-bold mb-2 flex items-center">
                <i class="fas fa-truck-pickup mr-3"></i>
                Pickup Request
            </h1>
            <p class="opacity-90">Kelola semua permintaan pickup Anda</p>
        </div>

        <!-- Stats Cards with vibrant colors -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Request -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-primary-100">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-primary-100 text-primary-600">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Total Request</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-warning-100">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-warning-100 text-warning-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Pending</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['pending']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Delivered -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-success-100">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-success-100 text-success-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Delivered</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['delivered']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-secondary-100">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-secondary-100 text-secondary-600">
                        <i class="fas fa-money-bill-wave text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-neutral-900">Rp
                            {{ number_format($revenue['total_revenue'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter with colorful accents -->
        <div class="bg-white rounded-xl shadow-lg mb-6 border border-neutral-100">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <form method="GET" action="{{ route('seller.pickup-request.index') }}"
                            class="flex flex-col sm:flex-row gap-4">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-neutral-400"></i>
                                </div>
                                <input type="text" name="search" value="{{ $search }}"
                                    placeholder="Cari pickup request..."
                                    class="w-full pl-10 pr-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                            </div>

                            <select name="status"
                                class="border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent px-4 py-2.5 transition">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed
                                </option>
                                <option value="pickup_scheduled" {{ $status === 'pickup_scheduled' ? 'selected' : '' }}>
                                    Pickup Scheduled</option>
                                <option value="picked_up" {{ $status === 'picked_up' ? 'selected' : '' }}>Picked Up
                                </option>
                                <option value="in_transit" {{ $status === 'in_transit' ? 'selected' : '' }}>In Transit
                                </option>
                                <option value="delivered" {{ $status === 'delivered' ? 'selected' : '' }}>Delivered
                                </option>
                                <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>

                            <button type="submit"
                                class="px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-md flex items-center justify-center">
                                <i class="fas fa-filter mr-2"></i>
                                Filter
                            </button>
                        </form>
                    </div>
                    <div>
                        <a href="{{ route('seller.pickup-request.create') }}"
                            class="px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-md flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>
                            Buat Pickup
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pickup Requests Table with colorful status badges -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-neutral-100">
            @if ($pickupRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Kode Pickup
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Penerima
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-200">
                            @foreach ($pickupRequests as $pickupRequest)
                                <tr class="hover:bg-neutral-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-neutral-900">
                                            {{ $pickupRequest->pickup_code }}
                                        </div>
                                        @if ($pickupRequest->courier_tracking_number)
                                            <div class="text-xs text-neutral-500 mt-1">
                                                <i class="fas fa-barcode mr-1"></i>
                                                {{ $pickupRequest->courier_tracking_number }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-neutral-900">
                                            {{ $pickupRequest->pickupAddress->name }}
                                        </div>
                                        <div class="text-xs text-neutral-500 mt-1">
                                            <i class="fas fa-phone mr-1"></i> {{ $pickupRequest->pickupAddress->phone }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-warning-100 text-warning-800',
                                                'confirmed' => 'bg-primary-100 text-primary-800',
                                                'pickup_scheduled' => 'bg-indigo-100 text-indigo-800',
                                                'picked_up' => 'bg-blue-100 text-blue-800',
                                                'in_transit' => 'bg-orange-100 text-orange-800',
                                                'delivered' => 'bg-success-100 text-success-800',
                                                'failed' => 'bg-error-100 text-error-800',
                                                'cancelled' => 'bg-neutral-100 text-neutral-800',
                                            ];
                                            $statusIcons = [
                                                'pending' => 'fa-clock',
                                                'confirmed' => 'fa-check-circle',
                                                'pickup_scheduled' => 'fa-calendar-check',
                                                'picked_up' => 'fa-truck-pickup',
                                                'in_transit' => 'fa-truck-moving',
                                                'delivered' => 'fa-check-double',
                                                'failed' => 'fa-times-circle',
                                                'cancelled' => 'fa-ban',
                                            ];
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$pickupRequest->status] ?? 'bg-neutral-100 text-neutral-800' }}">
                                            <i
                                                class="fas {{ $statusIcons[$pickupRequest->status] ?? 'fa-question-circle' }} mr-1"></i>
                                            {{ [
                                                'pending' => 'Pending',
                                                'confirmed' => 'Dikonfirmasi',
                                                'pickup_scheduled' => 'Dijadwalkan',
                                                'picked_up' => 'Diambil',
                                                'in_transit' => 'Dalam Perjalanan',
                                                'delivered' => 'Terkirim',
                                                'failed' => 'Gagal',
                                                'cancelled' => 'Dibatalkan',
                                            ][$pickupRequest->status] ?? ucfirst($pickupRequest->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-neutral-900">
                                            Rp {{ number_format($pickupRequest->total_amount, 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-neutral-500 mt-1">
                                            <i class="fas fa-boxes mr-1"></i> {{ $pickupRequest->items->count() }}
                                            item(s)
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $pickupRequest->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('seller.pickup-request.show', $pickupRequest->id) }}"
                                                class="text-primary-600 hover:text-primary-800 transition"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($pickupRequest->canBeCancelled())
                                                <form method="POST"
                                                    action="{{ route('seller.pickup-request.cancel', $pickupRequest->id) }}"
                                                    onsubmit="return confirm('Yakin ingin membatalkan pickup request ini?')"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="text-error-600 hover:text-error-800 transition"
                                                        title="Batal">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination would go here if needed -->
            @else
                <div class="p-12 text-center">
                    <div class="text-neutral-300 text-5xl mb-4">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <h3 class="text-lg font-medium text-neutral-900">Belum ada pickup request</h3>
                    <p class="mt-1 text-sm text-neutral-600">Mulai dengan membuat pickup request pertama Anda.</p>
                    <div class="mt-6">
                        <a href="{{ route('seller.pickup-request.create') }}"
                            class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-md">
                            <i class="fas fa-plus mr-2"></i>
                            Buat Pickup Request
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.plain-app>
