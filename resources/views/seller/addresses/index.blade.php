{{-- resources/views/seller/addresses/index.blade.php --}}
<x-layouts.plain-app>
    <x-slot name="title">Kelola Alamat</x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900">Kelola Alamat</h1>
                    <p class="mt-2 text-neutral-600">Atur alamat untuk keperluan pickup request</p>
                </div>
                <a href="{{ route('seller.addresses.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Alamat
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-md bg-red-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Address Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-md p-4 border border-neutral-200">
                <div class="flex items-center">
                    <div class="bg-primary-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-map-marker-alt text-primary-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-neutral-900">{{ $addresses->count() }}</p>
                        <p class="text-sm text-neutral-600">Total Alamat</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border border-neutral-200">
                <div class="flex items-center">
                    <div class="bg-success-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-star text-success-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-neutral-900">{{ $addresses->where('is_default', true)->count() }}</p>
                        <p class="text-sm text-neutral-600">Alamat Default</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border border-neutral-200">
                <div class="flex items-center">
                    <div class="bg-warning-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-map text-warning-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-neutral-900">{{ $addresses->whereNotNull('latitude')->count() }}</p>
                        <p class="text-sm text-neutral-600">Dengan Koordinat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($addresses as $address)
                <div class="bg-white rounded-xl shadow-md p-6 border border-neutral-200 relative hover:shadow-lg transition-shadow">
                    @if($address->is_default)
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                <i class="fas fa-star mr-1"></i>
                                Default
                            </span>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-neutral-900">{{ $address->label }}</h3>
                        <p class="text-neutral-600">{{ $address->name }}</p>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-neutral-600">
                            <i class="fas fa-phone w-4 mr-2"></i>
                            {{ $address->phone }}
                        </div>
                        <div class="flex items-center text-sm text-neutral-600">
                            <i class="fas fa-map-marker-alt w-4 mr-2"></i>
                            {{ $address->city }}, {{ $address->province }}
                        </div>
                        <div class="flex items-start text-sm text-neutral-600">
                            <i class="fas fa-home w-4 mr-2 mt-1"></i>
                            <div class="line-clamp-2">
                                {{ Str::limit($address->address, 80) }}
                            </div>
                        </div>
                        @if($address->latitude && $address->longitude)
                            <div class="flex items-center text-sm text-success-600">
                                <i class="fas fa-map w-4 mr-2"></i>
                                Koordinat tersedia
                            </div>
                        @else
                            <div class="flex items-center text-sm text-warning-600">
                                <i class="fas fa-exclamation-triangle w-4 mr-2"></i>
                                Belum ada koordinat
                            </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex justify-between items-center pt-4 border-t border-neutral-200">
                        <a href="{{ route('seller.addresses.show', $address) }}" 
                           class="text-primary-600 hover:text-primary-700 text-sm font-medium transition-colors">
                            <i class="fas fa-eye mr-1"></i>
                            Detail
                        </a>
                        <div class="flex space-x-2">
                            @if(!$address->is_default)
                                <form action="{{ route('seller.addresses.set-default', $address) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-success-600 hover:text-success-700 text-sm"
                                            title="Jadikan Default"
                                            onclick="return confirm('Jadikan alamat ini sebagai default?')">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('seller.addresses.edit', $address) }}" 
                               class="text-secondary-600 hover:text-secondary-700 text-sm"
                               title="Edit Alamat">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('seller.addresses.destroy', $address) }}" 
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-error-600 hover:text-error-700 text-sm"
                                        title="Hapus Alamat">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Extended Actions -->
                    <div class="mt-3 pt-3 border-t border-neutral-100">
                        <div class="flex gap-2">
                            <a href="{{ route('seller.addresses.show', $address) }}" 
                               class="flex-1 text-center px-3 py-2 text-xs bg-primary-50 text-primary-700 rounded-md hover:bg-primary-100 transition-colors">
                                Lihat Detail
                            </a>
                            <a href="{{ route('seller.addresses.edit', $address) }}" 
                               class="flex-1 text-center px-3 py-2 text-xs bg-secondary-50 text-secondary-700 rounded-md hover:bg-secondary-100 transition-colors">
                                Edit Alamat
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-map-marker-alt text-6xl text-neutral-300 mb-6"></i>
                    <h3 class="text-xl font-medium text-neutral-900 mb-2">Belum ada alamat</h3>
                    <p class="text-neutral-600 mb-6 max-w-6xl mx-auto">Tambahkan alamat pertama Anda untuk keperluan pickup request. Alamat dengan koordinat akan memudahkan kurir menemukan lokasi.</p>
                    <a href="{{ route('seller.addresses.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors shadow-sm">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Alamat Pertama
                    </a>
                </div>
            @endforelse
        </div>

        @if($addresses->count() > 0)
        <!-- Tips Section -->
        <div class="mt-8 bg-blue-50 rounded-xl p-6 border border-blue-200">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-lightbulb text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Tips Mengelola Alamat</h3>
                    <ul class="space-y-2 text-blue-800">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-2"></i>
                            <span>Pastikan setiap alamat memiliki koordinat untuk memudahkan kurir</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-2"></i>
                            <span>Gunakan label yang jelas seperti "Rumah", "Kantor", atau "Toko"</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-2"></i>
                            <span>Alamat default akan otomatis terpilih saat membuat pickup request</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mt-1 mr-2"></i>
                            <span>Perbarui informasi alamat jika ada perubahan</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-layouts.plain-app>