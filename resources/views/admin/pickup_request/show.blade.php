<x-layouts.plain-app>
    <x-slot name="title">Admin - Detail Pickup Request</x-slot>
    <div x-data="pickupRequestDetail({{ $pickupRequest->id }})" class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.pickup-requests.index') }}" 
                       class="text-neutral-600 hover:text-primary-600 transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-neutral-900">Detail Pickup Request</h1>
                        <p class="mt-2 text-neutral-600">{{ $pickupRequest->pickup_code }}</p>
                    </div>
                </div>
                <div>
                    @php
                        $statusConfig = [
                            'pending' => ['bg' => 'bg-warning-50', 'text' => 'text-warning-700', 'border' => 'border-warning-200', 'icon' => 'fas fa-clock', 'label' => 'Pending'],
                            'confirmed' => ['bg' => 'bg-secondary-50', 'text' => 'text-secondary-700', 'border' => 'border-secondary-200', 'icon' => 'fas fa-check', 'label' => 'Dikonfirmasi'],
                            'pickup_scheduled' => ['bg' => 'bg-secondary-50', 'text' => 'text-secondary-700', 'border' => 'border-secondary-200', 'icon' => 'fas fa-calendar-check', 'label' => 'Dijadwalkan'],
                            'picked_up' => ['bg' => 'bg-primary-50', 'text' => 'text-primary-700', 'border' => 'border-primary-200', 'icon' => 'fas fa-hand-paper', 'label' => 'Diambil'],
                            'in_transit' => ['bg' => 'bg-primary-50', 'text' => 'text-primary-700', 'border' => 'border-primary-200', 'icon' => 'fas fa-truck', 'label' => 'Dalam Perjalanan'],
                            'delivered' => ['bg' => 'bg-success-50', 'text' => 'text-success-700', 'border' => 'border-success-200', 'icon' => 'fas fa-check-circle', 'label' => 'Terkirim'],
                            'failed' => ['bg' => 'bg-error-50', 'text' => 'text-error-700', 'border' => 'border-error-200', 'icon' => 'fas fa-times-circle', 'label' => 'Gagal'],
                            'cancelled' => ['bg' => 'bg-neutral-50', 'text' => 'text-neutral-700', 'border' => 'border-neutral-200', 'icon' => 'fas fa-ban', 'label' => 'Dibatalkan'],
                        ];
                        $config = $statusConfig[$pickupRequest->status] ?? ['bg' => 'bg-neutral-50', 'text' => 'text-neutral-700', 'border' => 'border-neutral-200', 'icon' => 'fas fa-question', 'label' => ucfirst($pickupRequest->status)];
                    @endphp
                    <span id="status-badge" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-full border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                        <i class="{{ $config['icon'] }} mr-2"></i>
                        {{ $config['label'] }}
                    </span>
                </div>
            </div>
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900">
                            <i class="fas fa-info-circle text-primary mr-2"></i>
                            Informasi Umum
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-neutral-700">Kode Pickup</label>
                                <p class="text-lg font-bold text-primary">{{ $pickupRequest->pickup_code }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-neutral-700">Tipe Pengiriman</label>
                                <p class="text-sm">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $pickupRequest->delivery_type->value === 'pickup' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                        <i class="fas {{ $pickupRequest->delivery_type->value === 'pickup' ? 'fa-truck-pickup' : 'fa-shipping-fast' }} mr-1"></i>
                                        {{ $pickupRequest->delivery_type->label() }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-neutral-700">Metode Pembayaran</label>
                                <p class="text-sm">
                                    @php
                                        $paymentConfig = [
                                            'cod' => ['bg' => 'bg-warning-50', 'text' => 'text-warning-700', 'border' => 'border-warning-200', 'icon' => 'fas fa-money-bill-wave', 'label' => 'COD'],
                                            'wallet' => ['bg' => 'bg-success-50', 'text' => 'text-success-700', 'border' => 'border-success-200', 'icon' => 'fas fa-wallet', 'label' => 'Wallet'],
                                        ];
                                        $paymentCfg = $paymentConfig[$pickupRequest->payment_method] ?? ['bg' => 'bg-neutral-50', 'text' => 'text-neutral-700', 'border' => 'border-neutral-200', 'icon' => 'fas fa-credit-card', 'label' => ucfirst($pickupRequest->payment_method)];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border {{ $paymentCfg['bg'] }} {{ $paymentCfg['text'] }} {{ $paymentCfg['border'] }}">
                                        <i class="{{ $paymentCfg['icon'] }} mr-1"></i>
                                        {{ $paymentCfg['label'] }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-neutral-700">Layanan Kurir</label>
                                <p class="text-sm text-neutral-900">{{ $pickupRequest->courier_service ?? '-' }}</p>
                            </div>
                            @if($pickupRequest->courier_tracking_number)
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-neutral-700">Nomor Resi</label>
                                <p class="text-sm text-neutral-900 font-mono">{{ $pickupRequest->courier_tracking_number }}</p>
                            </div>
                            @endif
                            @if($pickupRequest->notes)
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-neutral-700">Catatan</label>
                                <p class="text-sm text-neutral-900">{{ $pickupRequest->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900">
                            <i class="fas fa-user-check text-success mr-2"></i>
                            Informasi Penerima
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-neutral-700">Nama Penerima</label>
                                <p class="text-sm text-neutral-900">{{ $pickupRequest->recipient_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-neutral-700">Nomor Telepon</label>
                                <p class="text-sm text-neutral-900">{{ $pickupRequest->recipient_phone }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-neutral-700">Alamat Lengkap</label>
                                <p class="text-sm text-neutral-900">{{ $pickupRequest->getFullRecipientAddressAttribute() }}</p>
                            </div>
                        </div>
                        @if($pickupRequest->buyerRating)
                        <div class="mt-6 p-4 bg-neutral-50 rounded-lg">
                            <h4 class="text-sm font-semibold text-neutral-900 mb-3">
                                <i class="fas fa-star text-warning mr-2"></i>
                                Rating Buyer
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                                <div>
                                    <p class="text-xs text-neutral-600">Total Order</p>
                                    <p class="text-lg font-bold text-neutral-900">{{ $pickupRequest->buyerRating->total_orders }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-600">Berhasil</p>
                                    <p class="text-lg font-bold text-success-600">{{ $pickupRequest->buyerRating->successful_orders }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-600">Gagal/Batal</p>
                                    <p class="text-lg font-bold text-error-600">{{ $pickupRequest->buyerRating->failed_cod_orders + $pickupRequest->buyerRating->cancelled_orders }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-600">Success Rate</p>
                                    <p class="text-lg font-bold text-{{ $pickupRequest->buyerRating->success_rate >= 80 ? 'success' : ($pickupRequest->buyerRating->success_rate >= 60 ? 'warning' : 'error') }}-600">
                                        {{ number_format($pickupRequest->buyerRating->success_rate, 1) }}%
                                    </p>
                                </div>
                            </div>
                            @if($pickupRequest->buyerRating->getRiskWarningAttribute())
                            <div class="mt-3 p-3 bg-{{ $pickupRequest->buyerRating->risk_level->color() }}-50 border border-{{ $pickupRequest->buyerRating->risk_level->color() }}-200 rounded-lg">
                                <p class="text-sm text-{{ $pickupRequest->buyerRating->risk_level->color() }}-700 font-medium">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    {{ $pickupRequest->buyerRating->getRiskWarningAttribute() }}
                                </p>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900">
                            <i class="fas fa-user text-primary mr-2"></i>
                            Informasi Pengirim
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-neutral-700">Nama User</label>
                                <p class="text-sm text-neutral-900">{{ $pickupRequest->user->name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-neutral-700">Email</label>
                                <p class="text-sm text-neutral-900">{{ $pickupRequest->user->email }}</p>
                            </div>
                            @if($pickupRequest->isPickupType() && $pickupRequest->pickupAddress)
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-neutral-700">Alamat Pickup</label>
                                <p class="text-sm text-neutral-900">{{ $pickupRequest->getFullPickupAddressAttribute() }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900">
                            <i class="fas fa-box text-secondary mr-2"></i>
                            Detail Item ({{ $pickupRequest->items->count() }} item)
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-neutral-200">
                            <thead class="bg-neutral-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Qty</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Berat/pcs</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Harga/pcs</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900">
                            <i class="fas fa-money-bill text-success mr-2"></i>
                            Ringkasan Biaya
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-600">Total Produk:</span>
                            <span class="font-medium">Rp {{ number_format($pickupRequest->product_total, 0, ',', '.') }}</span>
                        </div>
                        @if($pickupRequest->shipping_cost > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-600">Ongkir:</span>
                            <span class="font-medium">Rp {{ number_format($pickupRequest->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($pickupRequest->service_fee > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-neutral-600">Biaya Layanan:</span>
                            <span class="font-medium">Rp {{ number_format($pickupRequest->service_fee, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="border-t border-neutral-200 pt-4">
                            <div class="flex justify-between">
                                <span class="text-base font-semibold text-neutral-900">Total:</span>
                                <span class="text-lg font-bold text-primary">Rp {{ number_format($pickupRequest->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @if($pickupRequest->payment_method === 'cod')
                        <div class="mt-4 p-3 bg-warning-50 border border-warning-200 rounded-lg">
                            <p class="text-sm text-warning-700">
                                <i class="fas fa-money-bill-wave mr-2"></i>
                                COD Amount: Rp {{ number_format($pickupRequest->cod_amount, 0, ',', '.') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900">
                            <i class="fas fa-clock text-primary mr-2"></i>
                            Timeline
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-plus text-primary-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-neutral-900">Request Dibuat</p>
                                    <p class="text-xs text-neutral-500">{{ $pickupRequest->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            @if($pickupRequest->status !== 'pending')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-success-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-success-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-neutral-900">Dikonfirmasi</p>
                                </div>
                            </div>
                            @endif
                            @if($pickupRequest->pickup_scheduled_at)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-secondary-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-secondary-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-neutral-900">Dijadwalkan</p>
                                    <p class="text-xs text-neutral-500">{{ $pickupRequest->pickup_scheduled_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            @endif
                            @if($pickupRequest->picked_up_at)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-hand-paper text-primary-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-neutral-900">Diambil</p>
                                    <p class="text-xs text-neutral-500">{{ $pickupRequest->picked_up_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            @endif
                            @if($pickupRequest->status === 'in_transit')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-truck text-primary-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-neutral-900">Dalam Perjalanan</p>
                                </div>
                            </div>
                            @endif
                            @if($pickupRequest->delivered_at)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-success-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check-circle text-success-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-neutral-900">Terkirim</p>
                                    <p class="text-xs text-neutral-500">{{ $pickupRequest->delivered_at->format('d M Y H:i') }}</p>
                                    @if($pickupRequest->cod_collected_at)
                                    <p class="text-xs text-warning-600">COD: {{ $pickupRequest->cod_collected_at->format('d M Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                            @if($pickupRequest->status === 'failed')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-error-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-times-circle text-error-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-neutral-900">Gagal</p>
                                </div>
                            </div>
                            @endif
                            @if($pickupRequest->status === 'cancelled')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-neutral-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-ban text-neutral-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-neutral-900">Dibatalkan</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if($pickupRequest->status !== 'delivered' && $pickupRequest->status !== 'failed' && $pickupRequest->status !== 'cancelled')
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                        <h3 class="text-lg font-semibold text-neutral-900">
                            <i class="fas fa-cog text-secondary mr-2"></i>
                            Aksi Admin
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if($pickupRequest->status === 'pending')
                        <button @click="confirmPickup()" :disabled="loading"
                                class="w-full px-4 py-2 bg-success text-white rounded-lg hover:bg-success-600 disabled:opacity-50 transition-colors">
                            <i class="fas fa-check mr-2"></i>
                            <span x-show="!loading">Konfirmasi Request</span>
                            <span x-show="loading">Memproses...</span>
                        </button>
                        @endif
                        @if($pickupRequest->isPickupType() && $pickupRequest->status === 'confirmed')
                        <button @click="openScheduleModal()" 
                                class="w-full px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary-600 transition-colors">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Jadwalkan Pickup
                        </button>
                        @endif
                        @if($pickupRequest->isPickupType() && in_array($pickupRequest->status, ['confirmed', 'pickup_scheduled']))
                        <button @click="openPickupModal()" 
                                class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors">
                            <i class="fas fa-hand-paper mr-2"></i>
                            Tandai Diambil
                        </button>
                        @endif
                        @if(($pickupRequest->isPickupType() && $pickupRequest->status === 'picked_up') || ($pickupRequest->isDropOffType() && $pickupRequest->status === 'confirmed'))
                        <button @click="markAsInTransit()" :disabled="loading"
                                class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 disabled:opacity-50 transition-colors">
                            <i class="fas fa-truck mr-2"></i>
                            <span x-show="!loading">Dalam Perjalanan</span>
                            <span x-show="loading">Memproses...</span>
                        </button>
                        @endif
                        @if($pickupRequest->status === 'in_transit')
                        <button @click="markAsDelivered()" :disabled="loading"
                                class="w-full px-4 py-2 bg-success text-white rounded-lg hover:bg-success-600 disabled:opacity-50 transition-colors">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span x-show="!loading">Tandai Terkirim</span>
                            <span x-show="loading">Memproses...</span>
                        </button>
                        <button @click="openFailModal()" 
                                class="w-full px-4 py-2 bg-error text-white rounded-lg hover:bg-error-600 transition-colors">
                            <i class="fas fa-times-circle mr-2"></i>
                            Tandai Gagal
                        </button>
                        @endif
                        @if(in_array($pickupRequest->status, ['pending', 'confirmed']))
                        <hr class="my-4">
                        <button @click="cancelPickup()" :disabled="loading"
                                class="w-full px-4 py-2 bg-error text-white rounded-lg hover:bg-error-600 disabled:opacity-50 transition-colors">
                            <i class="fas fa-ban mr-2"></i>
                            <span x-show="!loading">Batalkan Request</span>
                            <span x-show="loading">Memproses...</span>
                        </button>
                        @endif
                    </div>
                </div>
                @endif
            </div>
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
    </div>
    <script>
        function pickupRequestDetail(requestId) {
            return {
                requestId: requestId,
                loading: false,
                alertMessage: '',
                alertType: 'success',
                scheduleModal: {
                    show: false,
                    datetime: ''
                },
                pickupModal: {
                    show: false,
                    trackingNumber: ''
                },
                failModal: {
                    show: false,
                    reason: ''
                },
                showAlert(message, type = 'success') {
                    this.alertMessage = message;
                    this.alertType = type;
                    setTimeout(() => {
                        this.clearAlert();
                    }, 5000);
                },
                clearAlert() {
                    this.alertMessage = '';
                },
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
                updateStatusBadge(status) {
                    const badge = document.getElementById('status-badge');
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
                            badge.className = `inline-flex items-center px-4 py-2 text-sm font-medium rounded-full border ${config.class}`;
                            badge.innerHTML = `<i class="${config.icon} mr-2"></i>${config.label}`;
                        }
                    }
                },
                async confirmPickup() {
                    if (!confirm('Apakah Anda yakin ingin mengkonfirmasi pickup request ini?')) {
                        return;
                    }
                    this.loading = true;
                    try {
                        const result = await this.apiCall(`/admin/pickup-requests/${this.requestId}/confirm`);
                        this.showAlert(result.message);
                        this.updateStatusBadge('confirmed');
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                openScheduleModal() {
                    this.scheduleModal.datetime = '';
                    this.scheduleModal.show = true;
                },
                async schedulePickup() {
                    if (!this.scheduleModal.datetime) {
                        this.showAlert('Tanggal dan waktu pickup harus diisi', 'error');
                        return;
                    }
                    this.loading = true;
                    try {
                        const result = await this.apiCall(`/admin/pickup-requests/${this.requestId}/schedule-pickup`, {
                            pickup_scheduled_at: this.scheduleModal.datetime
                        });
                        this.showAlert(result.message);
                        this.updateStatusBadge('pickup_scheduled');
                        this.scheduleModal.show = false;
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                openPickupModal() {
                    this.pickupModal.trackingNumber = '';
                    this.pickupModal.show = true;
                },
                async markAsPickedUp() {
                    this.loading = true;
                    try {
                        const data = {};
                        if (this.pickupModal.trackingNumber) {
                            data.courier_tracking_number = this.pickupModal.trackingNumber;
                        }
                        const result = await this.apiCall(`/admin/pickup-requests/${this.requestId}/mark-picked-up`, data);
                        this.showAlert(result.message);
                        this.updateStatusBadge('picked_up');
                        this.pickupModal.show = false;
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                async markAsInTransit() {
                    if (!confirm('Apakah Anda yakin paket sedang dalam perjalanan?')) {
                        return;
                    }
                    this.loading = true;
                    try {
                        const result = await this.apiCall(`/admin/pickup-requests/${this.requestId}/mark-in-transit`);
                        this.showAlert(result.message);
                        this.updateStatusBadge('in_transit');
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                async markAsDelivered() {
                    if (!confirm('Apakah Anda yakin paket sudah terkirim?')) {
                        return;
                    }
                    this.loading = true;
                    try {
                        const result = await this.apiCall(`/admin/pickup-requests/${this.requestId}/mark-delivered`);
                        this.showAlert(result.message);
                        this.updateStatusBadge('delivered');
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                openFailModal() {
                    this.failModal.reason = '';
                    this.failModal.show = true;
                },
                async markAsFailed() {
                    this.loading = true;
                    try {
                        const data = {};
                        if (this.failModal.reason) {
                            data.failure_reason = this.failModal.reason;
                        }
                        const result = await this.apiCall(`/admin/pickup-requests/${this.requestId}/mark-failed`, data);
                        this.showAlert(result.message);
                        this.updateStatusBadge('failed');
                        this.failModal.show = false;
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                async cancelPickup() {
                    if (!confirm('Apakah Anda yakin ingin membatalkan pickup request ini?')) {
                        return;
                    }
                    this.loading = true;
                    try {
                        const result = await this.apiCall(`/admin/pickup-requests/${this.requestId}/cancel`);
                        this.showAlert(result.message);
                        this.updateStatusBadge('cancelled');
                        setTimeout(() => window.location.reload(), 2000);
                    } catch (error) {
                        this.showAlert(error.message, 'error');
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        /* Color Variables */
        .bg-primary { background-color: var(--color-primary); }
        .bg-primary-50 { background-color: var(--color-primary-50); }
        .bg-primary-100 { background-color: var(--color-primary-100); }
        .bg-primary-600 { background-color: var(--color-primary-600); }
        .text-primary { color: var(--color-primary); }
        .text-primary-600 { color: var(--color-primary-600); }
        .text-primary-700 { color: var(--color-primary-700); }
        .border-primary-200 { border-color: var(--color-primary-200); }
        .focus\:ring-primary-500:focus { --tw-ring-color: var(--color-primary-500); }
        .focus\:border-primary-500:focus { border-color: var(--color-primary-500); }
        .hover\:bg-primary-600:hover { background-color: var(--color-primary-600); }
        .hover\:text-primary-600:hover { color: var(--color-primary-600); }
        .bg-secondary { background-color: var(--color-secondary); }
        .bg-secondary-50 { background-color: var(--color-secondary-50); }
        .bg-secondary-100 { background-color: var(--color-secondary-100); }
        .bg-secondary-600 { background-color: var(--color-secondary-600); }
        .text-secondary { color: var(--color-secondary); }
        .text-secondary-600 { color: var(--color-secondary-600); }
        .text-secondary-700 { color: var(--color-secondary-700); }
        .border-secondary-200 { border-color: var(--color-secondary-200); }
        .hover\:bg-secondary-600:hover { background-color: var(--color-secondary-600); }
        .bg-success { background-color: var(--color-success); }
        .bg-success-50 { background-color: var(--color-success-50); }
        .bg-success-100 { background-color: var(--color-success-100); }
        .bg-success-600 { background-color: var(--color-success-600); }
        .text-success { color: var(--color-success); }
        .text-success-600 { color: var(--color-success-600); }
        .text-success-700 { color: var(--color-success-700); }
        .border-success-200 { border-color: var(--color-success-200); }
        .hover\:bg-success-600:hover { background-color: var(--color-success-600); }
        .bg-warning-50 { background-color: var(--color-warning-50); }
        .bg-warning-100 { background-color: var(--color-warning-100); }
        .text-warning { color: var(--color-warning); }
        .text-warning-600 { color: var(--color-warning-600); }
        .text-warning-700 { color: var(--color-warning-700); }
        .border-warning-200 { border-color: var(--color-warning-200); }
        .bg-error { background-color: var(--color-error); }
        .bg-error-50 { background-color: var(--color-error-50); }
        .bg-error-100 { background-color: var(--color-error-100); }
        .bg-error-600 { background-color: var(--color-error-600); }
        .text-error-600 { color: var(--color-error-600); }
        .text-error-700 { color: var(--color-error-700); }
        .border-error-200 { border-color: var(--color-error-200); }
        .hover\:bg-error-600:hover { background-color: var(--color-error-600); }
        .bg-neutral-50 { background-color: var(--color-neutral-50); }
        .bg-neutral-100 { background-color: var(--color-neutral-100); }
        .bg-neutral-200 { background-color: var(--color-neutral-200); }
        .bg-neutral-300 { background-color: var(--color-neutral-300); }
        .bg-neutral-400 { background-color: var(--color-neutral-400); }
        .text-neutral-400 { color: var(--color-neutral-400); }
        .text-neutral-500 { color: var(--color-neutral-500); }
        .text-neutral-600 { color: var(--color-neutral-600); }
        .text-neutral-700 { color: var(--color-neutral-700); }
        .text-neutral-900 { color: var(--color-neutral-900); }
        .border-neutral-200 { border-color: var(--color-neutral-200); }
        .border-neutral-300 { border-color: var(--color-neutral-300); }
        .divide-neutral-200 > :not([hidden]) ~ :not([hidden]) { border-color: var(--color-neutral-200); }
        .hover\:bg-neutral-50:hover { background-color: var(--color-neutral-50); }
        .hover\:bg-neutral-400:hover { background-color: var(--color-neutral-400); }
        .hover\:text-neutral-600:hover { color: var(--color-neutral-600); }
    </style>
</x-layouts.plain-app>
