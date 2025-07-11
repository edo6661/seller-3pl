<x-layouts.plain-app>
    <x-slot name="title">Kelola Produk - Admin</x-slot>

    <div class="min-h-screen bg-neutral-50">
        <!-- Header Section -->
        <div class="bg-white border-b border-neutral-200">
            <div class="container mx-auto px-6 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-neutral-900 mb-2">Dashboard Produk</h1>
                        <p class="text-neutral-600">Kelola semua produk yang terdaftar dalam sistem</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-primary-50 text-primary-700 px-4 py-2 rounded-lg border border-primary-200">
                            <i class="fas fa-crown mr-2"></i>
                            Admin Panel
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-6 py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div
                    class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                            <i class="fas fa-box-open text-primary-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600 mb-1">Total Produk</p>
                            <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['total']) }}</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-xl bg-success-100 flex items-center justify-center">
                            <i class="fas fa-check-circle text-success-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600 mb-1">Produk Aktif</p>
                            <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['active']) }}</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-xl bg-error-100 flex items-center justify-center">
                            <i class="fas fa-times-circle text-error-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600 mb-1">Produk Nonaktif</p>
                            <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['inactive']) }}</p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-xl bg-secondary-100 flex items-center justify-center">
                            <i class="fas fa-users text-secondary-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600 mb-1">Penjual Aktif</p>
                            <p class="text-2xl font-bold text-neutral-900">
                                {{ number_format($stats['users_with_products']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 mb-8">
                <div class="flex items-center mb-4">
                    <i class="fas fa-filter text-neutral-600 mr-2"></i>
                    <h3 class="text-lg font-semibold text-neutral-900">Filter & Pencarian</h3>
                </div>

                <form action="{{ route('admin.products.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                <i class="fas fa-search mr-1"></i>
                                Cari Produk/Penjual
                            </label>
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Nama produk atau penjual..."
                                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                <i class="fas fa-user mr-1"></i>
                                Filter Penjual
                            </label>
                            <select name="user_id"
                                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200">
                                <option value="">Semua Penjual</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                <i class="fas fa-toggle-on mr-1"></i>
                                Status Produk
                            </label>
                            <select name="status"
                                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200">
                                <option value="">Semua Status</option>
                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Nonaktif
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end gap-3">
                            <button type="submit"
                                class="flex-1 bg-primary text-white font-medium py-3 px-4 rounded-lg hover:bg-primary-600 transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-filter mr-2"></i>
                                Filter
                            </button>
                            <a href="{{ route('admin.products.index') }}"
                                class="bg-neutral-500 hover:bg-neutral-600 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Products Table -->
            <div class="bg-white shadow-sm rounded-xl border border-neutral-200 overflow-hidden">
                @if ($products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-neutral-200">
                            <thead class="bg-neutral-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">
                                        <i class="fas fa-box mr-2"></i>
                                        Produk
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">
                                        <i class="fas fa-user mr-2"></i>
                                        Penjual
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">
                                        <i class="fas fa-weight mr-2"></i>
                                        Berat/Harga
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">
                                        <i class="fas fa-toggle-on mr-2"></i>
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-neutral-600 uppercase tracking-wider">
                                        <i class="fas fa-calendar mr-2"></i>
                                        Dibuat
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-neutral-200">
                                @foreach ($products as $product)
                                    <tr class="hover:bg-neutral-50 transition-colors duration-200">
                                        <td class="px-6 py-4">
                                            <div class="flex items-start">
                                                <div
                                                    class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-box text-primary-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-neutral-900 mb-1">
                                                        {{ $product->name }}</div>
                                                    <div class="text-sm text-neutral-600">
                                                        {{ Str::limit($product->description, 60) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-secondary-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-secondary-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-neutral-900">
                                                        {{ $product->user->name }}</div>
                                                    <div class="text-sm text-neutral-600">{{ $product->user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-1">
                                                <div class="flex items-center text-sm text-neutral-700">
                                                    <i class="fas fa-weight-hanging mr-2 text-neutral-500"></i>
                                                    {{ $product->weight_per_pcs }} kg
                                                </div>
                                                <div class="flex items-center text-sm font-semibold text-success-600">
                                                    <i class="fas fa-money-bill-wave mr-2"></i>
                                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($product->is_active)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-success-100 text-success-700 border border-success-200">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-error-100 text-error-700 border border-error-200">
                                                    <i class="fas fa-times-circle mr-1"></i>
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center text-sm text-neutral-600">
                                                <i class="fas fa-calendar-alt mr-2 text-neutral-500"></i>
                                                {{ $product->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-neutral-50 px-6 py-4 border-t border-neutral-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-neutral-600">
                                <i class="fas fa-info-circle mr-2"></i>
                                Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari
                                {{ $products->total() }} produk
                            </div>
                            <div class="pagination-wrapper">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-16">
                        <div
                            class="w-20 h-20 mx-auto mb-4 rounded-full bg-neutral-100 flex items-center justify-center">
                            <i class="fas fa-box-open text-3xl text-neutral-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-neutral-900 mb-2">Tidak ada produk ditemukan</h3>
                        <p class="text-neutral-600 max-w-md mx-auto">
                            @if ($search || $userId || $status)
                                Tidak ada produk yang cocok dengan filter yang dipilih. Coba ubah kriteria pencarian
                                Anda.
                            @else
                                Belum ada produk yang terdaftar dalam sistem. Produk akan muncul di sini setelah penjual
                                menambahkan produk.
                            @endif
                        </p>
                        @if ($search || $userId || $status)
                            <a href="{{ route('admin.products.index') }}"
                                class="inline-flex items-center mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-600 transition-colors duration-200">
                                <i class="fas fa-redo mr-2"></i>
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Custom pagination styling */
        .pagination-wrapper .pagination {
            display: flex;
            gap: 0.5rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .pagination-wrapper .pagination .page-item {
            margin: 0;
        }

        .pagination-wrapper .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--color-neutral-600);
            background-color: white;
            border: 1px solid var(--color-neutral-300);
            border-radius: var(--border-radius-lg);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pagination-wrapper .pagination .page-link:hover {
            color: var(--color-primary);
            background-color: var(--color-primary-50);
            border-color: var(--color-primary-200);
        }

        .pagination-wrapper .pagination .page-item.active .page-link {
            color: white;
            background-color: var(--color-primary);
            border-color: var(--color-primary);
        }

        .pagination-wrapper .pagination .page-item.disabled .page-link {
            color: var(--color-neutral-400);
            background-color: var(--color-neutral-100);
            border-color: var(--color-neutral-200);
            cursor: not-allowed;
        }
    </style>
</x-layouts.plain-app>
