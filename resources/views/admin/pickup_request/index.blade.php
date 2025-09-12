<x-layouts.plain-app>
    <x-slot name="title">Admin - Pickup Requests</x-slot>
    <div x-data="pickupRequestManager()" class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900">Pickup Requests Management</h1>
            <p class="mt-2 text-neutral-600">Kelola semua permintaan pickup dari seluruh pengguna</p>
        </div>
        <div x-show="alertMessage" x-transition class="mb-6">
            <div :class="alertType === 'success' ? 'bg-success-50 border-success-200 text-success-700' : 'bg-error-50 border-error-200 text-error-700'" 
                 class="border-l-4 p-4 rounded-lg">
                <div class="flex items-center">
                    <i :class="alertType === 'success' ? 'fas fa-check-circle text-success-500' : 'fas fa-times-circle text-error-500'" class="mr-2"></i>
                    <span x-text="alertMessage"></span>
                    <button @click="clearAlert()" class="ml-auto">
                        <i class="fas fa-times text-neutral-400 hover:text-neutral-600"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-secondary-100 rounded-lg">
                        <i class="fas fa-clipboard-list text-secondary-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Total</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-warning-100 rounded-lg">
                        <i class="fas fa-clock text-warning-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Pending</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['pending']) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-secondary-100 rounded-lg">
                        <i class="fas fa-truck text-secondary-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">In Progress</p>
                        <p class="text-2xl font-bold text-neutral-900">
                            {{ number_format($stats['pickup_scheduled'] + $stats['picked_up'] + $stats['in_transit']) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-success-100 rounded-lg">
                        <i class="fas fa-check-circle text-success-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Delivered</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['delivered']) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-primary-100 rounded-lg">
                        <i class="fas fa-money-bill-wave text-primary-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Revenue</p>
                        <p class="text-2xl font-bold text-neutral-900">Rp
                            {{ number_format($revenue['total_amount'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
            <div class="p-6 border-b border-neutral-200">
                <h2 class="text-lg font-semibold text-neutral-900 mb-4">
                    <i class="fas fa-search text-neutral-600 mr-2"></i>
                    Pencarian & Filter
                </h2>
                <form method="GET" action="{{ route('admin.pickup-requests.index') }}" class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-neutral-400"></i>
                            </div>
                            <input type="text" name="search" value="{{ $request->search }}"
                                placeholder="Cari berdasarkan kode pickup, nama penerima, telepon, user..."
                                class="w-full pl-10 pr-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        </div>
                        <button type="submit"
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Cari
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Status</label>
                            <select name="status"
                                class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="confirmed" {{ $request->status === 'confirmed' ? 'selected' : '' }}>
                                    Confirmed</option>
                                <option value="pickup_scheduled"
                                    {{ $request->status === 'pickup_scheduled' ? 'selected' : '' }}>Pickup Scheduled
                                </option>
                                <option value="picked_up" {{ $request->status === 'picked_up' ? 'selected' : '' }}>
                                    Picked Up</option>
                                <option value="in_transit" {{ $request->status === 'in_transit' ? 'selected' : '' }}>In
                                    Transit</option>
                                <option value="delivered" {{ $request->status === 'delivered' ? 'selected' : '' }}>
                                    Delivered</option>
                                <option value="failed" {{ $request->status === 'failed' ? 'selected' : '' }}>Failed
                                </option>
                                <option value="cancelled" {{ $request->status === 'cancelled' ? 'selected' : '' }}>
                                    Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Payment Method</label>
                            <select name="payment_method"
                                class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="">Semua Payment</option>
                                <option value="cod" {{ $request->payment_method === 'cod' ? 'selected' : '' }}>
                                    COD</option>
                                <option value="wallet" {{ $request->payment_method === 'wallet' ? 'selected' : '' }}>
                                    Wallet</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ $request->date_from }}"
                                class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ $request->date_to }}"
                                class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit"
                            class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-secondary-500 transition-colors">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.pickup-requests.index') }}"
                            class="px-4 py-2 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 focus:outline-none focus:ring-2 focus:ring-neutral-500 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if ($pickupRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-barcode mr-2"></i>
                                    Kode & User
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-user-check mr-2"></i>
                                    Penerima
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-user mr-2"></i>
                                    Pengirim
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-truck mr-2"></i>
                                    Tipe & Status
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Payment
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-money-bill mr-2"></i>
                                    Total & Items
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Tanggal & Jadwal
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-cog mr-2"></i>
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-200">
                            @foreach ($pickupRequests as $pickupRequest)
                                <tr class="hover:bg-neutral-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-neutral-900">
                                            <i class="fas fa-barcode text-primary mr-2"></i>
                                            {{ $pickupRequest->pickup_code }}
                                        </div>
                                        <div class="text-sm text-neutral-600">
                                            <i class="fas fa-user text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->user->name ?? 'N/A' }}
                                        </div>
                                        @if ($pickupRequest->courier_tracking_number)
                                            <div class="text-xs text-secondary-600 mt-1">
                                                <i class="fas fa-shipping-fast text-secondary-400 mr-1"></i>
                                                {{ $pickupRequest->courier_tracking_number }}
                                            </div>
                                        @endif
                                        @if ($pickupRequest->courier_service)
                                            <div class="text-xs text-secondary-500 mt-1">
                                                <i class="fas fa-truck text-secondary-400 mr-1"></i>
                                                {{ $pickupRequest->courier_service }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-neutral-900">
                                            <i class="fas fa-user-check text-success mr-1"></i>
                                            {{ $pickupRequest->recipient_name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-neutral-600">
                                            <i class="fas fa-phone text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->recipient_phone ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-neutral-500 mt-1">
                                            <i class="fas fa-map-marker-alt text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->recipient_city ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-neutral-900">
                                            <i class="fas fa-user text-primary mr-1"></i>
                                            {{ $pickupRequest->pickupAddress->name ?? $pickupRequest->user->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-neutral-600">
                                            <i class="fas fa-phone text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->pickupAddress->phone ?? $pickupRequest->user->phone ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-neutral-500 mt-1">
                                            <i class="fas fa-map-marker-alt text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->pickupAddress->city ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="mb-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $pickupRequest->delivery_type->value === 'pickup' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                                <i class="fas {{ $pickupRequest->delivery_type->value === 'pickup' ? 'fa-truck-pickup' : 'fa-shipping-fast' }} mr-1"></i>
                                                {{ $pickupRequest->delivery_type->value === 'pickup' ? 'Pickup' : 'Drop Off' }}
                                            </span>
                                        </div>
                                        @php
                                            $statusConfig = [
                                                'pending' => [
                                                    'bg' => 'bg-warning-50',
                                                    'text' => 'text-warning-700',
                                                    'border' => 'border-warning-200',
                                                    'icon' => 'fas fa-clock',
                                                    'label' => 'Pending',
                                                ],
                                                'confirmed' => [
                                                    'bg' => 'bg-secondary-50',
                                                    'text' => 'text-secondary-700',
                                                    'border' => 'border-secondary-200',
                                                    'icon' => 'fas fa-check',
                                                    'label' => 'Dikonfirmasi',
                                                ],
                                                'pickup_scheduled' => [
                                                    'bg' => 'bg-secondary-50',
                                                    'text' => 'text-secondary-700',
                                                    'border' => 'border-secondary-200',
                                                    'icon' => 'fas fa-calendar-check',
                                                    'label' => 'Dijadwalkan',
                                                ],
                                                'picked_up' => [
                                                    'bg' => 'bg-primary-50',
                                                    'text' => 'text-primary-700',
                                                    'border' => 'border-primary-200',
                                                    'icon' => 'fas fa-hand-paper',
                                                    'label' => 'Diambil',
                                                ],
                                                'in_transit' => [
                                                    'bg' => 'bg-primary-50',
                                                    'text' => 'text-primary-700',
                                                    'border' => 'border-primary-200',
                                                    'icon' => 'fas fa-truck',
                                                    'label' => 'Dalam Perjalanan',
                                                ],
                                                'delivered' => [
                                                    'bg' => 'bg-success-50',
                                                    'text' => 'text-success-700',
                                                    'border' => 'border-success-200',
                                                    'icon' => 'fas fa-check-circle',
                                                    'label' => 'Terkirim',
                                                ],
                                                'failed' => [
                                                    'bg' => 'bg-error-50',
                                                    'text' => 'text-error-700',
                                                    'border' => 'border-error-200',
                                                    'icon' => 'fas fa-times-circle',
                                                    'label' => 'Gagal',
                                                ],
                                                'cancelled' => [
                                                    'bg' => 'bg-neutral-50',
                                                    'text' => 'text-neutral-700',
                                                    'border' => 'border-neutral-200',
                                                    'icon' => 'fas fa-ban',
                                                    'label' => 'Dibatalkan',
                                                ],
                                            ];
                                            $config = $statusConfig[$pickupRequest->status] ?? [
                                                'bg' => 'bg-neutral-50',
                                                'text' => 'text-neutral-700',
                                                'border' => 'border-neutral-200',
                                                'icon' => 'fas fa-question',
                                                'label' => ucfirst($pickupRequest->status),
                                            ];
                                        @endphp
                                        <span :id="'status-badge-{{ $pickupRequest->id }}'" class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                                            <i class="{{ $config['icon'] }} mr-1"></i>
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $paymentConfig = [
                                                'cod' => [
                                                    'bg' => 'bg-warning-50',
                                                    'text' => 'text-warning-700',
                                                    'border' => 'border-warning-200',
                                                    'icon' => 'fas fa-money-bill-wave',
                                                    'label' => 'COD'
                                                ],
                                                'wallet' => [
                                                    'bg' => 'bg-success-50',
                                                    'text' => 'text-success-700',
                                                    'border' => 'border-success-200',
                                                    'icon' => 'fas fa-wallet',
                                                    'label' => 'Wallet'
                                                ],
                                            ];
                                            $paymentCfg = $paymentConfig[$pickupRequest->payment_method] ?? [
                                                'bg' => 'bg-neutral-50',
                                                'text' => 'text-neutral-700',
                                                'border' => 'border-neutral-200',
                                                'icon' => 'fas fa-credit-card',
                                                'label' => ucfirst($pickupRequest->payment_method)
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border {{ $paymentCfg['bg'] }} {{ $paymentCfg['text'] }} {{ $paymentCfg['border'] }}">
                                            <i class="{{ $paymentCfg['icon'] }} mr-1"></i>
                                            {{ $paymentCfg['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-neutral-900">
                                            <i class="fas fa-money-bill text-primary mr-1"></i>
                                            Rp {{ number_format($pickupRequest->total_amount, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-neutral-600">
                                            <i class="fas fa-box text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->items->count() }} item(s)
                                        </div>
                                        <div class="text-xs text-neutral-500 mt-1">
                                            <i class="fas fa-tag text-neutral-400 mr-1"></i>
                                            Produk: Rp {{ number_format($pickupRequest->product_total, 0, ',', '.') }}
                                        </div>
                                        @if($pickupRequest->shipping_cost > 0)
                                            <div class="text-xs text-neutral-500">
                                                <i class="fas fa-truck text-neutral-400 mr-1"></i>
                                                Kirim: Rp {{ number_format($pickupRequest->shipping_cost, 0, ',', '.') }}
                                            </div>
                                        @endif
                                        @if($pickupRequest->service_fee > 0)
                                            <div class="text-xs text-neutral-500">
                                                <i class="fas fa-cogs text-neutral-400 mr-1"></i>
                                                Layanan: Rp {{ number_format($pickupRequest->service_fee, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                                        <div class="flex items-center mb-1">
                                            <i class="fas fa-calendar text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->created_at->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-neutral-500">
                                            <i class="fas fa-clock text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->created_at->format('H:i') }}
                                        </div>
                                        @if ($pickupRequest->pickup_scheduled_at)
                                            <div class="text-xs text-secondary-600 mt-2 bg-secondary-50 px-2 py-1 rounded">
                                                <i class="fas fa-calendar-check text-secondary-400 mr-1"></i>
                                                Jadwal: {{ $pickupRequest->pickup_scheduled_at->format('d M H:i') }}
                                            </div>
                                        @endif
                                        @if ($pickupRequest->picked_up_at)
                                            <div class="text-xs text-primary-600 mt-1">
                                                <i class="fas fa-truck-pickup text-primary-400 mr-1"></i>
                                                Diambil: {{ $pickupRequest->picked_up_at->format('d M H:i') }}
                                            </div>
                                        @endif
                                        @if ($pickupRequest->delivered_at)
                                            <div class="text-xs text-success-600 mt-1">
                                                <i class="fas fa-check-circle text-success-400 mr-1"></i>
                                                Terkirim: {{ $pickupRequest->delivered_at->format('d M H:i') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button @click="showDetail({{ $pickupRequest->id }})" 
                                                    class="text-secondary-600 hover:text-secondary-900 transition-colors">
                                                <i class="fas fa-eye" title="Lihat Detail"></i>
                                            </button>
                                            @if($pickupRequest->status !== 'delivered')
                                                <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" 
                                                        class="text-primary-600 hover:text-primary-900 transition-colors">
                                                    <i class="fas fa-edit" title="Update Status"></i>
                                                </button>
                                                <div x-show="open" @click.away="open = false" x-transition 
                                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-neutral-200">
                                                    <div class="py-1">
                                                        @if($pickupRequest->status === 'pending')
                                                            <button @click="confirmPickup({{ $pickupRequest->id }}); open = false" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                                                                <i class="fas fa-check text-success-500 mr-2"></i>
                                                                Konfirmasi
                                                            </button>
                                                        @endif
                                                        @if($pickupRequest->isPickupType() && $pickupRequest->status === 'confirmed')
                                                            <button @click="openScheduleModal({{ $pickupRequest->id }}); open = false" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                                                                <i class="fas fa-calendar-check text-secondary-500 mr-2"></i>
                                                                Jadwalkan Pickup
                                                            </button>
                                                        @endif
                                                        @if($pickupRequest->isPickupType() && in_array($pickupRequest->status, ['confirmed', 'pickup_scheduled']))
                                                            <button @click="openPickupModal({{ $pickupRequest->id }}); open = false" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                                                                <i class="fas fa-hand-paper text-primary-500 mr-2"></i>
                                                                Tandai Diambil
                                                            </button>
                                                        @endif
                                                        @if(($pickupRequest->isPickupType() && $pickupRequest->status === 'picked_up') || ($pickupRequest->isDropOffType() && $pickupRequest->status === 'confirmed'))
                                                            <button @click="markAsInTransit({{ $pickupRequest->id }}); open = false" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                                                                <i class="fas fa-truck text-primary-500 mr-2"></i>
                                                                Dalam Perjalanan
                                                            </button>
                                                        @endif
                                                        @if($pickupRequest->status === 'in_transit')
                                                            <button @click="markAsDelivered({{ $pickupRequest->id }}); open = false" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                                                                <i class="fas fa-check-circle text-success-500 mr-2"></i>
                                                                Tandai Terkirim
                                                            </button>
                                                            <button @click="openFailModal({{ $pickupRequest->id }}); open = false" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50">
                                                                <i class="fas fa-times-circle text-error-500 mr-2"></i>
                                                                Tandai Gagal
                                                            </button>
                                                        @endif
                                                        @if(in_array($pickupRequest->status, ['pending', 'confirmed']))
                                                            <div class="border-t border-neutral-200"></div>
                                                            <button @click="cancelPickup({{ $pickupRequest->id }}); open = false" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-error-700 hover:bg-error-50">
                                                                <i class="fas fa-ban text-error-500 mr-2"></i>
                                                                Batalkan
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-neutral-200 bg-neutral-50">
                    {{ $pickupRequests->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="mx-auto h-16 w-16 bg-neutral-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-clipboard-list text-neutral-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-neutral-900 mb-2">Tidak ada pickup request ditemukan</h3>
                    <p class="text-sm text-neutral-500 mb-4">
                        @if ($request->anyFilled(['search', 'status', 'payment_method', 'date_from', 'date_to']))
                            Coba ubah kriteria pencarian atau filter Anda.
                        @else
                            Belum ada pickup request yang dibuat.
                        @endif
                    </p>
                    @if ($request->anyFilled(['search', 'status', 'payment_method', 'date_from', 'date_to']))
                        <a href="{{ route('admin.pickup-requests.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
        <div x-show="scheduleModal.show" x-cloak class="fixed inset-0 bg-neutral-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-neutral-900 mb-4">Jadwalkan Pickup</h3>
                    <form @submit.prevent="schedulePickup()">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Tanggal dan Waktu Pickup</label>
                            <input type="datetime-local" x-model="scheduleModal.datetime" 
                                   class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   required>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" :disabled="loading"
                                    class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 disabled:opacity-50">
                                <span x-show="!loading">Jadwalkan</span>
                                <span x-show="loading">Memproses...</span>
                            </button>
                            <button type="button" @click="scheduleModal.show = false"
                                    class="flex-1 px-4 py-2 bg-neutral-300 text-neutral-700 rounded-lg hover:bg-neutral-400">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div x-show="pickupModal.show" x-cloak class="fixed inset-0 bg-neutral-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-neutral-900 mb-4">Tandai Diambil</h3>
                    <form @submit.prevent="markAsPickedUp()">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Nomor Resi (Opsional)</label>
                            <input type="text" x-model="pickupModal.trackingNumber" 
                                   class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Masukkan nomor resi...">
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" :disabled="loading"
                                    class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 disabled:opacity-50">
                                <span x-show="!loading">Tandai Diambil</span>
                                <span x-show="loading">Memproses...</span>
                            </button>
                            <button type="button" @click="pickupModal.show = false"
                                    class="flex-1 px-4 py-2 bg-neutral-300 text-neutral-700 rounded-lg hover:bg-neutral-400">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div x-show="failModal.show" x-cloak class="fixed inset-0 bg-neutral-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-neutral-900 mb-4">Tandai Gagal</h3>
                    <form @submit.prevent="markAsFailed()">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Alasan Kegagalan</label>
                            <textarea x-model="failModal.reason" rows="3"
                                      class="w-full px-3 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="Masukkan alasan kegagalan pengiriman..."></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" :disabled="loading"
                                    class="flex-1 px-4 py-2 bg-error text-white rounded-lg hover:bg-error-600 disabled:opacity-50">
                                <span x-show="!loading">Tandai Gagal</span>
                                <span x-show="loading">Memproses...</span>
                            </button>
                            <button type="button" @click="failModal.show = false"
                                    class="flex-1 px-4 py-2 bg-neutral-300 text-neutral-700 rounded-lg hover:bg-neutral-400">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div x-show="detailModal.show" x-cloak class="fixed inset-0 bg-neutral-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-neutral-900">Detail Pickup Request</h3>
                    <button @click="detailModal.show = false" class="text-neutral-400 hover:text-neutral-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div x-show="detailModal.loading" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-neutral-400"></i>
                    <p class="text-neutral-600 mt-2">Memuat detail...</p>
                </div>
                <div x-show="!detailModal.loading && detailModal.data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="font-semibold text-neutral-900 border-b pb-2">Informasi Umum</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-neutral-600">Kode Pickup:</span>
                                    <span class="font-medium" x-text="detailModal.data?.pickup_code"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600">Status:</span>
                                    <span class="font-medium" x-text="detailModal.data?.status"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600">Tipe Pengiriman:</span>
                                    <span class="font-medium" x-text="detailModal.data?.delivery_type"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600">Metode Pembayaran:</span>
                                    <span class="font-medium" x-text="detailModal.data?.payment_method"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600">Total Amount:</span>
                                    <span class="font-medium" x-text="'Rp ' + (detailModal.data?.total_amount ? Number(detailModal.data.total_amount).toLocaleString('id-ID') : '0')"></span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h4 class="font-semibold text-neutral-900 border-b pb-2">Informasi Penerima</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-neutral-600">Nama:</span>
                                    <span class="font-medium" x-text="detailModal.data?.recipient_name"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-600">Telepon:</span>
                                    <span class="font-medium" x-text="detailModal.data?.recipient_phone"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-neutral-600">Alamat:</span>
                                    <p class="font-medium mt-1" x-text="detailModal.data?.recipient_address"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h4 class="font-semibold text-neutral-900 border-b pb-2">Informasi Pengirim</h4>
                        <div class="text-sm space-y-2">
                            <div class="flex justify-between">
                                <span class="text-neutral-600">User:</span>
                                <span class="font-medium" x-text="detailModal.data?.user?.name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-neutral-600">Email:</span>
                                <span class="font-medium" x-text="detailModal.data?.user?.email"></span>
                            </div>
                            <div x-show="detailModal.data?.pickup_address" class="text-sm">
                                <span class="text-neutral-600">Alamat Pickup:</span>
                                <p class="font-medium mt-1" x-text="detailModal.data?.pickup_address"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function pickupRequestManager() {
            return {
                loading: false,
                alertMessage: '',
                alertType: 'success',
                // Modal states
                scheduleModal: {
                    show: false,
                    id: null,
                    datetime: ''
                },
                pickupModal: {
                    show: false,
                    id: null,
                    trackingNumber: ''
                },
                failModal: {
                    show: false,
                    id: null,
                    reason: ''
                },
                detailModal: {
                    show: false,
                    loading: false,
                    data: null
                },
                // Show alert
                showAlert(message, type = 'success') {
                    this.alertMessage = message;
                    this.alertType = type;
                    // Auto hide after 5 seconds
                    setTimeout(() => {
                        this.clearAlert();
                    }, 5000);
                },
                // Clear alert
                clearAlert() {
                    this.alertMessage = '';
                },
                // API call helper
                async apiCall(url, data = {}) {
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify(data)
                        });
                        const result = await response.json();
                        if (!response.ok) {
                            throw new Error(result.message || 'Terjadi kesalahan');
                        }
                        return result;
                    } catch (error) {
                        throw new Error(error.message || 'Terjadi kesalahan jaringan');
                    }
                },
                // Update status badge in table
                updateStatusBadge(id, status) {
                    const badge = document.querySelector(`#status-badge-${id}`);
                    if (badge) {
                        const statusConfigs = {
                            'pending': { class: 'bg-warning-50 text-warning-700 border-warning-200', icon: 'fas fa-clock', label: 'Pending' },
                            'confirmed': { class: 'bg-secondary-50 text-secondary-700 border-secondary-200', icon: 'fas fa-check', label: 'Dikonfirmasi' },
                            'pickup_scheduled': { class: 'bg-secondary-50 text-secondary-700 border-secondary-200', icon: 'fas fa-calendar-check', label: 'Dijadwalkan' },
                            'picked_up': { class: 'bg-primary-50 text-primary-700 border-primary-200', icon: 'fas fa-hand-paper', label: 'Diambil' },
                            'in_transit': { class: 'bg-primary-50 text-primary-700 border-primary-200', icon: 'fas fa-truck', label: 'Dalam Perjalanan' },
                            'delivered': { class: 'bg-success-50 text-success-700 border-success-200', icon: 'fas fa-check-circle', label: 'Terkirim' },
                            'failed': { class: 'bg-error-50 text-error-700 border-error-200', icon: 'fas fa-times-circle', label: 'Gagal' },
                            'cancelled': { class: 'bg-neutral-50 text-neutral-700 border-neutral-200', icon: 'fas fa-ban', label: 'Dibatalkan' }
                        };
                        const config = statusConfigs[status];
                        if (config) {
                            badge.className = `inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border ${config.class}`;
                            badge.innerHTML = `<i class="${config.icon} mr-1"></i>${config.label}`;
                        }
                    }
                },
                // Confirm pickup request
                async confirmPickup(id) {
                    if (!confirm('Apakah Anda yakin ingin mengkonfirmasi pickup request ini?')) {
                        return;
                    }
                    this.loading = true;
                    try {
                        const result = await this.apiCall(`/admin/pickup-requests/${id}/confirm`);
                        this.showAlert(result.message);
                        this.updateStatusBadge(id, 'confirmed');
                        // Refresh page after 2 seconds
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                // Open schedule modal
                openScheduleModal(id) {
                    this.scheduleModal.id = id;
                    this.scheduleModal.datetime = '';
                    this.scheduleModal.show = true;
                },
                // Schedule pickup
                async schedulePickup() {
                    if (!this.scheduleModal.datetime) {
                        this.showAlert('Tanggal dan waktu pickup harus diisi', 'error');
                        return;
                    }
                    this.loading = true;
                    try {
                        const result = await this.apiCall(`/admin/pickup-requests/${this.scheduleModal.id}/schedule-pickup`, {
                            pickup_scheduled_at: this.scheduleModal.datetime
                        });
                        this.showAlert(result.message);
                        this.updateStatusBadge(this.scheduleModal.id, 'pickup_scheduled');
                        this.scheduleModal.show = false;
                        // Refresh page after 2 seconds
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                // Open pickup modal
                openPickupModal(id) {
                    this.pickupModal.id = id;
                    this.pickupModal.trackingNumber = '';
                    this.pickupModal.show = true;
                },
                // Mark as picked up
                async markAsPickedUp() {
                    this.loading = true;
                    try {
                        const data = {};
                        if (this.pickupModal.trackingNumber) {
                            data.courier_tracking_number = this.pickupModal.trackingNumber;
                        }
                        const result = await this.apiCall(`/admin/pickup-requests/${this.pickupModal.id}/mark-picked-up`, data);
                        this.showAlert(result.message);
                        this.updateStatusBadge(this.pickupModal.id, 'picked_up');
                        this.pickupModal.show = false;
                        // Refresh page after 2 seconds
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                // Mark as in transit
                async markAsInTransit(id) {
                    if (!confirm('Apakah Anda yakin paket sedang dalam perjalanan?')) {
                        return;
                    }
                    this.loading = true;
                    try {
                        const result = await this.apiCall(`/admin/pickup-requests/${id}/mark-in-transit`);
                        this.showAlert(result.message);
                        this.updateStatusBadge(id, 'in_transit');
                        // Refresh page after 2 seconds
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                // Mark as delivered
                async markAsDelivered(id) {
                    if (!confirm('Apakah Anda yakin paket sudah terkirim?')) {
                        return;
                    }
                    this.loading = true;
                    try {
                        const result = await this.apiCall(`/admin/pickup-requests/${id}/mark-delivered`);
                        this.showAlert(result.message);
                        this.updateStatusBadge(id, 'delivered');
                        // Refresh page after 2 seconds
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                // Open fail modal
                openFailModal(id) {
                    this.failModal.id = id;
                    this.failModal.reason = '';
                    this.failModal.show = true;
                },
                // Mark as failed
                async markAsFailed() {
                    this.loading = true;
                    try {
                        const data = {};
                        if (this.failModal.reason) {
                            data.failure_reason = this.failModal.reason;
                        }
                        const result = await this.apiCall(`/admin/pickup-requests/${this.failModal.id}/mark-failed`, data);
                        this.showAlert(result.message);
                        this.updateStatusBadge(this.failModal.id, 'failed');
                        this.failModal.show = false;
                        // Refresh page after 2 seconds
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                // Cancel pickup
                async cancelPickup(id) {
                    if (!confirm('Apakah Anda yakin ingin membatalkan pickup request ini?')) {
                        return;
                    }
                    this.loading = true;
                    try {
                        const result = await this.apiCall(`/admin/pickup-requests/${id}/cancel`);
                        this.showAlert(result.message);
                        this.updateStatusBadge(id, 'cancelled');
                        // Refresh page after 2 seconds
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                // Show detail
                async showDetail(id) {
                    this.detailModal.show = true;
                    this.detailModal.loading = true;
                    this.detailModal.data = null;
                    try {
                        const response = await fetch(`/admin/pickup-requests/${id}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        });
                        const result = await response.json();
                        if (!response.ok) {
                            throw new Error(result.message || 'Gagal memuat detail');
                        }
                        this.detailModal.data = result.data;
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                        this.detailModal.show = false;
                    } finally {
                        this.detailModal.loading = false;
                    }
                }
            };
        }
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        // Add smooth scroll and highlight effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to stats cards
            const statsCards = document.querySelectorAll('.grid > div');
            statsCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate-fade-in');
            });
            // Add hover effects to table rows
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(4px)';
                    this.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                    this.style.boxShadow = 'none';
                });
            });
        });
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fade-in 0.5s ease-out forwards;
        }
        tbody tr {
            transition: all 0.2s ease;
        }
        .bg-primary {
            background-color: var(--color-primary);
        }
        .bg-primary-50 {
            background-color: var(--color-primary-50);
        }
        .bg-primary-100 {
            background-color: var(--color-primary-100);
        }
        .bg-primary-600 {
            background-color: var(--color-primary-600);
        }
        .text-primary {
            color: var(--color-primary);
        }
        .text-primary-700 {
            color: var(--color-primary-700);
        }
        .border-primary-200 {
            border-color: var(--color-primary-200);
        }
        .focus\:ring-primary-500:focus {
            --tw-ring-color: var(--color-primary-500);
        }
        .focus\:border-primary-500:focus {
            border-color: var(--color-primary-500);
        }
        .hover\:bg-primary-600:hover {
            background-color: var(--color-primary-600);
        }
        .bg-secondary {
            background-color: var(--color-secondary);
        }
        .bg-secondary-50 {
            background-color: var(--color-secondary-50);
        }
        .bg-secondary-100 {
            background-color: var(--color-secondary-100);
        }
        .bg-secondary-600 {
            background-color: var(--color-secondary-600);
        }
        .text-secondary-600 {
            color: var(--color-secondary-600);
        }
        .text-secondary-700 {
            color: var(--color-secondary-700);
        }
        .border-secondary-200 {
            border-color: var(--color-secondary-200);
        }
        .focus\:ring-secondary-500:focus {
            --tw-ring-color: var(--color-secondary-500);
        }
        .hover\:bg-secondary-600:hover {
            background-color: var(--color-secondary-600);
        }
        .bg-success-50 {
            background-color: var(--color-success-50);
        }
        .bg-success-100 {
            background-color: var(--color-success-100);
        }
        .text-success-600 {
            color: var(--color-success-600);
        }
        .text-success-700 {
            color: var(--color-success-700);
        }
        .border-success-200 {
            border-color: var(--color-success-200);
        }
        .bg-warning-50 {
            background-color: var(--color-warning-50);
        }
        .bg-warning-100 {
            background-color: var(--color-warning-100);
        }
        .text-warning-600 {
            color: var(--color-warning-600);
        }
        .text-warning-700 {
            color: var(--color-warning-700);
        }
        .border-warning-200 {
            border-color: var(--color-warning-200);
        }
        .bg-error-50 {
            background-color: var(--color-error-50);
        }
        .text-error-700 {
            color: var(--color-error-700);
        }
        .border-error-200 {
            border-color: var(--color-error-200);
        }
        .text-neutral-400 {
            color: var(--color-neutral-400);
        }
        .text-neutral-500 {
            color: var(--color-neutral-500);
        }
        .text-neutral-600 {
            color: var(--color-neutral-600);
        }
        .text-neutral-700 {
            color: var(--color-neutral-700);
        }
        .text-neutral-900 {
            color: var(--color-neutral-900);
        }
        .bg-neutral-50 {
            background-color: var(--color-neutral-50);
        }
        .bg-neutral-100 {
            background-color: var(--color-neutral-100);
        }
        .bg-neutral-200 {
            background-color: var(--color-neutral-200);
        }
        .bg-neutral-300 {
            background-color: var(--color-neutral-300);
        }
        .border-neutral-200 {
            border-color: var(--color-neutral-200);
        }
        .border-neutral-300 {
            border-color: var(--color-neutral-300);
        }
        .divide-neutral-200> :not([hidden])~ :not([hidden]) {
            border-color: var(--color-neutral-200);
        }
        .focus\:ring-neutral-500:focus {
            --tw-ring-color: var(--color-neutral-500);
        }
        .hover\:bg-neutral-50:hover {
            background-color: var(--color-neutral-50);
        }
        .hover\:bg-neutral-300:hover {
            background-color: var(--color-neutral-300);
        }
    </style>
</x-layouts.plain-app>