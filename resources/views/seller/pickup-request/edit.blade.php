<x-layouts.plain-app>
    <x-slot name="title">Edit Pickup Request</x-slot>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900">Edit Pickup Request</h1>
                    <p class="mt-2 text-neutral-600">Edit permintaan pickup <strong>{{ $pickupRequest->pickup_code }}</strong> </p>
                </div>
                <a href="{{ route('seller.pickup-request.show', $pickupRequest->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary-600 transition-colors shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        @if (session('error'))
            <div class="rounded-md bg-red-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('seller.pickup-request.update', $pickupRequest->id) }}" class="space-y-6"
              x-data="pickupRequestForm()" x-init="initGoogleMaps()">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-md p-6 border border-neutral-200">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-shipping-fast text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900">Metode Pengiriman</h3>
                </div>
                <div class="flex space-x-4">
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer flex-1" :class="deliveryType === 'pickup' ? 'border-primary-500 bg-primary-50' : 'border-neutral-300'">
                        <input type="radio" name="delivery_type" value="pickup" x-model="deliveryType" class="form-radio text-primary-600 focus:ring-primary-500">
                        <span class="ml-3 text-sm font-medium text-neutral-800">
                            <i class="fas fa-truck-pickup mr-2"></i>Di-pickup oleh Kurir
                        </span>
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer flex-1" :class="deliveryType === 'drop_off' ? 'border-primary-500 bg-primary-50' : 'border-neutral-300'">
                        <input type="radio" name="delivery_type" value="drop_off" x-model="deliveryType" class="form-radio text-primary-600 focus:ring-primary-500">
                        <span class="ml-3 text-sm font-medium text-neutral-800">
                            <i class="fas fa-store mr-2"></i>Saya Antar ke Gerai
                        </span>
                    </label>
                </div>
                @error('delivery_type')
                    <p class="mt-2 text-sm text-error-600">{{ $message }}</p>
                @enderror
            </div>

            <div x-show="deliveryType === 'pickup'" x-transition>
                <div class="bg-white rounded-xl shadow-md p-6 border border-neutral-200">
                    <div class="flex items-center mb-4">
                        <div class="bg-primary-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-truck-pickup text-primary-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-neutral-900">Informasi Lokasi Pickup</h3>
                    </div>
                    <div class="space-y-4">
                        <!-- Pilih Address untuk PICKUP -->
                        <div>
                            <label for="address_id" class="block text-sm font-medium text-neutral-700 mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                Pilih Alamat Pickup
                            </label>
                            <select name="address_id" id="address_id" 
                                    class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                    required x-model="selectedAddressId" @change="loadSelectedPickupAddress">
                                <option value="">-- Pilih Alamat --</option>
                                @foreach($addresses as $address)
                                    <option value="{{ $address->id }}" 
                                            data-name="{{ $address->name }}"
                                            data-phone="{{ $address->phone }}"
                                            data-city="{{ $address->city }}"
                                            data-province="{{ $address->province }}"
                                            data-postal_code="{{ $address->postal_code }}"
                                            data-address="{{ $address->address }}"
                                            data-latitude="{{ $address->latitude }}"
                                            data-longitude="{{ $address->longitude }}"
                                            {{ old('address_id', $pickupRequest->address_id) == $address->id ? 'selected' : '' }}>
                                        {{ $address->label }} - {{ $address->name }}
                                        @if($address->is_default)
                                            <span class="text-primary-600">(Default)</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('address_id')
                                <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tombol Kelola Address -->
                        <div class="flex gap-2">
                            <a href="{{ route('seller.addresses.create') }}" 
                            class="inline-flex items-center px-3 py-2 bg-success-600 text-white rounded-md hover:bg-success-700 transition-colors text-sm">
                                <i class="fas fa-plus mr-1"></i>
                                Tambah Alamat Baru
                            </a>
                            <a href="{{ route('seller.addresses.index') }}" 
                            class="inline-flex items-center px-3 py-2 bg-secondary-600 text-white rounded-md hover:bg-secondary-700 transition-colors text-sm">
                                <i class="fas fa-cog mr-1"></i>
                                Kelola Alamat
                            </a>
                        </div>

                        <!-- Preview Address Pickup yang Dipilih -->
                        <div x-show="selectedAddressId" class="p-4 bg-blue-50 rounded-lg border">
                            <h4 class="font-medium text-blue-900 mb-2">Preview Alamat Pickup:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-600">
                                <div>
                                    <strong>Nama:</strong> <span x-text="pickupPreviewData.name"></span>
                                </div>
                                <div>
                                    <strong>Telepon:</strong> <span x-text="pickupPreviewData.phone"></span>
                                </div>
                                <div>
                                    <strong>Kota:</strong> <span x-text="pickupPreviewData.city"></span>
                                </div>
                                <div>
                                    <strong>Provinsi:</strong> <span x-text="pickupPreviewData.province"></span>
                                </div>
                                <div class="md:col-span-2">
                                    <strong>Alamat:</strong> <span x-text="pickupPreviewData.address"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Map untuk Pickup -->
                        <div x-show="selectedAddressId" class="mt-4">
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Lokasi Pickup di Peta:</label>
                            <div id="pickup-map" class="w-full h-64 rounded-lg border border-gray-300"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Lokasi Pickup -->
            

            <!-- Informasi Penerima -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-neutral-200">
                <div class="flex items-center mb-4">
                    <div class="bg-secondary-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-user text-secondary-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900">Informasi Penerima</h3>
                    <small class="ml-2 text-gray-500">(Isi manual, tidak menggunakan alamat tersimpan)</small>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Form Fields -->
                        <div class="space-y-4">
                            <!-- Search Address -->
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-2">
                                    <i class="fas fa-search mr-1"></i>
                                    Cari Alamat Penerima
                                </label>
                                <input type="text" 
                                       id="recipient-address-search"
                                       x-model="recipientSearchQuery"
                                       placeholder="Mulai ketik alamat penerima..."
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="recipient_name" class="block text-sm font-medium text-neutral-700 mb-1">Nama Penerima</label>
                                    <input type="text" name="recipient_name" id="recipient_name"
                                           x-model="formData.recipient_name"
                                           value="{{ old('recipient_name', $pickupRequest->recipient_name) }}"
                                           class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                           required>
                                    @error('recipient_name')
                                        <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="recipient_phone" class="block text-sm font-medium text-neutral-700 mb-1">Nomor Telepon</label>
                                    <input type="text" name="recipient_phone" id="recipient_phone"
                                           x-model="formData.recipient_phone"
                                           value="{{ old('recipient_phone', $pickupRequest->recipient_phone) }}"
                                           class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                           required>
                                    @error('recipient_phone')
                                        <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="recipient_city" class="block text-sm font-medium text-neutral-700 mb-1">Kota</label>
                                    <input type="text" name="recipient_city" id="recipient_city"
                                           x-model="formData.recipient_city"
                                           value="{{ old('recipient_city', $pickupRequest->recipient_city) }}"
                                           class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                           required>
                                    @error('recipient_city')
                                        <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="recipient_province" class="block text-sm font-medium text-neutral-700 mb-1">Provinsi</label>
                                    <input type="text" name="recipient_province" id="recipient_province"
                                           x-model="formData.recipient_province"
                                           value="{{ old('recipient_province', $pickupRequest->recipient_province) }}"
                                           class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                           required>
                                    @error('recipient_province')
                                        <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="recipient_postal_code" class="block text-sm font-medium text-neutral-700 mb-1">Kode Pos</label>
                                <input type="text" name="recipient_postal_code" id="recipient_postal_code"
                                       x-model="formData.recipient_postal_code"
                                       value="{{ old('recipient_postal_code', $pickupRequest->recipient_postal_code) }}"
                                       class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                       required>
                                @error('recipient_postal_code')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="recipient_address" class="block text-sm font-medium text-neutral-700 mb-1">Alamat Penerima</label>
                                <textarea name="recipient_address" id="recipient_address"
                                          x-model="formData.recipient_address"
                                          rows="3"
                                          placeholder="Akan terisi otomatis dari pencarian atau peta"
                                          class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                          required>{{ old('recipient_address', $pickupRequest->recipient_address) }}</textarea>
                                @error('recipient_address')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Hidden Coordinates -->
                            <input type="hidden" name="recipient_latitude" x-model="formData.recipient_latitude">
                            <input type="hidden" name="recipient_longitude" x-model="formData.recipient_longitude">

                            <!-- Coordinates Display (Optional) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 mb-1">Latitude</label>
                                    <input type="number" 
                                           x-model="formData.recipient_latitude"
                                           step="any"
                                           class="block w-full rounded-lg border-neutral-300 bg-gray-50"
                                           readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-neutral-700 mb-1">Longitude</label>
                                    <input type="number" 
                                           x-model="formData.recipient_longitude"
                                           step="any"
                                           class="block w-full rounded-lg border-neutral-300 bg-gray-50"
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Map -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-2">Lokasi Penerima</label>
                                <div id="recipient-map" class="w-full h-80 rounded-lg border border-gray-300"></div>
                            </div>
                            <div x-show="recipientStatus" 
                                 class="p-3 rounded-md transition-all" 
                                 :class="recipientStatus === 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'"
                                 x-transition>
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i :class="recipientStatus === 'success' ? 'fas fa-check-circle text-green-400' : 'fas fa-exclamation-triangle text-red-400'"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium" 
                                           :class="recipientStatus === 'success' ? 'text-green-800' : 'text-red-800'" 
                                           x-text="recipientMessage"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produk -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-neutral-200">
                <div class="flex items-center mb-4">
                    <div class="bg-success-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-boxes text-success-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900">Pilih Produk</h3>
                </div>
                <div id="product-container">
                    @forelse($pickupRequest->items as $index => $item)
                        <div class="product-item grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-4">
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-1">Produk</label>
                                <select name="items[{{ $index }}][product_id]"
                                        class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                        required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                                data-weight="{{ $product->weight_per_pcs }}"
                                                {{ old('items.'.$index.'.product_id', $item->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-1">Jumlah</label>
                                <input type="number" name="items[{{ $index }}][quantity]" min="1" 
                                       value="{{ old('items.'.$index.'.quantity', $item->quantity) }}"
                                       class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                       required>
                            </div>
                            <div>
                                <button type="button"
                                        class="remove-product px-3 py-2 bg-error-600 text-white rounded-lg hover:bg-error-700 transition-colors shadow-sm"
                                        style="{{ $pickupRequest->items->count() <= 1 ? 'display: none;' : 'display: inline-flex;' }}">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="product-item grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-4">
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-1">Produk</label>
                                <select name="items[0][product_id]"
                                    class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                    required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                            data-weight="{{ $product->weight_per_pcs }}">
                                            {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-1">Jumlah</label>
                                <input type="number" name="items[0][quantity]" min="1" value="1"
                                    class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                    required>
                            </div>
                            <div>
                                <button type="button"
                                    class="remove-product px-3 py-2 bg-error-600 text-white rounded-lg hover:bg-error-700 transition-colors shadow-sm"
                                    style="display: none;">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
                <button type="button" id="add-product"
                    class="mt-4 px-4 py-2 bg-success-600 text-white rounded-lg hover:bg-success-700 transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Produk
                </button>
            </div>

            <!-- Informasi Pengiriman dan Pembayaran -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-neutral-200">
                <div class="flex items-center mb-4">
                    <div class="bg-warning-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-truck text-warning-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900">Pengiriman & Pembayaran</h3>
                </div>

                <!-- Wallet Balance Info -->
                <div class="mb-4 p-4 bg-secondary-50 border border-secondary-200 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-wallet text-secondary-600 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-secondary-800">Informasi Wallet</h4>
                            <p class="text-sm text-secondary-700 mt-1">
                                Saldo Wallet: <span
                                    class="font-semibold">{{ $wallet->getFormattedAvailableBalanceAttribute() }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="shipping_cost" class="block text-sm font-medium text-neutral-700 mb-1">Biaya
                            Pengiriman</label>
                        <input type="number" name="shipping_cost" id="shipping_cost"
                            value="{{ old('shipping_cost', $pickupRequest->shipping_cost) }}" min="0" step="0.01"
                            class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                            required>
                        @error('shipping_cost')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="service_fee" class="block text-sm font-medium text-neutral-700 mb-1">Biaya Layanan
                            (Opsional)</label>
                        <input type="number" name="service_fee" id="service_fee"
                            value="{{ old('service_fee', $pickupRequest->service_fee) }}" min="0" step="0.01"
                            class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm">
                        @error('service_fee')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-neutral-700 mb-1">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method"
                                class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                required>
                            <option value="">Pilih Metode</option>
                            <option value="wallet" {{ old('payment_method', $pickupRequest->payment_method) === 'wallet' ? 'selected' : '' }}>
                                Wallet
                            </option>
                            
                            <option value="cod" 
                                    {{ old('payment_method', $pickupRequest->payment_method) === 'cod' ? 'selected' : '' }} 
                                    @disabled(!auth()->user()->canRequestCodPickup())>
                                COD @if(!auth()->user()->canRequestCodPickup()) (Akun Belum Terverifikasi) @endif
                            </option>
                        </select>
                        
                        @if(!auth()->user()->canRequestCodPickup())
                            <p class="mt-1 text-xs text-neutral-500">
                                Fitur COD hanya tersedia untuk seller yang profilnya telah diverifikasi oleh admin.
                            </p>
                        @endif

                        @error('payment_method')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="courier_service" class="block text-sm font-medium text-neutral-700 mb-1">Jasa
                            Kurir (Opsional)</label>
                        <input type="text" name="courier_service" id="courier_service"
                            value="{{ old('courier_service', $pickupRequest->courier_service) }}"
                            class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm">
                        @error('courier_service')
                            <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Total Amount Display -->
                <div class="mt-4 p-4 bg-primary-50 border border-primary-200 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-primary-800">Total Pembayaran:</span>
                        <span id="total-amount" class="text-lg font-bold text-primary-900">Rp 0</span>
                    </div>
                </div>

                <!-- Wallet Balance Warning -->
                <div id="wallet-warning" class="mt-4 p-4 bg-error-50 border border-error-200 rounded-lg"
                    style="display: none;">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-error-500 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-error-800">Saldo Wallet Tidak Mencukupi</h3>
                            <div class="mt-1 text-sm text-error-700">
                                <p id="wallet-warning-message"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="notes" class="block text-sm font-medium text-neutral-700 mb-1">Catatan
                        (Opsional)</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm">{{ old('notes', $pickupRequest->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('seller.pickup-request.show', $pickupRequest->id) }}"
                    class="px-6 py-3 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition-colors shadow-sm text-center">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors shadow-sm">
                    Update Pickup Request
                </button>
            </div>
        </form>
    </div>

    <script>
        function pickupRequestForm() {
            return {
                deliveryType: @json(old('delivery_type', $pickupRequest->delivery_type ?? 'pickup')),

                recipientSearchQuery: '',
                selectedAddressId: @json(old('address_id', $pickupRequest->address_id)),
                pickupPreviewData: {
                    name: '',
                    phone: '',
                    city: '',
                    province: '',
                    address: ''
                },
                formData: {
                    recipient_name: @json(old('recipient_name', $pickupRequest->recipient_name)),
                    recipient_phone: @json(old('recipient_phone', $pickupRequest->recipient_phone)),
                    recipient_city: @json(old('recipient_city', $pickupRequest->recipient_city)),
                    recipient_province: @json(old('recipient_province', $pickupRequest->recipient_province)),
                    recipient_postal_code: @json(old('recipient_postal_code', $pickupRequest->recipient_postal_code)),
                    recipient_address: @json(old('recipient_address', $pickupRequest->recipient_address)),
                    recipient_latitude: {{ old('recipient_latitude', $pickupRequest->recipient_latitude ?? -6.2088) }},
                    recipient_longitude: {{ old('recipient_longitude', $pickupRequest->recipient_longitude ?? 106.8456) }}
                },
                recipientStatus: '',
                recipientMessage: '',
                pickupStatus: '',
                pickupMessage: '',
                recipientMap: null,
                pickupMap: null,
                recipientMarker: null,
                pickupMarker: null,
                recipientAutocomplete: null,
                pickupAutocomplete: null,
                geocoder: null,

                initGoogleMaps() {
                    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                        this.setupMapsAndAutocomplete();
                    } else {
                        const script = document.createElement('script');
                        script.src = `https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMapsCallback`;
                        script.defer = true;
                        document.head.appendChild(script);
                        window.initMapsCallback = () => {
                            this.setupMapsAndAutocomplete();
                        };
                    }
                },

                setupMapsAndAutocomplete() {
                    this.geocoder = new google.maps.Geocoder();
                    this.setupPickupMap();
                    this.setupRecipientMap();
                    
                    // Load pickup address on page load if selected
                    if (this.selectedAddressId) {
                        this.loadSelectedPickupAddress();
                    }
                },

                setupPickupMap() {
                    // Ambil koordinat dari alamat pickup yang sudah dipilih
                    const pickupAddress = @json($pickupRequest->pickupAddress);
                    const initialPosition = {
                        lat: pickupAddress && pickupAddress.latitude ? parseFloat(pickupAddress.latitude) : -6.2088,
                        lng: pickupAddress && pickupAddress.longitude ? parseFloat(pickupAddress.longitude) : 106.8456
                    };

                    this.pickupMap = new google.maps.Map(document.getElementById('pickup-map'), {
                        center: initialPosition,
                        zoom: 15,
                        mapTypeControl: false,
                        streetViewControl: false
                    });

                    this.pickupMarker = new google.maps.Marker({
                        position: initialPosition,
                        map: this.pickupMap,
                        draggable: false, // Tidak bisa didrag karena menggunakan alamat tersimpan
                        title: 'Lokasi Pickup (dari alamat tersimpan)'
                    });
                },

                setupRecipientMap() {
                    const initialPosition = {
                        lat: parseFloat(this.formData.recipient_latitude),
                        lng: parseFloat(this.formData.recipient_longitude)
                    };

                    this.recipientMap = new google.maps.Map(document.getElementById('recipient-map'), {
                        center: initialPosition,
                        zoom: 15,
                        mapTypeControl: false,
                        streetViewControl: false
                    });

                    this.recipientMarker = new google.maps.Marker({
                        position: initialPosition,
                        map: this.recipientMap,
                        draggable: true,
                        title: 'Lokasi Penerima'
                    });

                    const recipientInput = document.getElementById('recipient-address-search');
                    this.recipientAutocomplete = new google.maps.places.Autocomplete(recipientInput, {
                        types: ['address'],
                        componentRestrictions: { country: 'id' }
                    });

                    this.recipientAutocomplete.addListener('place_changed', () => {
                        this.handleRecipientPlaceChanged();
                    });

                    this.recipientMarker.addListener('dragend', (event) => {
                        this.handleRecipientMarkerDrag(event);
                    });

                    this.recipientMap.addListener('click', (event) => {
                        this.recipientMarker.setPosition(event.latLng);
                        this.handleRecipientMarkerDrag(event);
                    });
                },

                handleRecipientPlaceChanged() {
                    const place = this.recipientAutocomplete.getPlace();
                    if (!place.geometry || !place.geometry.location) {
                        this.showRecipientStatus('error', 'Lokasi tidak ditemukan. Silakan coba lagi.');
                        return;
                    }
                    this.recipientMap.setCenter(place.geometry.location);
                    this.recipientMap.setZoom(17);
                    this.recipientMarker.setPosition(place.geometry.location);
                    this.fillRecipientAddressComponents(place);
                    this.setRecipientCoordinates(place.geometry.location);
                    this.showRecipientStatus('success', 'Alamat penerima berhasil ditemukan dan diisi otomatis.');
                },

                handleRecipientMarkerDrag(event) {
                    this.setRecipientCoordinates(event.latLng);
                    this.reverseGeocodeRecipient(event.latLng);
                },

                reverseGeocodeRecipient(location) {
                    this.geocoder.geocode({ 'location': location }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            this.fillRecipientAddressComponents(results[0]);
                            this.recipientSearchQuery = results[0].formatted_address;
                            this.showRecipientStatus('success', 'Alamat penerima diperbarui dari lokasi di peta.');
                        } else {
                            this.showRecipientStatus('error', 'Gagal mendapatkan alamat dari lokasi.');
                        }
                    });
                },

                fillRecipientAddressComponents(place) {
                    if (place.formatted_address) {
                        this.formData.recipient_address = place.formatted_address;
                    }
                    const components = place.address_components;
                    let city = '';
                    let state = '';
                    components.forEach(component => {
                        const types = component.types;
                        if (types.includes('administrative_area_level_2')) {
                            city = component.long_name;
                        } else if (types.includes('administrative_area_level_1')) {
                            state = component.long_name;
                        } else if (types.includes('postal_code')) {
                            this.formData.recipient_postal_code = component.long_name;
                        }
                    });
                    this.formData.recipient_city = city.replace(/Kota |Kabupaten /g, '');
                    this.formData.recipient_province = state;
                },

                setRecipientCoordinates(location) {
                    if (typeof location.lat === 'function') {
                        this.formData.recipient_latitude = location.lat();
                        this.formData.recipient_longitude = location.lng();
                    } else { 
                        this.formData.recipient_latitude = location.lat;
                        this.formData.recipient_longitude = location.lng;
                    }
                },

                showRecipientStatus(status, message) {
                    this.recipientStatus = status;
                    this.recipientMessage = message;
                    setTimeout(() => {
                        this.recipientStatus = '';
                        this.recipientMessage = '';
                    }, 5000);
                },

                showPickupStatus(status, message) {
                    this.pickupStatus = status;
                    this.pickupMessage = message;
                    setTimeout(() => {
                        this.pickupStatus = '';
                        this.pickupMessage = '';
                    }, 5000);
                },

                loadSelectedPickupAddress() {
                    const select = document.getElementById('address_id');
                    const selectedOption = select.options[select.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        // Update preview data untuk pickup
                        this.pickupPreviewData = {
                            name: selectedOption.dataset.name || '',
                            phone: selectedOption.dataset.phone || '',
                            city: selectedOption.dataset.city || '',
                            province: selectedOption.dataset.province || '',
                            address: selectedOption.dataset.address || ''
                        };
                        
                        // Update map untuk pickup
                        const latitude = parseFloat(selectedOption.dataset.latitude);
                        const longitude = parseFloat(selectedOption.dataset.longitude);
                        if (!isNaN(latitude) && !isNaN(longitude) && this.pickupMap) {
                            const position = { lat: latitude, lng: longitude };
                            this.pickupMap.setCenter(position);
                            this.pickupMap.setZoom(17);
                            this.pickupMarker.setPosition(position);
                        }
                    } else {
                        this.pickupPreviewData = {
                            name: '',
                            phone: '',
                            city: '',
                            province: '',
                            address: ''
                        };
                    }
                },
            }
        }

        let productIndex = {{ $pickupRequest->items->count() > 0 ? $pickupRequest->items->keys()->last() + 1 : 1 }};
        const currentWalletBalance = {{ $wallet->available_balance }};

        function calculateTotal() {
            let productTotal = 0;
            document.querySelectorAll('.product-item').forEach(function(item) {
                const productSelect = item.querySelector('select[name*="[product_id]"]');
                const quantityInput = item.querySelector('input[name*="[quantity]"]');
                if (productSelect.value && quantityInput.value) {
                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                    const price = parseFloat(selectedOption.dataset.price) || 0;
                    const quantity = parseInt(quantityInput.value) || 0;
                    productTotal += price * quantity;
                }
            });

            const shippingCost = parseFloat(document.getElementById('shipping_cost').value) || 0;
            const serviceFee = parseFloat(document.getElementById('service_fee').value) || 0;
            const totalAmount = productTotal + shippingCost + serviceFee;

            document.getElementById('total-amount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(
            totalAmount);
            checkWalletBalance(totalAmount);
            return totalAmount;
        }

        function checkWalletBalance(totalAmount) {
            const paymentMethod = document.getElementById('payment_method').value;
            const walletWarning = document.getElementById('wallet-warning');
            const submitButton = document.querySelector('button[type="submit"]');

            if (paymentMethod === 'wallet' && totalAmount > 0) {
                if (currentWalletBalance < totalAmount) {
                    const shortfall = totalAmount - currentWalletBalance;
                    document.getElementById('wallet-warning-message').innerHTML =
                        `Saldo Anda: <strong>Rp ${new Intl.NumberFormat('id-ID').format(currentWalletBalance)}</strong><br>` +
                        `Yang dibutuhkan: <strong>Rp ${new Intl.NumberFormat('id-ID').format(totalAmount)}</strong><br>` +
                        `Kekurangan: <strong>Rp ${new Intl.NumberFormat('id-ID').format(shortfall)}</strong>`;
                    walletWarning.style.display = 'block';
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    walletWarning.style.display = 'none';
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            } else {
                walletWarning.style.display = 'none';
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        document.addEventListener('change', function(e) {
            if (e.target.matches('select[name*="[product_id]"]') ||
                e.target.matches('input[name*="[quantity]"]') ||
                e.target.id === 'shipping_cost' ||
                e.target.id === 'service_fee' ||
                e.target.id === 'payment_method') {
                calculateTotal();
            }
        });

        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name*="[quantity]"]') ||
                e.target.id === 'shipping_cost' ||
                e.target.id === 'service_fee') {
                calculateTotal();
            }
        });

        document.getElementById('add-product').addEventListener('click', function() {
            const container = document.getElementById('product-container');
            const productOptions = {!! json_encode(
                $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'weight_per_pcs' => $product->weight_per_pcs ?? 0,
                    ];
                }),
            ) !!};

            let optionsHtml = '<option value="">Pilih Produk</option>';
            productOptions.forEach(function(product) {
                const formattedPrice = new Intl.NumberFormat('id-ID').format(product.price);
                optionsHtml +=
                    `<option value="${product.id}" data-price="${product.price}" data-weight="${product.weight_per_pcs}">${product.name} - Rp ${formattedPrice}</option>`;
            });

            const newProductItem = `
                <div class="product-item grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-1">Produk</label>
                        <select name="items[${productIndex}][product_id]" class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm" required>
                            ${optionsHtml}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-1">Jumlah</label>
                        <input type="number" name="items[${productIndex}][quantity]" min="1" value="1" 
                            class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm" required>
                    </div>
                    <div>
                        <button type="button" class="remove-product px-3 py-2 bg-error-600 text-white rounded-lg hover:bg-error-700 transition-colors shadow-sm">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newProductItem);
            productIndex++;
            updateRemoveButtons();
            calculateTotal();
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-product')) {
                e.target.closest('.product-item').remove();
                updateRemoveButtons();
                calculateTotal();
            }
        });

        function updateRemoveButtons() {
            const productItems = document.querySelectorAll('.product-item');
            const removeButtons = document.querySelectorAll('.remove-product');
            if (productItems.length > 1) {
                removeButtons.forEach(function(button) {
                    button.style.display = 'inline-flex';
                });
            } else {
                removeButtons.forEach(function(button) {
                    button.style.display = 'none';
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
</x-layouts.plain-app>