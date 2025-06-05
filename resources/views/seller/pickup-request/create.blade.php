<x-layouts.plain-app>
    <x-slot name="title">Buat Pickup Request</x-slot>
    
    <!-- Tambahkan meta tag untuk CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Buat Pickup Request</h1>
                    <p class="mt-2 text-gray-600">Buat permintaan pickup baru untuk produk Anda</p>
                </div>
                <a href="{{ route('seller.pickup-request.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('seller.pickup-request.store') }}" class="space-y-8">
            @csrf
            
            <!-- Informasi Penerima -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Penerima</h3>
                
                <!-- Map untuk penerima -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Lokasi di Peta</label>
                    <div id="recipient-map" class="w-full h-64 bg-gray-200 rounded-md mb-2"></div>
                    <p class="text-sm text-gray-500">Klik pada peta untuk memilih lokasi, atau isi alamat di bawah untuk pencarian otomatis</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="recipient_name" class="block text-sm font-medium text-gray-700">Nama Penerima</label>
                        <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('recipient_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="recipient_phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="text" name="recipient_phone" id="recipient_phone" value="{{ old('recipient_phone') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('recipient_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="recipient_city" class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="recipient_city" id="recipient_city" value="{{ old('recipient_city') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('recipient_city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="recipient_province" class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <input type="text" name="recipient_province" id="recipient_province" value="{{ old('recipient_province') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('recipient_province')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="recipient_postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                        <input type="text" name="recipient_postal_code" id="recipient_postal_code" value="{{ old('recipient_postal_code') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('recipient_postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <button type="button" id="geocode-recipient" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Cari di Peta
                        </button>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="recipient_address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                    <textarea name="recipient_address" id="recipient_address" rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('recipient_address') }}</textarea>
                    @error('recipient_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hidden fields untuk koordinat -->
                <input type="hidden" name="recipient_latitude" id="recipient_latitude" value="{{ old('recipient_latitude') }}">
                <input type="hidden" name="recipient_longitude" id="recipient_longitude" value="{{ old('recipient_longitude') }}">
                
                <!-- Info koordinat -->
                <div class="mt-2 text-sm text-gray-600">
                    <span>Koordinat: </span>
                    <span id="recipient-coordinates">Belum dipilih</span>
                </div>
            </div>

            <!-- Informasi Pickup -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Lokasi Pickup</h3>
                
                <!-- Map untuk pickup -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Lokasi Pickup di Peta</label>
                    <div id="pickup-map" class="w-full h-64 bg-gray-200 rounded-md mb-2"></div>
                    <p class="text-sm text-gray-500">Klik pada peta untuk memilih lokasi pickup</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="pickup_name" class="block text-sm font-medium text-gray-700">Nama Pengirim</label>
                        <input type="text" name="pickup_name" id="pickup_name" value="{{ old('pickup_name') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('pickup_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pickup_phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="text" name="pickup_phone" id="pickup_phone" value="{{ old('pickup_phone') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('pickup_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pickup_city" class="block text-sm font-medium text-gray-700">Kota</label>
                        <input type="text" name="pickup_city" id="pickup_city" value="{{ old('pickup_city') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('pickup_city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pickup_province" class="block text-sm font-medium text-gray-700">Provinsi</label>
                        <input type="text" name="pickup_province" id="pickup_province" value="{{ old('pickup_province') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('pickup_province')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pickup_postal_code" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                        <input type="text" name="pickup_postal_code" id="pickup_postal_code" value="{{ old('pickup_postal_code') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('pickup_postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <button type="button" id="geocode-pickup" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Cari di Peta
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="pickup_address" class="block text-sm font-medium text-gray-700">Alamat Pickup</label>
                    <textarea name="pickup_address" id="pickup_address" rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('pickup_address') }}</textarea>
                    @error('pickup_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hidden fields untuk koordinat pickup -->
                <input type="hidden" name="pickup_latitude" id="pickup_latitude" value="{{ old('pickup_latitude') }}">
                <input type="hidden" name="pickup_longitude" id="pickup_longitude" value="{{ old('pickup_longitude') }}">
                
                <!-- Info koordinat pickup -->
                <div class="mt-2 text-sm text-gray-600">
                    <span>Koordinat: </span>
                    <span id="pickup-coordinates">Belum dipilih</span>
                </div>
            </div>

            <!-- Produk -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pilih Produk</h3>
                <div id="product-container">
                    <div class="product-item grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Produk</label>
                            <select name="items[0][product_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Pilih Produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-weight="{{ $product->weight_per_pcs }}">
                                        {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                            <input type="number" name="items[0][quantity]" min="1" value="1" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <button type="button" class="remove-product px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" style="display: none;">
                                Hapus
                            </button>
                        </div>
                    </div>
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
                        <input type="number" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', 0) }}" min="0" step="0.01" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('shipping_cost')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="service_fee" class="block text-sm font-medium text-gray-700">Biaya Layanan (Opsional)</label>
                        <input type="number" name="service_fee" id="service_fee" value="{{ old('service_fee', 0) }}" min="0" step="0.01" 
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
                            <option value="wallet" {{ old('payment_method') === 'wallet' ? 'selected' : '' }}>Wallet</option>
                            <option value="cod" {{ old('payment_method') === 'cod' ? 'selected' : '' }}>COD</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="courier_service" class="block text-sm font-medium text-gray-700">Jasa Kurir (Opsional)</label>
                        <input type="text" name="courier_service" id="courier_service" value="{{ old('courier_service') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('courier_service')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('seller.pickup-request.index') }}" 
                   class="px-6 py-3 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Buat Pickup Request
                </button>
            </div>
            
        </form>
    </div>

    <!-- Load Google Maps API -->
    <script async defer 
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMaps&libraries=places">
    </script>

   <!-- Perbaikan untuk bagian script di HTML -->
<script>
    let recipientMap, pickupMap;
    let recipientMarker, pickupMarker;
    let mapsInitialized = false;
    
    // Initialize Google Maps
    function initMaps() {
        // Default ke Jakarta jika tidak ada koordinat
        const defaultCenter = { lat: -6.2088, lng: 106.8456 };
        
        // Initialize recipient map
        recipientMap = new google.maps.Map(document.getElementById('recipient-map'), {
            zoom: 13,
            center: defaultCenter
        });
        
        // Initialize pickup map
        pickupMap = new google.maps.Map(document.getElementById('pickup-map'), {
            zoom: 13,
            center: defaultCenter
        });
        
        // Add click listeners
        recipientMap.addListener('click', function(event) {
            setRecipientMarker(event.latLng);
            reverseGeocodeRecipient(event.latLng.lat(), event.latLng.lng());
        });
        
        pickupMap.addListener('click', function(event) {
            setPickupMarker(event.latLng);
            reverseGeocodePickup(event.latLng.lat(), event.latLng.lng());
        });
        
        // Set flag bahwa maps sudah diinisialisasi
        mapsInitialized = true;
        
        // Load existing coordinates jika ada
        loadExistingCoordinates();
    }
    
    // Set recipient marker
    function setRecipientMarker(location) {
        if (!mapsInitialized) return;
        
        if (recipientMarker) {
            recipientMarker.setMap(null);
        }
        
        recipientMarker = new google.maps.Marker({
            position: location,
            map: recipientMap,
            title: 'Lokasi Penerima'
        });
        
        // Update hidden fields
        document.getElementById('recipient_latitude').value = location.lat();
        document.getElementById('recipient_longitude').value = location.lng();
        
        // Update coordinate display
        document.getElementById('recipient-coordinates').textContent = 
            `${location.lat().toFixed(6)}, ${location.lng().toFixed(6)}`;
    }
    
    // Set pickup marker
    function setPickupMarker(location) {
        if (!mapsInitialized) return;
        
        if (pickupMarker) {
            pickupMarker.setMap(null);
        }
        
        pickupMarker = new google.maps.Marker({
            position: location,
            map: pickupMap,
            title: 'Lokasi Pickup'
        });
        
        // Update hidden fields
        document.getElementById('pickup_latitude').value = location.lat();
        document.getElementById('pickup_longitude').value = location.lng();
        
        // Update coordinate display
        document.getElementById('pickup-coordinates').textContent = 
            `${location.lat().toFixed(6)}, ${location.lng().toFixed(6)}`;
    }
    
    // Reverse geocode untuk recipient
    async function reverseGeocodeRecipient(lat, lng) {
        try {
            const response = await fetch('/api/maps/reverse-geocode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    latitude: lat,
                    longitude: lng
                })
            });
            
            const result = await response.json();
            if (result.success) {
                const data = result.data;
                document.getElementById('recipient_city').value = data.city || '';
                document.getElementById('recipient_province').value = data.province || '';
                document.getElementById('recipient_postal_code').value = data.postal_code || '';
                
                // Update alamat jika kosong
                if (!document.getElementById('recipient_address').value) {
                    document.getElementById('recipient_address').value = data.formatted_address || '';
                }
            }
        } catch (error) {
            console.error('Reverse geocoding error:', error);
        }
    }
    
    // Reverse geocode untuk pickup
    async function reverseGeocodePickup(lat, lng) {
        try {
            const response = await fetch('/api/maps/reverse-geocode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    latitude: lat,
                    longitude: lng
                })
            });
            
            const result = await response.json();
            if (result.success) {
                const data = result.data;
                document.getElementById('pickup_city').value = data.city || '';
                document.getElementById('pickup_province').value = data.province || '';
                document.getElementById('pickup_postal_code').value = data.postal_code || '';
                
                // Update alamat jika kosong
                if (!document.getElementById('pickup_address').value) {
                    document.getElementById('pickup_address').value = data.formatted_address || '';
                }
            }
        } catch (error) {
            console.error('Reverse geocoding error:', error);
        }
    }
    
    // Geocode recipient address
    async function geocodeRecipient() {
        const address = document.getElementById('recipient_address').value;
        const city = document.getElementById('recipient_city').value;
        const province = document.getElementById('recipient_province').value;
        const postalCode = document.getElementById('recipient_postal_code').value;
        
        if (!address && !city) {
            alert('Silakan isi alamat atau kota terlebih dahulu');
            return;
        }
        
        try {
            const response = await fetch('/api/maps/geocode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    address: address,
                    city: city,
                    province: province,
                    postal_code: postalCode
                })
            });
            
            const result = await response.json();
            if (result.success) {
                const data = result.data;
                const location = new google.maps.LatLng(data.latitude, data.longitude);
                
                // Set marker dan center map
                setRecipientMarker(location);
                recipientMap.setCenter(location);
                recipientMap.setZoom(16);
                
                // Update form fields
                document.getElementById('recipient_city').value = data.city || city;
                document.getElementById('recipient_province').value = data.province || province;
                document.getElementById('recipient_postal_code').value = data.postal_code || postalCode;
            } else {
                alert('Alamat tidak ditemukan. Silakan coba dengan alamat yang lebih spesifik.');
            }
        } catch (error) {
            console.error('Geocoding error:', error);
            alert('Terjadi kesalahan saat mencari alamat');
        }
    }
    
    // Geocode pickup address
    async function geocodePickup() {
        const address = document.getElementById('pickup_address').value;
        const city = document.getElementById('pickup_city').value;
        const province = document.getElementById('pickup_province').value;
        const postalCode = document.getElementById('pickup_postal_code').value;
        
        if (!address && !city) {
            alert('Silakan isi alamat atau kota terlebih dahulu');
            return;
        }
        
        try {
            const response = await fetch('/api/maps/geocode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    address: address,
                    city: city,
                    province: province,
                    postal_code: postalCode
                })
            });
            
            const result = await response.json();
            if (result.success) {
                const data = result.data;
                const location = new google.maps.LatLng(data.latitude, data.longitude);
                
                // Set marker dan center map
                setPickupMarker(location);
                pickupMap.setCenter(location);
                pickupMap.setZoom(16);
                
                // Update form fields
                document.getElementById('pickup_city').value = data.city || city;
                document.getElementById('pickup_province').value = data.province || province;
                document.getElementById('pickup_postal_code').value = data.postal_code || postalCode;
            } else {
                alert('Alamat tidak ditemukan. Silakan coba dengan alamat yang lebih spesifik.');
            }
        } catch (error) {
            console.error('Geocoding error:', error);
            alert('Terjadi kesalahan saat mencari alamat');
        }
    }
    
    // Load existing coordinates jika ada (untuk edit mode)
    function loadExistingCoordinates() {
        if (!mapsInitialized) return;
        
        const recipientLat = document.getElementById('recipient_latitude').value;
        const recipientLng = document.getElementById('recipient_longitude').value;
        const pickupLat = document.getElementById('pickup_latitude').value;
        const pickupLng = document.getElementById('pickup_longitude').value;
        
        if (recipientLat && recipientLng) {
            const location = new google.maps.LatLng(parseFloat(recipientLat), parseFloat(recipientLng));
            setRecipientMarker(location);
            recipientMap.setCenter(location);
            recipientMap.setZoom(16);
        }
        
        if (pickupLat && pickupLng) {
            const location = new google.maps.LatLng(parseFloat(pickupLat), parseFloat(pickupLng));
            setPickupMarker(location);
            pickupMap.setCenter(location);
            pickupMap.setZoom(16);
        }
    }
    
    // Setup event listeners setelah DOM loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Event listeners untuk tombol geocode
        document.getElementById('geocode-recipient').addEventListener('click', geocodeRecipient);
        document.getElementById('geocode-pickup').addEventListener('click', geocodePickup);
        
        // Throttled auto-complete (dengan debounce yang lebih baik)
        let recipientTimeout, pickupTimeout;
        
        // Auto-complete untuk recipient
        const recipientFields = [
            document.getElementById('recipient_address'),
            document.getElementById('recipient_city'),
            document.getElementById('recipient_province'),
            document.getElementById('recipient_postal_code')
        ];
        
        recipientFields.forEach(field => {
            field.addEventListener('input', function() {
                clearTimeout(recipientTimeout);
                recipientTimeout = setTimeout(() => {
                    const hasValue = recipientFields.some(f => f.value.trim().length > 2);
                    if (hasValue && mapsInitialized) {
                        geocodeRecipient();
                    }
                }, 2000); // Tunggu 2 detik setelah user berhenti mengetik
            });
        });
        
        // Auto-complete untuk pickup
        const pickupFields = [
            document.getElementById('pickup_address'),
            document.getElementById('pickup_city'),
            document.getElementById('pickup_province'),
            document.getElementById('pickup_postal_code')
        ];
        
        pickupFields.forEach(field => {
            field.addEventListener('input', function() {
                clearTimeout(pickupTimeout);
                pickupTimeout = setTimeout(() => {
                    const hasValue = pickupFields.some(f => f.value.trim().length > 2);
                    if (hasValue && mapsInitialized) {
                        geocodePickup();
                    }
                }, 2000); // Tunggu 2 detik setelah user berhenti mengetik
            });
        });
        
        // Product management code (tidak diubah)
        setupProductManagement();
    });
    
    // Fungsi untuk setup product management
    function setupProductManagement() {
        let productIndex = 1;
        
        document.getElementById('add-product').addEventListener('click', function() {
            const container = document.getElementById('product-container');
            
            const productOptions = {!! json_encode($products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'weight_per_pcs' => $product->weight_per_pcs ?? 0
                ];
            })) !!};
            
            let optionsHtml = '<option value="">Pilih Produk</option>';
            productOptions.forEach(function(product) {
                const formattedPrice = new Intl.NumberFormat('id-ID').format(product.price);
                optionsHtml += `<option value="${product.id}" data-price="${product.price}" data-weight="${product.weight_per_pcs}">${product.name} - Rp ${formattedPrice}</option>`;
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
        
        // Event listener untuk tombol hapus
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-product')) {
                e.target.closest('.product-item').remove();
                updateRemoveButtons();
            }
        });
        
        // Fungsi untuk mengatur visibility tombol hapus
        function updateRemoveButtons() {
            const productItems = document.querySelectorAll('.product-item');
            const removeButtons = document.querySelectorAll('.remove-product');
            
            if (productItems.length > 1) {
                removeButtons.forEach(function(button) {
                    button.style.display = 'block';
                });
            } else {
                removeButtons.forEach(function(button) {
                    button.style.display = 'none';
                });
            }
        }
        
        // Initialize remove buttons
        updateRemoveButtons();
    }
</script>

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
</x-layouts.plain-app>