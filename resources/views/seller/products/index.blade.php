<x-layouts.plain-app>
    <x-slot name="title">Kelola Produk</x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-neutral-900 mb-4">Kelola Produk</h1>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Produk -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-secondary-100 text-secondary-600">
                            <i class="fas fa-boxes text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600">Total Produk</p>
                            <p class="text-2xl font-bold text-neutral-900">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Produk Aktif -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-success-100 text-success-600">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600">Produk Aktif</p>
                            <p class="text-2xl font-bold text-neutral-900">{{ $stats['active'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Produk Nonaktif -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-error-100 text-error-600">
                            <i class="fas fa-times-circle text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600">Produk Nonaktif</p>
                            <p class="text-2xl font-bold text-neutral-900">{{ $stats['inactive'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <!-- Search Form -->
            <div class="flex-1 ">
                <form action="{{ route('seller.products.index') }}" method="GET" class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-neutral-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari produk..."
                        class="w-full pl-10 pr-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                </form>
            </div>

            <!-- Add Product Button -->
            <a href="{{ route('seller.products.create') }}"
                class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-5 rounded-lg transition shadow-md hover:shadow-lg flex items-center">
                <i class="fas fa-plus-circle mr-2"></i>
                Tambah Produk
            </a>
        </div>

        <!-- Products Table -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            @if ($products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Produk</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Berat/Harga</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-200">
                            @foreach ($products as $product)
                                <tr class="hover:bg-neutral-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if ($product->image)
                                                <div class="flex-shrink-0 h-10 w-10 mr-3">
                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                        src="{{ $product->image }}" alt="{{ $product->name }}">
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-neutral-900">{{ $product->name }}
                                                </div>
                                                <div class="text-sm text-neutral-500">
                                                    {{ Str::limit($product->description, 50) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-neutral-900">{{ $product->weight_per_pcs }} kg</div>
                                        <div class="text-sm font-medium text-success-600">Rp
                                            {{ number_format($product->price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-success-100 text-success-800' : 'bg-error-100 text-error-800' }}">
                                            <i
                                                class="fas {{ $product->is_active ? 'fa-check-circle mr-1' : 'fa-times-circle mr-1' }}"></i>
                                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('seller.products.show', $product->id) }}"
                                                class="text-secondary-600 hover:text-secondary-800 transition"
                                                x-data="{ tooltip: 'Detail' }" x-tooltip="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('seller.products.edit', $product->id) }}"
                                                class="text-primary-600 hover:text-primary-800 transition"
                                                x-data="{ tooltip: 'Edit' }" x-tooltip="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('seller.products.toggle-status', $product->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-warning-800 transition"
                                                    x-data="{ tooltip: '{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}' }" x-tooltip="tooltip">
                                                    <i
                                                        class="fas {{ $product->is_active ? 'fa-toggle-on text-green-600' : 'fa-toggle-off' }}"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('seller.products.destroy', $product->id) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-error-600 hover:text-error-800 transition"
                                                    x-data="{ tooltip: 'Hapus' }" x-tooltip="tooltip">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{-- @if ($products->hasPages())
                    <div class="px-6 py-4 border-t border-neutral-200 bg-neutral-50">
                        {{ $products->links() }}
                    </div>
                @endif --}}
            @else
                <div class="text-center py-12">
                    <div class="text-neutral-300 text-6xl mb-4">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3 class="mt-2 text-lg font-medium text-neutral-900">Tidak ada produk</h3>
                    <p class="mt-1 text-sm text-neutral-500">
                        @if ($search)
                            Tidak ada produk yang cocok dengan pencarian "{{ $search }}".
                        @else
                            Mulai dengan menambahkan produk pertama Anda.
                        @endif
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('seller.products.create') }}"
                            class="inline-flex items-center px-5 py-2.5 border border-transparent shadow-md text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Tambah Produk
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.plain-app>
