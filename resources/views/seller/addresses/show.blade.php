{{-- resources/views/seller/addresses/show.blade.php --}}
<x-layouts.plain-app>
    <x-slot name="title">Detail Alamat</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900">Detail Alamat</h1>
                    <p class="mt-2 text-neutral-600">Detail informasi alamat untuk keperluan pickup request</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('seller.addresses.edit', $address) }}"
                        class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors shadow-sm">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Alamat
                    </a>
                    <a href="{{ route('seller.addresses.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary-600 transition-colors shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Address Information -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-neutral-200">
                <div class="flex items-center mb-6">
                    <div class="bg-primary-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-map-marker-alt text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900">{{ $address->label }}</h3>
                        @if($address->is_default)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 mt-1">
                                <i class="fas fa-star mr-1"></i>
                                Alamat Default
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-1">Nama Penerima</label>
                            <p class="text-neutral-900">{{ $address->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-1">Nomor Telepon</label>
                            <p class="text-neutral-900">{{ $address->phone }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-1">Kota</label>
                            <p class="text-neutral-900">{{ $address->city }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-1">Provinsi</label>
                            <p class="text-neutral-900">{{ $address->province }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-1">Kode Pos</label>
                            <p class="text-neutral-900">{{ $address->postal_code }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-1">Koordinat</label>
                            <p class="text-neutral-900">
                                @if($address->latitude && $address->longitude)
                                    {{ $address->latitude }}, {{ $address->longitude }}
                                @else
                                    <span class="text-neutral-400">Tidak tersedia</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-1">Alamat Lengkap</label>
                        <p class="text-neutral-900 leading-relaxed">{{ $address->address }}</p>
                    </div>

                    <div class="pt-4 border-t border-neutral-200">
                        <div class="grid grid-cols-2 gap-4 text-sm text-neutral-600">
                            <div>
                                <strong>Dibuat:</strong><br>
                                {{ $address->created_at->format('d M Y, H:i') }}
                            </div>
                            <div>
                                <strong>Terakhir diubah:</strong><br>
                                {{ $address->updated_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 mt-6 pt-4 border-t border-neutral-200">
                    <a href="{{ route('seller.addresses.edit', $address) }}"
                        class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors text-sm">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Alamat
                    </a>
                    <form action="{{ route('seller.addresses.destroy', $address) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-error-600 text-white rounded-lg hover:bg-error-700 transition-colors text-sm">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Alamat
                        </button>
                    </form>
                </div>
            </div>

            <!-- Map Display -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-neutral-200">
                <div class="flex items-center mb-4">
                    <div class="bg-success-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-map text-success-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900">Lokasi di Peta</h3>
                </div>
                
                @if($address->latitude && $address->longitude)
                    <div id="address-map" class="w-full h-96 rounded-lg border border-gray-300" 
                         x-data="addressMap()" x-init="initGoogleMaps()"></div>
                @else
                    <div class="w-full h-96 rounded-lg border border-gray-300 bg-gray-50 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-map-marker-alt text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-500 mb-2">Lokasi Tidak Tersedia</h4>
                            <p class="text-gray-400 mb-4">Alamat ini belum memiliki koordinat lokasi</p>
                            <a href="{{ route('seller.addresses.edit', $address) }}"
                                class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors">
                                <i class="fas fa-edit mr-2"></i>
                                Edit untuk Menambah Lokasi
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($address->latitude && $address->longitude)
    <script>
        function addressMap() {
            return {
                map: null,
                marker: null,

                initGoogleMaps() {
                    if (typeof google !== 'undefined' && google.maps) {
                        this.setupMap();
                    } else {
                        const script = document.createElement('script');
                        script.src = `https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMapCallback`;
                        script.defer = true;
                        document.head.appendChild(script);
                        
                        window.initMapCallback = () => {
                            this.setupMap();
                        };
                    }
                },

                setupMap() {
                    const position = {
                        lat: parseFloat({{ $address->latitude }}),
                        lng: parseFloat({{ $address->longitude }})
                    };

                    this.map = new google.maps.Map(document.getElementById('address-map'), {
                        center: position,
                        zoom: 15,
                        mapTypeControl: true,
                        streetViewControl: true,
                        fullscreenControl: true
                    });

                    this.marker = new google.maps.Marker({
                        position: position,
                        map: this.map,
                        title: '{{ $address->label }} - {{ $address->name }}',
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
                        }
                    });

                    // Info window
                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div class="p-2">
                                <h6 class="font-semibold text-gray-900">{{ $address->label }}</h6>
                                <p class="text-sm text-gray-600 mt-1">{{ $address->name }}</p>
                                <p class="text-sm text-gray-600">{{ $address->phone }}</p>
                                <p class="text-sm text-gray-500 mt-2">{{ $address->address }}</p>
                            </div>
                        `
                    });

                    this.marker.addListener('click', () => {
                        infoWindow.open(this.map, this.marker);
                    });

                    // Auto open info window
                    setTimeout(() => {
                        infoWindow.open(this.map, this.marker);
                    }, 500);
                }
            }
        }
    </script>
    @endif
</x-layouts.plain-app>