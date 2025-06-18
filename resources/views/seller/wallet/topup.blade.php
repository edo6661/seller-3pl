<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('seller.wallet.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    ← Kembali
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Top Up Saldo</h1>
            </div>
            <p class="text-gray-600">Isi saldo dompet Anda dengan mudah dan aman</p>
        </div>

        <!-- Current Balance -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-blue-800">Saldo Saat Ini:</span>
                <span class="text-lg font-bold text-blue-900">{{ $wallet->formatted_balance }}</span>
            </div>
        </div>

      
        <!-- Top Up Form -->
        <form action="{{ route('seller.wallet.topup.submit') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
            @csrf
            
            <!-- Amount Input -->
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Jumlah Top Up <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                    <input type="number" 
                           id="amount" 
                           name="amount" 
                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="10000"
                           min="10000"
                           max="10000000"
                           value="{{ old('amount') }}"
                           required>
                </div>
                <p class="text-sm text-gray-500 mt-1">Minimum Rp 10.000 - Maksimum Rp 10.000.000</p>
            </div>

            <!-- Quick Amount Buttons -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Cepat:</label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" onclick="setAmount(50000)" 
                            class="quick-amount-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Rp 50.000
                    </button>
                    <button type="button" onclick="setAmount(100000)" 
                            class="quick-amount-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Rp 100.000
                    </button>
                    <button type="button" onclick="setAmount(200000)" 
                            class="quick-amount-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Rp 200.000
                    </button>
                    <button type="button" onclick="setAmount(500000)" 
                            class="quick-amount-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Rp 500.000
                    </button>
                    <button type="button" onclick="setAmount(1000000)" 
                            class="quick-amount-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Rp 1.000.000
                    </button>
                    <button type="button" onclick="setAmount(2000000)" 
                            class="quick-amount-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Rp 2.000.000
                    </button>
                </div>
            </div>

            <!-- Payment Methods (Optional) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Metode Pembayaran (Opsional):</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="payment_methods[]" value="credit_card" class="mr-2">
                        <span class="text-sm">Kartu Kredit</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="payment_methods[]" value="bank_transfer" class="mr-2">
                        <span class="text-sm">Transfer Bank</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="payment_methods[]" value="gopay" class="mr-2">
                        <span class="text-sm">GoPay</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="payment_methods[]" value="shopeepay" class="mr-2">
                        <span class="text-sm">ShopeePay</span>
                    </label>
                </div>
                <p class="text-sm text-gray-500 mt-1">Kosongkan untuk menampilkan semua metode pembayaran</p>
            </div>

            <!-- Submit Button -->
            <div class="flex space-x-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Lanjutkan Pembayaran
                </button>
                <a href="{{ route('seller.wallet.index') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>

        <!-- Info Box -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Informasi Penting</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Proses top up biasanya selesai dalam 5-10 menit</li>
                            <li>Saldo akan otomatis masuk setelah pembayaran berhasil</li>
                            <li>Pastikan melengkapi pembayaran dalam batas waktu yang ditentukan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setAmount(amount) {
            document.getElementById('amount').value = amount;
            
            // Update button styles
            document.querySelectorAll('.quick-amount-btn').forEach(btn => {
                btn.classList.remove('bg-blue-100', 'border-blue-300');
                btn.classList.add('bg-gray-100', 'border-gray-300');
            });
            
            event.target.classList.remove('bg-gray-100', 'border-gray-300');
            event.target.classList.add('bg-blue-100', 'border-blue-300');
        }

        // Format number input
        document.getElementById('amount').addEventListener('input', function(e) {
            // Remove any non-digit characters except for the decimal point
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</x-layouts.plain-app>