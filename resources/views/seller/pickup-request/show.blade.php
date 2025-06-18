<x-layouts.plain-app>
    <x-slot name="title">Detail Pickup Request - {{ $pickupRequest->pickup_code }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Pickup Request</h1>
                    <p class="mt-2 text-gray-600">{{ $pickupRequest->pickup_code }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('seller.pickup-request.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    @if($pickupRequest->canBeCancelled())
                        <a href="{{ route('seller.pickup-request.edit', $pickupRequest->id) }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m18 9-4 4-3-3"/>
                            </svg>
                            Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-8">
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'pickup_scheduled' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'picked_up' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                    'in_transit' => 'bg-orange-100 text-orange-800 border-orange-200',
                    'delivered' => 'bg-green-100 text-green-800 border-green-200',
                    'failed' => 'bg-red-100 text-red-800 border-red-200',
                    'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
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
            <div class="inline-flex items-center px-4 py-2 rounded-lg border {{ $statusColors[$pickupRequest->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                <span class="text-lg font-semibold">
                    {{ $statusLabels[$pickupRequest->status] ?? ucfirst($pickupRequest->status) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Pickup Details -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Pickup</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Alamat Pickup</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="font-medium text-gray-900">{{ $pickupRequest->pickup_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $pickupRequest->pickup_phone }}</p>
                                    <p class="text-sm text-gray-600 mt-2">{{ $pickupRequest->full_pickup_address }}</p>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Alamat Tujuan</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="font-medium text-gray-900">{{ $pickupRequest->recipient_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $pickupRequest->recipient_phone }}</p>
                                    <p class="text-sm text-gray-600 mt-2">{{ $pickupRequest->full_recipient_address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items List -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Produk</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pickupRequest->items as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                                @if($item->product->description)
                                                    <div class="text-sm text-gray-500">{{ $item->product->description }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($item->quantity) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($item->total_weight, 2) }} kg
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                Rp {{ number_format($item->price_per_pcs, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Timeline</h3>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full">
                                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-900">Request Dibuat</div>
                                                <div class="text-sm text-gray-500">{{ $pickupRequest->requested_at->format('d M Y H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                
                                @if($pickupRequest->pickup_scheduled_at)
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-full">
                                                    <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-medium text-gray-900">Pickup Dijadwalkan</div>
                                                    <div class="text-sm text-gray-500">{{ $pickupRequest->pickup_scheduled_at->format('d M Y H:i') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif

                                @if($pickupRequest->picked_up_at)
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-full">
                                                    <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-medium text-gray-900">Sudah Diambil</div>
                                                    <div class="text-sm text-gray-500">{{ $pickupRequest->picked_up_at->format('d M Y H:i') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif

                                @if($pickupRequest->delivered_at)
                                    <li>
                                        <div class="relative">
                                            <div class="relative flex space-x-3">
                                                <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-full">
                                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-medium text-gray-900">Terkirim</div>
                                                    <div class="text-sm text-gray-500">{{ $pickupRequest->delivered_at->format('d M Y H:i') }}</div>
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

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Cost Summary -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Biaya</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal Produk</span>
                                <span class="font-medium">Rp {{ number_format($pickupRequest->product_total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Biaya Pengiriman</span>
                                <span class="font-medium">Rp {{ number_format($pickupRequest->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            @if($pickupRequest->service_fee > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Biaya Layanan</span>
                                    <span class="font-medium">Rp {{ number_format($pickupRequest->service_fee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="border-t pt-4">
                                <div class="flex justify-between text-lg font-semibold">
                                    <span>Total</span>
                                    <span>Rp {{ number_format($pickupRequest->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Metode Pembayaran</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center">
                            @if($pickupRequest->payment_method === 'cod')
                                <div class="p-2 bg-orange-100 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Cash on Delivery (COD)</div>
                                    <div class="text-sm text-gray-500">Bayar saat terima</div>
                                </div>
                            @elseif($pickupRequest->payment_method === 'balance')
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4z"/>
                                        <path d="M6 6h8v2H6V6z"/>
                                        <path d="M6 10h8v2H6v-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Saldo</div>
                                    <div class="text-sm text-gray-500">Dibayar dari saldo</div>
                                </div>
                            @else
                                <div class="p-2 bg-green-100 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ ucfirst($pickupRequest->payment_method) }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if($pickupRequest->canBeCancelled())
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Aksi</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @if($pickupRequest->status === 'pending')
                                <form method="POST" action="{{ route('seller.pickup-request.confirm', $pickupRequest->id) }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                        Konfirmasi Request
                                    </button>
                                </form>
                            @endif

                            @if($pickupRequest->status === 'confirmed')
                                <form method="POST" action="{{ route('seller.pickup-request.schedule', $pickupRequest->id) }}" class="w-full">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jadwal Pickup</label>
                                        <input type="datetime-local" name="pickup_scheduled_at" required 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               min="{{ now()->addMinutes(30)->format('Y-m-d\TH:i') }}"
                                               >
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                                        Jadwalkan Pickup
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('seller.pickup-request.cancel', $pickupRequest->id) }}" 
                                  onsubmit="return confirm('Yakin ingin membatalkan pickup request ini?')" class="w-full">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    Batalkan Request
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Tracking Info -->
                @if($pickupRequest->courier_tracking_number)
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Tracking</h3>
                        </div>
                        <div class="p-6">
                            <div class="text-sm">
                                <span class="text-gray-600">Nomor Resi:</span>
                                <span class="font-mono font-medium">{{ $pickupRequest->courier_tracking_number }}</span>
                            </div>
                            @if($pickupRequest->courier_service)
                                <div class="text-sm mt-2">
                                    <span class="text-gray-600">Kurir:</span>
                                    <span class="font-medium">{{ $pickupRequest->courier_service }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Notes -->
                @if($pickupRequest->notes)
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Catatan</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-700">{{ $pickupRequest->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-layouts.plain-app>