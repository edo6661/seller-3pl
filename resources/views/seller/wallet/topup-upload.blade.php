<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('seller.wallet.topup.payment', $transaction->reference_id) }}"
                    class="text-secondary-600 hover:text-secondary-800 mr-4 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <h1 class="text-2xl font-bold text-neutral-900">Upload Bukti Pembayaran</h1>
            </div>
            <p class="text-neutral-600">Upload bukti transfer untuk verifikasi pembayaran</p>
        </div>

        <!-- Transfer Details -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Detail Transfer</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bank Info -->
                <div>
                    <h4 class="text-sm font-medium text-neutral-700 mb-3">Transfer ke Rekening:</h4>
                    <div class="bg-neutral-50 rounded-lg p-4">
                        <div class="text-lg font-semibold text-neutral-900">{{ $transaction->bank_name }}</div>
                        <div class="text-sm text-neutral-600 mt-1">{{ $transaction->bank_account_number }}</div>
                        <div class="text-sm text-neutral-600">a.n. {{ $transaction->bank_account_name }}</div>
                    </div>
                </div>

                <!-- Amount Info -->
                <div>
                    <h4 class="text-sm font-medium text-neutral-700 mb-3">Jumlah Transfer:</h4>
                    <div class="bg-primary-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-primary-900">{{ $transaction->formatted_amount }}</div>
                        <div class="text-sm text-primary-600 mt-1">{{ $transaction->reference_id }}</div>
                    </div>
                </div>
            </div>
            <!-- QR Code -->
            @if($transaction->qr_code_url)
            <div class="mt-6 text-center">
                <h4 class="text-sm font-medium text-neutral-700 mb-3">Atau Scan QR Code:</h4>
                <div class="inline-block bg-white p-4 rounded-lg border-2 border-neutral-200">
                    <img src="{{ $transaction->qr_code_url_path }}" alt="QR Code" class="w-full mx-auto object-cover">
                </div>
            </div>
            @endif
        </div>

        <!-- Upload Form -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-neutral-900 mb-6">Upload Bukti Transfer</h3>
            
            <form action="{{ route('seller.wallet.topup.upload.submit', $transaction->reference_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label for="payment_proof" class="block text-sm font-medium text-neutral-700 mb-2">
                        Bukti Pembayaran <span class="text-error-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-neutral-300 border-dashed rounded-md hover:border-primary-400 transition">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-neutral-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-neutral-600">
                                <label for="payment_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                    <span>Upload file</span>
                                    <input id="payment_proof" name="payment_proof" type="file" class="sr-only" accept="image/*" required>
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-neutral-500">PNG, JPG, JPEG hingga 2MB</p>
                        </div>
                    </div>
                    @error('payment_proof')
                        <p class="text-sm text-error-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview -->
                <div id="image-preview" class="hidden mb-6">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Preview:</label>
                    <div class="bg-neutral-50 rounded-lg p-4">
                        <img id="preview-img" src="" alt="Preview" class="max-w-full max-h-64 mx-auto rounded">
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-700 transition">
                        <i class="fas fa-upload mr-2"></i> Upload Bukti Pembayaran
                    </button>
                    <a href="{{ route('seller.wallet.index') }}" class="px-6 py-3 border border-neutral-300 rounded-lg font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Important Info -->
        <div class="mt-6 bg-warning-50 border border-warning-200 rounded-xl p-5">
            <div class="flex">
                <div class="flex-shrink-0 pt-0.5">
                    <i class="fas fa-exclamation-triangle text-warning-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-base font-medium text-warning-800">Penting!</h3>
                    <div class="mt-2 text-sm text-warning-700">
                        <ul class="list-disc list-inside space-y-1.5">
                            <li>Pastikan melakukan transfer sesuai jumlah yang tertera</li>
                            <li>Upload bukti transfer yang jelas dan dapat dibaca</li>
                            <li>Verifikasi akan dilakukan dalam 1x24 jam</li>
                            <li>Jika ada masalah, silakan hubungi customer service</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // File preview
        document.getElementById('payment_proof').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('image-preview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-layouts.plain-app>