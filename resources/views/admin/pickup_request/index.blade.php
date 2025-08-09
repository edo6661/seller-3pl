<x-layouts.plain-app>
    <x-slot name="title">Admin - Pickup Requests</x-slot>

    <div class=" mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900">Pickup Requests Management</h1>
            <p class="mt-2 text-neutral-600">Kelola semua permintaan pickup dari seluruh pengguna</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <!-- Total Card -->
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

            <!-- Pending Card -->
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

            <!-- In Progress Card -->
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

            <!-- Delivered Card -->
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

            <!-- Revenue Card -->
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

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
            <div class="p-6 border-b border-neutral-200">
                <h2 class="text-lg font-semibold text-neutral-900 mb-4">
                    <i class="fas fa-search text-neutral-600 mr-2"></i>
                    Pencarian & Filter
                </h2>

                <form method="GET" action="{{ route('admin.pickup-requests.index') }}" class="space-y-4">
                    <!-- Search Input -->
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

                    <!-- Filters -->
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
                                <option value="balance" {{ $request->payment_method === 'balance' ? 'selected' : '' }}>
                                    Balance</option>
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

                    <!-- Filter Actions -->
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

        <!-- Pickup Requests Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if ($pickupRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-barcode mr-2"></i>
                                    Kode & User
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-user-check mr-2"></i>
                                    Penerima
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-user mr-2"></i>
                                    Pengirim
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Status
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Payment
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-money-bill mr-2"></i>
                                    Total
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-neutral-700 uppercase tracking-wider">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Tanggal
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
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-neutral-900">
                                            <i class="fas fa-user-check text-success mr-1"></i>
                                            {{ $pickupRequest->recipientAddress->name }}
                                        </div>
                                        <div class="text-sm text-neutral-600">
                                            <i class="fas fa-phone text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->recipientAddress->phone }}
                                        </div>
                                        <div class="text-xs text-neutral-500">
                                            <i class="fas fa-map-marker-alt text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->recipientAddress->city }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-neutral-900">
                                            <i class="fas fa-user text-primary mr-1"></i>
                                            {{ $pickupRequest->pickup_name }}
                                        </div>
                                        <div class="text-sm text-neutral-600">
                                            <i class="fas fa-phone text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->pickup_phone }}
                                        </div>
                                        <div class="text-xs text-neutral-500">
                                            <i class="fas fa-map-marker-alt text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->pickup_city }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                                            <i class="{{ $config['icon'] }} mr-1"></i>
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $paymentConfig = [
                                                'balance' => [
                                                    'bg' => 'bg-secondary-50',
                                                    'text' => 'text-secondary-700',
                                                    'border' => 'border-secondary-200',
                                                    'icon' => 'fas fa-wallet',
                                                ],
                                                'wallet' => [
                                                    'bg' => 'bg-success-50',
                                                    'text' => 'text-success-700',
                                                    'border' => 'border-success-200',
                                                    'icon' => 'fas fa-mobile-alt',
                                                ],
                                            ];
                                            $paymentCfg = $paymentConfig[$pickupRequest->payment_method] ?? [
                                                'bg' => 'bg-neutral-50',
                                                'text' => 'text-neutral-700',
                                                'border' => 'border-neutral-200',
                                                'icon' => 'fas fa-credit-card',
                                            ];
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border {{ $paymentCfg['bg'] }} {{ $paymentCfg['text'] }} {{ $paymentCfg['border'] }}">
                                            <i class="{{ $paymentCfg['icon'] }} mr-1"></i>
                                            {{ ucfirst($pickupRequest->payment_method) }}
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
                                        <div class="text-xs text-neutral-500">
                                            <i class="fas fa-tag text-neutral-400 mr-1"></i>
                                            Produk: Rp {{ number_format($pickupRequest->product_total, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->created_at->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-neutral-500 mt-1">
                                            <i class="fas fa-clock text-neutral-400 mr-1"></i>
                                            {{ $pickupRequest->created_at->format('H:i') }}
                                        </div>
                                        @if ($pickupRequest->pickup_scheduled_at)
                                            <div class="text-xs text-secondary-600 mt-1">
                                                <i class="fas fa-calendar-check text-secondary-400 mr-1"></i>
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
