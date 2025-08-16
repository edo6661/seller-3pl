{{-- resources/views/seller/addresses/edit.blade.php --}}
<x-layouts.plain-app>
    <x-slot name="title">Edit Alamat</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900">Edit Alamat</h1>
                    <p class="mt-2 text-neutral-600">Perbarui alamat  untuk keperluan pickup request</p>
                </div>
                <a href="{{ route('seller.addresses.index') }}"
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

        <form method="POST" action="{{ route('seller.addresses.update', $address) }}" class="space-y-6"
              x-data="addressEditForm()" x-init="initGoogleMaps()">
            @csrf
            @method('PUT')

            <!-- Form Address -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-neutral-200">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Form Fields -->
                    <div class="space-y-4">
                        <!-- Search Address -->
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                <i class="fas fa-search mr-1"></i>
                                Cari Alamat
                            </label>
                            <input type="text" 
                                   id="address-search"
                                   x-model="searchQuery"
                                   placeholder="Mulai ketik alamat..."
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="label" class="block text-sm font-medium text-neutral-700 mb-1">Label Alamat</label>
                                <input type="text" name="label" id="label"
                                       x-model="formData.label"
                                       value="{{ old('label', $address->label) }}"
                                       placeholder="Contoh: Rumah, Kantor, Toko"
                                       class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                       required>
                                @error('label')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-neutral-700 mb-1">Nama Penerima</label>
                                <input type="text" name="name" id="name"
                                       x-model="formData.name"
                                       value="{{ old('name', $address->name) }}"
                                       class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-neutral-700 mb-1">Nomor Telepon</label>
                                <input type="text" name="phone" id="phone"
                                       x-model="formData.phone"
                                       value="{{ old('phone', $address->phone) }}"
                                       class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                       required>
                                @error('phone')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-neutral-700 mb-1">Kota</label>
                                <input type="text" name="city" id="city"
                                       x-model="formData.city"
                                       value="{{ old('city', $address->city) }}"
                                       class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                       required>
                                @error('city')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="province" class="block text-sm font-medium text-neutral-700 mb-1">Provinsi</label>
                                <input type="text" name="province" id="province"
                                       x-model="formData.province"
                                       value="{{ old('province', $address->province) }}"
                                       class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                       required>
                                @error('province')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-neutral-700 mb-1">Kode Pos</label>
                                <input type="text" name="postal_code" id="postal_code"
                                       x-model="formData.postal_code"
                                       value="{{ old('postal_code', $address->postal_code) }}"
                                       class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                       required>
                                @error('postal_code')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-neutral-700 mb-1">Alamat Lengkap</label>
                            <textarea name="address" id="address"
                                      x-model="formData.address"
                                      rows="3"
                                      class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                      required>{{ old('address', $address->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hidden Coordinates -->
                        <input type="hidden" name="latitude" x-model="formData.latitude">
                        <input type="hidden" name="longitude" x-model="formData.longitude">

                        <!-- Coordinates Display -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-1">Latitude</label>
                                <input type="number" 
                                       x-model="formData.latitude"
                                       step="any"
                                       class="block w-full rounded-lg border-neutral-300 bg-gray-50"
                                       readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-1">Longitude</label>
                                <input type="number" 
                                       x-model="formData.longitude"
                                       step="any"
                                       class="block w-full rounded-lg border-neutral-300 bg-gray-50"
                                       readonly>
                            </div>
                        </div>

                        <!-- Default Address -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_default" id="is_default" value="1"
                                       {{ old('is_default', $address->is_default) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:border-primary-500 focus:ring-primary-500">
                                <label for="is_default" class="ml-2 text-sm text-neutral-700">
                                    Jadikan alamat default
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-neutral-500">
                                Alamat default akan otomatis dipilih saat membuat pickup request
                            </p>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Lokasi di Peta</label>
                            <div id="address-map" class="w-full h-80 rounded-lg border border-gray-300"></div>
                        </div>
                        
                        <div x-show="status" 
                             class="p-3 rounded-md transition-all" 
                             :class="status === 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'"
                             x-transition>
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i :class="status === 'success' ? 'fas fa-check-circle text-green-400' : 'fas fa-exclamation-triangle text-red-400'"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium" 
                                       :class="status === 'success' ? 'text-green-800' : 'text-red-800'" 
                                       x-text="message"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('seller.addresses.index') }}"
                    class="px-6 py-3 bg-neutral-200 text-neutral-700 rounded-lg hover:bg-neutral-300 transition-colors shadow-sm text-center">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors shadow-sm">
                    Update Alamat
                </button>
            </div>
        </form>
    </div>

    <script>
        function addressEditForm() {
            return {
                searchQuery: '',
                formData: {
                    label: @json(old('label', $address->label)),
                    name: @json(old('name', $address->name)),
                    phone: @json(old('phone', $address->phone)),
                    city: @json(old('city', $address->city)),
                    province: @json(old('province', $address->province)),
                    postal_code: @json(old('postal_code', $address->postal_code)),
                    address: @json(old('address', $address->address)),
                    latitude: {{ old('latitude', $address->latitude ?? -6.2088) }},
                    longitude: {{ old('longitude', $address->longitude ?? 106.8456) }}
                },
                status: '',
                message: '',
                map: null,
                marker: null,
                autocomplete: null,
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
                    this.setupMap();
                    this.setupAutocomplete();
                },

                setupMap() {
                    const initialPosition = {
                        lat: parseFloat(this.formData.latitude),
                        lng: parseFloat(this.formData.longitude)
                    };

                    this.map = new google.maps.Map(document.getElementById('address-map'), {
                        center: initialPosition,
                        zoom: 15,
                        mapTypeControl: false,
                        streetViewControl: false
                    });

                    this.marker = new google.maps.Marker({
                        position: initialPosition,
                        map: this.map,
                        draggable: true,
                        title: 'Lokasi Alamat'
                    });

                    // Event listeners
                    this.marker.addListener('dragend', (event) => {
                        this.handleMarkerDrag(event);
                    });

                    this.map.addListener('click', (event) => {
                        this.marker.setPosition(event.latLng);
                        this.handleMarkerDrag(event);
                    });
                },

                setupAutocomplete() {
                    const input = document.getElementById('address-search');
                    this.autocomplete = new google.maps.places.Autocomplete(input, {
                        types: ['address'],
                        componentRestrictions: { country: 'id' }
                    });

                    this.autocomplete.addListener('place_changed', () => {
                        this.handlePlaceChanged();
                    });
                },

                handlePlaceChanged() {
                    const place = this.autocomplete.getPlace();
                    
                    if (!place.geometry || !place.geometry.location) {
                        this.showStatus('error', 'Lokasi tidak ditemukan. Silakan coba lagi.');
                        return;
                    }

                    // Update map
                    this.map.setCenter(place.geometry.location);
                    this.map.setZoom(17);
                    this.marker.setPosition(place.geometry.location);

                    // Fill form data
                    this.fillAddressComponents(place);
                    this.setCoordinates(place.geometry.location);
                    
                    this.showStatus('success', 'Alamat berhasil ditemukan dan diisi otomatis.');
                },

                handleMarkerDrag(event) {
                    this.setCoordinates(event.latLng);
                    this.reverseGeocode(event.latLng);
                },

                reverseGeocode(location) {
                    this.geocoder.geocode({ 'location': location }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            this.fillAddressComponents(results[0]);
                            this.searchQuery = results[0].formatted_address;
                            this.showStatus('success', 'Alamat diperbarui dari lokasi di peta.');
                        } else {
                            this.showStatus('error', 'Gagal mendapatkan alamat dari lokasi.');
                        }
                    });
                },

                fillAddressComponents(place) {
                    if (place.formatted_address) {
                        this.formData.address = place.formatted_address;
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
                            this.formData.postal_code = component.long_name;
                        }
                    });

                    this.formData.city = city.replace(/Kota |Kabupaten /g, '');
                    this.formData.province = state;
                },

                setCoordinates(location) {
                    this.formData.latitude = location.lat();
                    this.formData.longitude = location.lng();
                },

                showStatus(status, message) {
                    this.status = status;
                    this.message = message;
                    
                    setTimeout(() => {
                        this.status = '';
                        this.message = '';
                    }, 5000);
                }
            }
        }
    </script>
</x-layouts.plain-app>