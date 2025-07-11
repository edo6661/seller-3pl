<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('seller.wallet.index') }}"
                    class="text-secondary-600 hover:text-secondary-800 mr-4 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <h1 class="text-2xl font-bold text-neutral-900">Top Up Saldo</h1>
            </div>
            <p class="text-neutral-600">Isi saldo dompet Anda dengan mudah dan aman</p>
        </div>

        <!-- Current Balance -->
        <div class="bg-primary-50 border border-primary-200 rounded-xl p-5 mb-6 shadow-sm">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-primary-800 flex items-center">
                    <i class="fas fa-wallet mr-2"></i> Saldo Saat Ini:
                </span>
                <span class="text-lg font-bold text-primary-900">{{ $wallet->formatted_balance }}</span>
            </div>
        </div>

        <!-- Top Up Form -->
        <form action="{{ route('seller.wallet.topup.submit') }}" method="POST"
            class="bg-white rounded-xl shadow-lg p-6">
            @csrf

            <!-- Amount Input -->
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-neutral-700 mb-2">
                    Jumlah Top Up <span class="text-error-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-neutral-500">Rp</span>
                    <input type="number" id="amount" name="amount"
                        class="w-full pl-12 pr-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                        placeholder="10000" min="10000" max="10000000" value="{{ old('amount') }}" required>
                </div>
                <p class="text-sm text-neutral-500 mt-1">Minimum Rp 10.000 - Maksimum Rp 10.000.000</p>
            </div>

            <!-- Quick Amount Buttons -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-neutral-700 mb-3">Pilih Cepat:</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <button type="button" onclick="setAmount(50000)"
                        class="quick-amount-btn bg-neutral-100 hover:bg-primary-100 border border-neutral-300 px-4 py-2.5 rounded-lg text-sm font-medium transition hover:border-primary-300">
                        Rp 50.000
                    </button>
                    <button type="button" onclick="setAmount(100000)"
                        class="quick-amount-btn bg-neutral-100 hover:bg-primary-100 border border-neutral-300 px-4 py-2.5 rounded-lg text-sm font-medium transition hover:border-primary-300">
                        Rp 100.000
                    </button>
                    <button type="button" onclick="setAmount(200000)"
                        class="quick-amount-btn bg-neutral-100 hover:bg-primary-100 border border-neutral-300 px-4 py-2.5 rounded-lg text-sm font-medium transition hover:border-primary-300">
                        Rp 200.000
                    </button>
                    <button type="button" onclick="setAmount(500000)"
                        class="quick-amount-btn bg-neutral-100 hover:bg-primary-100 border border-neutral-300 px-4 py-2.5 rounded-lg text-sm font-medium transition hover:border-primary-300">
                        Rp 500.000
                    </button>
                    <button type="button" onclick="setAmount(1000000)"
                        class="quick-amount-btn bg-neutral-100 hover:bg-primary-100 border border-neutral-300 px-4 py-2.5 rounded-lg text-sm font-medium transition hover:border-primary-300">
                        Rp 1.000.000
                    </button>
                    <button type="button" onclick="setAmount(2000000)"
                        class="quick-amount-btn bg-neutral-100 hover:bg-primary-100 border border-neutral-300 px-4 py-2.5 rounded-lg text-sm font-medium transition hover:border-primary-300">
                        Rp 2.000.000
                    </button>
                </div>
            </div>

            <!-- Payment Methods (Optional) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-neutral-700 mb-3">Metode Pembayaran (Opsional):</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label
                        class="flex items-center p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition cursor-pointer">
                        <input type="checkbox" name="payment_methods[]" value="credit_card"
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-neutral-300 rounded">
                        <span class="ml-3 text-sm text-neutral-700">Kartu Kredit</span>
                    </label>
                    <label
                        class="flex items-center p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition cursor-pointer">
                        <input type="checkbox" name="payment_methods[]" value="bank_transfer"
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-neutral-300 rounded">
                        <span class="ml-3 text-sm text-neutral-700">Transfer Bank</span>
                    </label>
                    <label
                        class="flex items-center p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition cursor-pointer">
                        <input type="checkbox" name="payment_methods[]" value="gopay"
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-neutral-300 rounded">
                        <span class="ml-3 text-sm text-neutral-700">GoPay</span>
                    </label>
                    <label
                        class="flex items-center p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition cursor-pointer">
                        <input type="checkbox" name="payment_methods[]" value="shopeepay"
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-neutral-300 rounded">
                        <span class="ml-3 text-sm text-neutral-700">ShopeePay</span>
                    </label>
                </div>
                <p class="text-sm text-neutral-500 mt-2">Kosongkan untuk menampilkan semua metode pembayaran</p>
            </div>

            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit"
                    class="flex-1 bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-700 transition shadow-md hover:shadow-lg focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 flex items-center justify-center">
                    <i class="fas fa-credit-card mr-2"></i> Lanjutkan Pembayaran
                </button>
                <a href="{{ route('seller.wallet.index') }}"
                    class="px-6 py-3 border border-neutral-300 rounded-lg font-semibold text-neutral-700 hover:bg-neutral-50 transition text-center">
                    Batal
                </a>
            </div>
        </form>

        <!-- Info Box -->
        <div class="mt-6 bg-warning-50 border border-warning-200 rounded-xl p-5">
            <div class="flex">
                <div class="flex-shrink-0 pt-0.5">
                    <i class="fas fa-info-circle text-warning-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-base font-medium text-warning-800">Informasi Penting</h3>
                    <div class="mt-2 text-sm text-warning-700">
                        <ul class="list-disc list-inside space-y-1.5">
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
                btn.classList.remove('bg-primary-100', 'border-primary-300', 'text-primary-800');
                btn.classList.add('bg-neutral-100', 'border-neutral-300');
            });

            event.target.classList.remove('bg-neutral-100', 'border-neutral-300');
            event.target.classList.add('bg-primary-100', 'border-primary-300', 'text-primary-800');
        }

        // Format number input
        document.getElementById('amount').addEventListener('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</x-layouts.plain-app>
