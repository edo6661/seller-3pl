<x-layouts.plain-app>
    <x-slot name="title">Detail Produk - {{ $product->name }}</x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('seller.products.index') }}"
                            class="inline-flex items-center text-sm font-medium text-secondary-600 hover:text-secondary-800 transition">
                            <i class="fas fa-boxes mr-2"></i>
                            Kelola Produk
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-neutral-400 mx-2"></i>
                            <span
                                class="ml-1 text-sm font-medium text-neutral-900 md:ml-2">{{ Str::limit($product->name, 30) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4">
                        <h1 class="text-2xl font-bold text-neutral-900">{{ $product->name }}</h1>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-success-100 text-success-800' : 'bg-error-100 text-error-800' }}">
                            <i
                                class="fas {{ $product->is_active ? 'fa-check-circle mr-1' : 'fa-times-circle mr-1' }}"></i>
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <div class="flex items-center text-neutral-500">
                            <i class="fas fa-calendar-day mr-2"></i>
                            Dibuat: {{ $product->created_at->translatedFormat('d M Y') }}
                        </div>
                        <div class="flex items-center text-neutral-500">
                            <i class="fas fa-history mr-2"></i>
                            Diupdate: {{ $product->updated_at->diffForHumans() }}
                        </div>
                        <div class="flex items-center text-neutral-500">
                            <i class="fas fa-barcode mr-2"></i>
                            ID: {{ $product->id }}
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('seller.products.edit', $product->id) }}"
                        class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-5 rounded-lg transition shadow-md hover:shadow-lg flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Produk
                    </a>

                    <form action="{{ route('seller.products.toggle-status', $product->id) }}" method="POST"
                        class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="bg-blue-600 hover:bg-warning-700 text-white font-medium py-2.5 px-5 rounded-lg transition shadow-md hover:shadow-lg flex items-center">
                            <i class="fas {{ $product->is_active ? 'fa-toggle-on mr-2' : 'fa-toggle-off mr-2' }}"></i>
                            {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Product Details -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Product Image & Basic Info -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="md:flex">
                            {{-- <div class="md:w-1/3 bg-neutral-100 flex items-center justify-center p-6">
                                @if ($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                        class="h-48 w-full object-contain">
                                @else
                                    <div class="text-neutral-300 text-6xl">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div> --}}
                            <div class="p-6 md:w-2/3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h2 class="text-xl font-bold text-neutral-900 mb-2">{{ $product->name }}</h2>
                                        <div class="flex items-center mb-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 mr-2">
                                                <i class="fas fa-tag mr-1"></i>
                                                {{ $product->category->name ?? 'Tanpa Kategori' }}
                                            </span>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary-100 text-secondary-800">
                                                <i class="fas fa-box mr-1"></i> {{ $product->stock }} stok
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-2xl font-bold text-success-600">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="border-t border-neutral-200 pt-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-neutral-500">Berat per Pcs</p>
                                            <p class="font-medium text-neutral-900">{{ $product->weight_per_pcs }} kg
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-neutral-500">Dimensi</p>
                                            <p class="font-medium text-neutral-900">
                                                {{ $product->length }}x{{ $product->width }}x{{ $product->height }}
                                                cm</p>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description & Details -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="border-b border-neutral-200 pb-4 mb-4">
                            <h2 class="text-xl font-semibold text-neutral-900">Deskripsi Produk</h2>
                        </div>
                        <div class="prose max-w-none text-neutral-700">
                            {!! $product->description
                                ? nl2br(e($product->description))
                                : '<p class="text-neutral-400">Tidak ada deskripsi</p>' !!}
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="border-b border-neutral-200 pb-4 mb-4">
                            <h3 class="text-lg font-semibold text-neutral-900">Aksi Cepat</h3>
                        </div>
                        <div class="space-y-3">
                            <a href="{{ route('seller.products.edit', $product->id) }}"
                                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition shadow hover:shadow-md flex items-center justify-center">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Produk
                            </a>

                            <form action="{{ route('seller.products.toggle-status', $product->id) }}" method="POST"
                                class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-warning-700 text-white font-medium py-2.5 px-4 rounded-lg transition shadow hover:shadow-md flex items-center justify-center">
                                    <i
                                        class="fas {{ $product->is_active ? 'fa-toggle-on mr-2' : 'fa-toggle-off mr-2' }}"></i>
                                    {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>

                            <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')"
                                class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full bg-error-600 hover:bg-error-700 text-white font-medium py-2.5 px-4 rounded-lg transition shadow hover:shadow-md flex items-center justify-center">
                                    <i class="fas fa-trash-alt mr-2"></i>
                                    Hapus Produk
                                </button>
                            </form>
                        </div>
                    </div>



                    <!-- Timeline -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="border-b border-neutral-200 pb-4 mb-4">
                            <h3 class="text-lg font-semibold text-neutral-900">Riwayat Produk</h3>
                        </div>
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <span
                                                    class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                                                    <i class="fas fa-plus text-primary-600"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <p class="font-medium text-neutral-900">Produk dibuat</p>
                                                    </div>
                                                    <p class="mt-0.5 text-sm text-neutral-500">
                                                        {{ $product->created_at->translatedFormat('d M Y H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <span
                                                    class="h-8 w-8 rounded-full bg-secondary-100 flex items-center justify-center">
                                                    <i class="fas fa-edit text-secondary-600"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <p class="font-medium text-neutral-900">Terakhir diupdate</p>
                                                    </div>
                                                    <p class="mt-0.5 text-sm text-neutral-500">
                                                        {{ $product->updated_at->translatedFormat('d M Y H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.plain-app>
