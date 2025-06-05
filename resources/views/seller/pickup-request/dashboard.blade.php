<x-layouts.plain-app>
    <x-slot name="title">Dashboard Pickup Request</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Pickup Request</h1>
            <p class="mt-2 text-gray-600">Ringkasan dan statistik dari aktivitas pickup request Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Request</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Terkirim</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['delivered']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
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
                    <div class="p-3 bg-emerald-100 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($revenue['total_revenue'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Statistik Bulanan ({{ now()->year }})</h2>
                <div class="h-80 md:h-96">
                    <canvas id="monthlyStatsChart"></canvas>
                    <div id="chartData"
                         data-labels="{{ json_encode(collect($monthlyStats)->pluck('month_name')) }}"
                         data-orders="{{ json_encode(collect($monthlyStats)->pluck('total_orders')) }}"
                         data-revenue="{{ json_encode(collect($monthlyStats)->pluck('total_revenue')) }}">
                    </div>
                    {{-- <p class="text-center text-gray-500 mt-16">Grafik statistik bulanan akan ditampilkan di sini.</p> --}}
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Pickup Request Terbaru</h2>
                    <a href="{{ route('seller.pickup-request.index') }}" class="mt-2 sm:mt-0 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        Lihat Semua &rarr;
                    </a>
                </div>
            </div>
            @if($recentPickupRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode Pickup
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Penerima
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentPickupRequests as $pickupRequest)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $pickupRequest->pickup_code }}
                                        </div>
                                        @if($pickupRequest->courier_tracking_number)
                                            <div class="text-xs text-gray-500">
                                                {{ $pickupRequest->courier_tracking_number }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $pickupRequest->recipient_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $pickupRequest->recipient_phone }}
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
                                        <div class="text-sm font-medium text-gray-900">
                                            Rp {{ number_format($pickupRequest->total_amount, 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $pickupRequest->items->count() }} item(s)
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $pickupRequest->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('seller.pickup-request.show', $pickupRequest->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pickup request terbaru</h3>
                    <p class="mt-1 text-sm text-gray-500">Saat ada pickup request baru, akan muncul di sini.</p>
                    <div class="mt-6">
                        <a href="{{ route('seller.pickup-request.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Buat Pickup Request Baru
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartDataEl = document.getElementById('chartData');
            if (chartDataEl) {
                const labels = JSON.parse(chartDataEl.dataset.labels);
                const ordersData = JSON.parse(chartDataEl.dataset.orders);
                const revenueData = JSON.parse(chartDataEl.dataset.revenue);

                const ctx = document.getElementById('monthlyStatsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar', // atau 'line'
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Total Orders',
                                data: ordersData,
                                backgroundColor: 'rgba(54, 162, 235, 0.6)', // Biru
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                yAxisID: 'yOrders',
                            },
                            {
                                label: 'Total Revenue (Rp)',
                                data: revenueData,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)', // Hijau
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1,
                                yAxisID: 'yRevenue',
                                type: 'line', // bisa dikombinasikan
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            yOrders: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Total Orders'
                                },
                                beginAtZero: true
                            },
                            yRevenue: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Total Revenue (Rp)'
                                },
                                grid: {
                                    drawOnChartArea: false, // hanya garis grid untuk yOrders
                                },
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value, index, values) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            if (context.dataset.yAxisID === 'yRevenue') {
                                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                            } else {
                                                label += context.parsed.y;
                                            }
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Script untuk auto-hide notifikasi
            const successAlert = document.querySelector('.bg-green-100');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 5000);
            }

            const errorAlert = document.querySelector('.bg-red-100');
            if (errorAlert) {
                setTimeout(() => {
                    errorAlert.style.display = 'none';
                }, 5000);
            }
        });
    </script>
    @endpush

</x-layouts.plain-app>