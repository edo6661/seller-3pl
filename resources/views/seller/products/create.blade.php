<x-layouts.plain-app>
    <x-slot name="title">Tambah Produk</x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <!-- Header with subtle background -->
            <div class="mb-8 p-5 bg-primary-50 rounded-xl">
                <h1 class="text-2xl font-bold text-neutral-900 mb-2 flex items-center">
                    <i class="fas fa-cube text-primary-600 mr-3"></i>
                    Tambah Produk Baru
                </h1>
                <p class="text-neutral-600">Lengkapi informasi produk yang akan Anda jual</p>
            </div>

            <!-- Form with card styling -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-neutral-100">
                <form action="{{ route('seller.products.store') }}" method="POST" class="p-8 space-y-8">
                    @csrf

                    <!-- Form sections with subtle separation -->
                    <div class="space-y-6">
                        <!-- Nama Produk -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-semibold text-neutral-700">
                                Nama Produk <span class="text-error-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-neutral-400"></i>
                                </div>
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
                                    class="w-full pl-10 pr-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('name') border-error-500 @enderror"
                                    placeholder="Contoh: Kaos Polos Premium" required>
                            </div>
                            @error('name')
                                <p class="mt-1 text-sm text-error-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="space-y-2">
                            <label for="description" class="block text-sm font-semibold text-neutral-700">
                                Deskripsi Produk
                            </label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 text-neutral-400">
                                    <i class="fas fa-align-left"></i>
                                </div>
                                <textarea id="description" name="description" rows="5"
                                    class="w-full pl-10 pr-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('description') border-error-500 @enderror"
                                    placeholder="Masukkan deskripsi produk (opsional)">{{ old('description') }}</textarea>
                            </div>
                            @error('description')
                                <p class="mt-1 text-sm text-error-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Berat per Pcs -->
                        <div class="space-y-2">
                            <label for="weight_per_pcs" class="block text-sm font-semibold text-neutral-700">
                                Berat per Pcs (kg) <span class="text-error-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-weight-hanging text-neutral-400"></i>
                                </div>
                                <input type="number" id="weight_per_pcs" name="weight_per_pcs"
                                    value="{{ old('weight_per_pcs') }}" step="0.01" min="0.01" required
                                    class="w-full pl-10 pr-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 @error('weight_per_pcs') border-error-500 @enderror"
                                    placeholder="0.00">
                            </div>
                            @error('weight_per_pcs')
                                <p class="mt-1 text-sm text-error-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Status Aktif -->
                        <div class="pt-4 space-y-2">
                            <div class="flex items-center p-3 bg-neutral-50 rounded-lg">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                    class="h-5 w-5 text-primary-600 focus:ring-primary-500 border-neutral-300 rounded transition">
                                <label for="is_active" class="ml-3 block text-sm font-medium text-neutral-700">
                                    Aktifkan produk
                                </label>
                            </div>
                            <p class="text-xs text-neutral-500 ml-8">
                                <i class="fas fa-info-circle mr-1"></i> Produk yang aktif akan ditampilkan kepada
                                pembeli
                            </p>
                            @error('is_active')
                                <p class="mt-1 text-sm text-error-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-8 border-t border-neutral-100">
                        <a href="{{ route('seller.products.index') }}"
                            class="px-6 py-3 border border-neutral-300 rounded-lg text-sm font-semibold text-neutral-700 bg-white hover:bg-neutral-50 transition-all duration-200 shadow-sm hover:shadow-md text-center">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-3 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.plain-app>
