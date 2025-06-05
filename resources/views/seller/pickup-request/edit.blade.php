<x-layouts.plain-app>
    <x-slot name="title">Edit Pickup Request</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Pickup Request</h1>
                    <p class="mt-2 text-gray-600">Edit permintaan pickup {{ $pickupRequest->pickup_code }}</p>
                </div>
                <a href="{{ route('seller.pickup-request.show', $pickupRequest->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Status Warning -->
        @if($pickupRequest->status !== 'pending' && $pickupRequest->status !== 'confirmed')
            <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Peringatan</h3>
                        <p class="mt-1 text-sm text-yellow-700">Pickup request ini tidak dapat diedit karena status sudah {{ ucfirst($pickupRequest->status) }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('seller.pickup-request.update', $pickupRequest->id) }}" class="space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Informasi Penerima -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Penerima</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="recipient_name" class="block text-sm font-medium text-gray-700">Nama Penerima</label>
                        <input type="text" name="recipient_name" id="recipient_name" 
                               value="{{ old('recipient_name', $pickupRequest->recipient_name) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('recipient_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="recipient_phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="text" name="recipient_phone" id="recipient_phone" 
                               value="{{ old('recipient_phone', $pickupRequest->recipient_phone) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('recipient_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="recipient_city" class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="recipient_city" id="recipient_city" 
                               value="{{ old('recipient_city', $pickupRequest->recipient_city) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('recipient_city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="recipient_province" class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <input type="text" name="recipient_province" id="recipient_province" 
                               value="{{ old('recipient_province', $pickupRequest->recipient_province) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('recipient_province')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="recipient_postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                        <input type="text" name="recipient_postal_code" id="recipient_postal_code" 
                               value="{{ old('recipient_postal_code', $pickupRequest->recipient_postal_code) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('recipient_postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label for="recipient_address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                    <textarea name="recipient_address" id="recipient_address" rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('recipient_address', $pickupRequest->recipient_address) }}</textarea>
                    @error('recipient_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Informasi Pickup -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Lokasi Pickup</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="pickup_name" class="block text-sm font-medium text-gray-700">Nama Pengirim</label>
                        <input type="text" name="pickup_name" id="pickup_name" 
                               value="{{ old('pickup_name', $pickupRequest->pickup_name) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('pickup_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pickup_phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="text" name="pickup_phone" id="pickup_phone" 
                               value="{{ old('pickup_phone', $pickupRequest->pickup_phone) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('pickup_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pickup_city" class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="pickup_city" id="pickup_city" 
                               value="{{ old('pickup_city', $pickupRequest->pickup_city) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('pickup_city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pickup_province" class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <input type="text" name="pickup_province" id="pickup_province" 
                               value="{{ old('pickup_province', $pickupRequest->pickup_province) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('pickup_province')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pickup_postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                        <input type="text" name="pickup_postal_code" id="pickup_postal_code" 
                               value="{{ old('pickup_postal_code', $pickupRequest->pickup_postal_code) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('pickup_postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- <div>
                        <label for="pickup_scheduled_at" class="block text-sm font-medium text-gray-700">Waktu Pickup (Opsional)</label>
                        <input type="datetime-local" name="pickup_scheduled_at" id="pickup_scheduled_at" 
                               value="{{ old('pickup_scheduled_at', $pickupRequest->pickup_scheduled_at ? $pickupRequest->pickup_scheduled_at->format('Y-m-d\TH:i') : '') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('pickup_scheduled_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div> --}}
                </div>
                <div class="mt-4">
                    <label for="pickup_address" class="block text-sm font-medium text-gray-700">Alamat Pickup</label>
                    <textarea name="pickup_address" id="pickup_address" rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('pickup_address', $pickupRequest->pickup_address) }}</textarea>
                    @error('pickup_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Produk -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Produk</h3>
                <div id="product-container">
                    @foreach($pickupRequest->items as $index => $item)
                        <div class="product-item grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Produk</label>
                                <select name="items[{{ $index }}][product_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                data-price="{{ $product->price }}" 
                                                data-weight="{{ $product->weight_per_pcs }}"
                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                                <input type="number" name="items[{{ $index }}][quantity]" min="1" 
                                       value="{{ old('items.'.$index.'.quantity', $item->quantity) }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <button type="button" class="remove-product px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" 
                                        style="{{ count($pickupRequest->items) <= 1 ? 'display: none;' : '' }}">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-product" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Tambah Produk
                </button>
            </div>

            <!-- Informasi Pengiriman dan Pembayaran -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengiriman & Pembayaran</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="shipping_cost" class="block text-sm font-medium text-gray-700">Biaya Pengiriman</label>
                        <input type="number" name="shipping_cost" id="shipping_cost" 
                               value="{{ old('shipping_cost', $pickupRequest->shipping_cost) }}" 
                               min="0" step="0.01" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('shipping_cost')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="service_fee" class="block text-sm font-medium text-gray-700">Biaya Layanan</label>
                        <input type="number" name="service_fee" id="service_fee" 
                               value="{{ old('service_fee', $pickupRequest->service_fee) }}" 
                               min="0" step="0.01" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('service_fee')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Pilih Metode</option>
                            <option value="balance" {{ old('payment_method', $pickupRequest->payment_method) === 'balance' ? 'selected' : '' }}>Saldo</option>
                            <option value="wallet" {{ old('payment_method', $pickupRequest->payment_method) === 'wallet' ? 'selected' : '' }}>Wallet</option>
                            <option value="cod" {{ old('payment_method', $pickupRequest->payment_method) === 'cod' ? 'selected' : '' }}>COD</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="courier_service" class="block text-sm font-medium text-gray-700">Jasa Kurir</label>
                        <input type="text" name="courier_service" id="courier_service" 
                               value="{{ old('courier_service', $pickupRequest->courier_service) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('courier_service')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $pickupRequest->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('seller.pickup-request.show', $pickupRequest->id) }}" 
                   class="px-6 py-3 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Update Pickup Request
                </button>
            </div>
        </form>
    </div>

    <script>
        let productIndex = {{ count($pickupRequest->items) }};

        document.getElementById('add-product').addEventListener('click', function() {
            const container = document.getElementById('product-container');
            // Buat variabel untuk menyimpan data produk
            const productOptions = {!! json_encode($products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'weight_per_pcs' => $product->weight_per_pcs ?? 0
                ];
            })) !!};

            let optionsHtml = '<option value="">Pilih Produk</option>';
            productOptions.forEach(product => {
                optionsHtml += `<option value="${product.id}" data-price="${product.price}" data-weight="${product.weight_per_pcs}">${product.name} - Rp ${product.price.toLocaleString('id-ID')}</option>`;
            });

            const newProductItem = `
                <div class="product-item grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Produk</label>
                        <select name="items[${productIndex}][product_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            ${optionsHtml}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                        <input type="number" name="items[${productIndex}][quantity]" min="1" value="1" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <button type="button" class="remove-product px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Hapus
                        </button>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', newProductItem);
            productIndex++;

            updateRemoveButtons();
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-product')) {
                e.target.closest('.product-item').remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const productItems = document.querySelectorAll('.product-item');
            const removeButtons = document.querySelectorAll('.remove-product');
            
            if (productItems.length > 1) {
                removeButtons.forEach(button => button.style.display = 'block');
            } else {
                removeButtons.forEach(button => button.style.display = 'none');
            }
        }
    </script>

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
</x-layouts.plain-app>