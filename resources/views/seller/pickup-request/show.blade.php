<x-layouts.plain-app>
    <x-slot name="title">Detail Pickup Request - {{ $pickupRequest->pickup_code }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900">Detail Pickup Request</h1>
                    <p class="mt-2 text-neutral-600">Kode: <span
                            class="font-mono bg-neutral-100 px-2 py-1 rounded">{{ $pickupRequest->pickup_code }}</span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('seller.pickup-request.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-neutral-600 text-white rounded-lg hover:bg-neutral-700 transition-colors shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    @if ($pickupRequest->canBeCancelled())
                        <a href="{{ route('seller.pickup-request.edit', $pickupRequest->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary-600 transition-colors shadow-sm">
                            <i class="fas fa-pen mr-2"></i>
                            Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="mb-8">
            @php
                $statusColors = [
                    'pending' => 'bg-warning-100 text-warning-800 border-warning-200',
                    'confirmed' => 'bg-secondary-100 text-secondary-800 border-secondary-200',
                    'pickup_scheduled' => 'bg-primary-100 text-primary-800 border-primary-200',
                    'picked_up' => 'bg-primary-200 text-primary-800 border-primary-300',
                    'in_transit' => 'bg-primary-300 text-primary-900 border-primary-400',
                    'delivered' => 'bg-success-100 text-success-800 border-success-200',
                    'failed' => 'bg-error-100 text-error-800 border-error-200',
                    'cancelled' => 'bg-neutral-100 text-neutral-800 border-neutral-200',
                ];
                $statusLabels = [
                    'pending' => 'Menunggu Konfirmasi',
                    'confirmed' => 'Dikonfirmasi',
                    'pickup_scheduled' => 'Dijadwalkan',
                    'picked_up' => 'Sudah Diambil',
                    'in_transit' => 'Dalam Perjalanan',
                    'delivered' => 'Terkirim',
                    'failed' => 'Gagal',
                    'cancelled' => 'Dibatalkan',
                ];
            @endphp
            <div
                class="inline-flex items-center px-4 py-2 rounded-lg border text-lg font-semibold {{ $statusColors[$pickupRequest->status] ?? 'bg-neutral-100 text-neutral-800 border-neutral-200' }}">
                <span class="mr-2">
                    @switch($pickupRequest->status)
                        @case('pending')
                            <i class="fas fa-clock"></i>
                        @break

                        @case('confirmed')
                            <i class="fas fa-check-circle"></i>
                        @break

                        @case('pickup_scheduled')
                            <i class="fas fa-calendar-check"></i>
                        @break

                        @case('picked_up')
                            <i class="fas fa-truck-pickup"></i>
                        @break

                        @case('in_transit')
                            <i class="fas fa-truck-moving"></i>
                        @break

                        @case('delivered')
                            <i class="fas fa-check-circle"></i>
                        @break

                        @case('failed')
                            <i class="fas fa-exclamation-circle"></i>
                        @break

                        @case('cancelled')
                            <i class="fas fa-ban"></i>
                        @break
                    @endswitch
                </span>
                {{ $statusLabels[$pickupRequest->status] ?? ucfirst($pickupRequest->status) }}
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-primary-500"></i>
                            Informasi Pickup
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <h4 class="text-sm font-medium text-neutral-700 flex items-center gap-1">
                                    <i class="fas fa-home text-primary-400"></i>
                                    Alamat Pickup
                                </h4>
                                <div class="bg-primary-50 rounded-lg p-4 border border-primary-100">
                                    <p class="font-medium text-neutral-900">{{ $pickupRequest->pickup_name }}</p>
                                    <p class="text-sm text-neutral-600 mt-1">
                                        <i class="fas fa-phone-alt text-primary-400 mr-1"></i>
                                        {{ $pickupRequest->pickup_phone }}
                                    </p>
                                    <p class="text-sm text-neutral-600 mt-2 flex items-start">
                                        <i class="fas fa-map-pin text-primary-400 mr-2 mt-1"></i>
                                        {{ $pickupRequest->full_pickup_address }}
                                    </p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <h4 class="text-sm font-medium text-neutral-700 flex items-center gap-1">
                                    <i class="fas fa-truck text-secondary-400"></i>
                                    Alamat Tujuan
                                </h4>
                                <div class="bg-secondary-50 rounded-lg p-4 border border-secondary-100">
                                    <p class="font-medium text-neutral-900">{{ $pickupRequest->recipientAddress->name }}</p>
                                    <p class="text-sm text-neutral-600 mt-1">
                                        <i class="fas fa-phone-alt text-secondary-400 mr-1"></i>
                                        {{ $pickupRequest->recipientAddress->phone }}
                                    </p>
                                    <p class="text-sm text-neutral-600 mt-2 flex items-start">
                                        <i class="fas fa-map-pin text-secondary-400 mr-2 mt-1"></i>
                                        {{ $pickupRequest->recipientAddress->full_address }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-boxes text-primary-500"></i>
                            Daftar Produk
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-neutral-200">
                                <thead class="bg-neutral-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                            Produk</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                            Qty</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                            Berat</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                            Harga</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                            Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-neutral-200">
                                    @foreach ($pickupRequest->items as $item)
                                        <tr class="hover:bg-neutral-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-neutral-900">
                                                    {{ $item->product->name }}</div>
                                                @if ($item->product->description)
                                                    <div class="text-xs text-neutral-500 mt-1">
                                                        {{ $item->product->description }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">
                                                {{ number_format($item->quantity) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">
                                                {{ number_format($item->total_weight, 2) }} kg
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">
                                                Rp {{ number_format($item->price_per_pcs, 0, ',', '.') }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                                                Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-history text-primary-500"></i>
                            Timeline
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex items-start">
                                            <div
                                                class="flex items-center justify-center w-10 h-10 bg-secondary-100 rounded-full ring-8 ring-white">
                                                <i class="fas fa-calendar-plus text-secondary-600"></i>
                                            </div>
                                            <div class="min-w-0 flex-1 pl-4">
                                                <div class="text-sm font-medium text-neutral-900">Request Dibuat</div>
                                                <div class="text-sm text-neutral-500 mt-1">
                                                    <i class="far fa-clock mr-1"></i>
                                                    {{ $pickupRequest->requested_at->format('d M Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                @if ($pickupRequest->pickup_scheduled_at)
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex items-start">
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 bg-primary-100 rounded-full ring-8 ring-white">
                                                    <i class="fas fa-calendar-check text-primary-600"></i>
                                                </div>
                                                <div class="min-w-0 flex-1 pl-4">
                                                    <div class="text-sm font-medium text-neutral-900">Pickup Dijadwalkan
                                                    </div>
                                                    <div class="text-sm text-neutral-500 mt-1">
                                                        <i class="far fa-clock mr-1"></i>
                                                        {{ $pickupRequest->pickup_scheduled_at->format('d M Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif

                                @if ($pickupRequest->picked_up_at)
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex items-start">
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 bg-primary-200 rounded-full ring-8 ring-white">
                                                    <i class="fas fa-truck-pickup text-primary-700"></i>
                                                </div>
                                                <div class="min-w-0 flex-1 pl-4">
                                                    <div class="text-sm font-medium text-neutral-900">Sudah Diambil
                                                    </div>
                                                    <div class="text-sm text-neutral-500 mt-1">
                                                        <i class="far fa-clock mr-1"></i>
                                                        {{ $pickupRequest->picked_up_at->format('d M Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif

                                @if ($pickupRequest->delivered_at)
                                    <li>
                                        <div class="relative">
                                            <div class="relative flex items-start">
                                                <div
                                                    class="flex items-center justify-center w-10 h-10 bg-success-100 rounded-full ring-8 ring-white">
                                                    <i class="fas fa-check-circle text-success-600"></i>
                                                </div>
                                                <div class="min-w-0 flex-1 pl-4">
                                                    <div class="text-sm font-medium text-neutral-900">Terkirim</div>
                                                    <div class="text-sm text-neutral-500 mt-1">
                                                        <i class="far fa-clock mr-1"></i>
                                                        {{ $pickupRequest->delivered_at->format('d M Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-receipt text-primary-500"></i>
                            Ringkasan Biaya
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-neutral-600">Subtotal Produk</span>
                                <span class="font-medium">Rp
                                    {{ number_format($pickupRequest->product_total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-neutral-600">Biaya Pengiriman</span>
                                <span class="font-medium">Rp
                                    {{ number_format($pickupRequest->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            @if ($pickupRequest->service_fee > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-neutral-600">Biaya Layanan</span>
                                    <span class="font-medium">Rp
                                        {{ number_format($pickupRequest->service_fee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="border-t border-neutral-200 pt-3 mt-3">
                                <div class="flex justify-between text-lg font-semibold">
                                    <span>Total</span>
                                    <span class="text-primary-600">Rp
                                        {{ number_format($pickupRequest->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-credit-card text-primary-500"></i>
                            Metode Pembayaran
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center">
                            @if ($pickupRequest->payment_method === 'cod')
                                <div class="p-3 bg-warning-100 rounded-lg mr-4 text-warning-600">
                                    <i class="fas fa-money-bill-wave text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-neutral-900">Cash on Delivery (COD)</div>
                                    <div class="text-xs text-neutral-500">Bayar saat terima</div>
                                </div>
                            @elseif($pickupRequest->payment_method === 'balance')
                                <div class="p-3 bg-secondary-100 rounded-lg mr-4 text-secondary-600">
                                    <i class="fas fa-wallet text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-neutral-900">Saldo</div>
                                    <div class="text-xs text-neutral-500">Dibayar dari saldo</div>
                                </div>
                            @else
                                <div class="p-3 bg-success-100 rounded-lg mr-4 text-success-600">
                                    <i class="fas fa-credit-card text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-neutral-900">
                                        {{ ucfirst($pickupRequest->payment_method) }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($pickupRequest->canBeCancelled())
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                            <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-bolt text-primary-500"></i>
                                Aksi
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @if ($pickupRequest->status === 'pending')
                                <form method="POST"
                                    action="{{ route('seller.pickup-request.confirm', $pickupRequest->id) }}"
                                    class="w-full">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-success text-white rounded-lg hover:bg-success-600 transition-colors shadow-sm flex items-center justify-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        Konfirmasi Request
                                    </button>
                                </form>
                            @endif

                            @if ($pickupRequest->status === 'confirmed')
                                <form method="POST"
                                    action="{{ route('seller.pickup-request.schedule', $pickupRequest->id) }}"
                                    class="w-full">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-neutral-700 mb-1">Jadwal
                                            Pickup</label>
                                        <input type="datetime-local" name="pickup_scheduled_at" required
                                            class="w-full rounded-lg border-neutral-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                            min="{{ now()->addMinutes(30)->format('Y-m-d\TH:i') }}">
                                    </div>
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors shadow-sm flex items-center justify-center gap-2">
                                        <i class="fas fa-calendar-check"></i>
                                        Jadwalkan Pickup
                                    </button>
                                </form>
                            @endif

                            <form method="POST"
                                action="{{ route('seller.pickup-request.cancel', $pickupRequest->id) }}"
                                onsubmit="return confirm('Yakin ingin membatalkan pickup request ini?')"
                                class="w-full">
                                @csrf
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-error text-white rounded-lg hover:bg-error-600 transition-colors shadow-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-times-circle"></i>
                                    Batalkan Request
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($pickupRequest->courier_tracking_number)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                            <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-truck text-primary-500"></i>
                                Tracking
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="text-sm flex items-center gap-2">
                                <span class="text-neutral-600">Nomor Resi:</span>
                                <span
                                    class="font-mono font-medium bg-neutral-100 px-2 py-1 rounded">{{ $pickupRequest->courier_tracking_number }}</span>
                            </div>
                            @if ($pickupRequest->courier_service)
                                <div class="text-sm mt-3 flex items-center gap-2">
                                    <span class="text-neutral-600">Kurir:</span>
                                    <span
                                        class="font-medium bg-secondary-100 text-secondary-800 px-2 py-1 rounded">{{ $pickupRequest->courier_service }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($pickupRequest->notes)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                            <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-primary-500"></i>
                                Catatan
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-neutral-700 bg-neutral-50 p-3 rounded-lg border border-neutral-200">
                                {{ $pickupRequest->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.plain-app>
