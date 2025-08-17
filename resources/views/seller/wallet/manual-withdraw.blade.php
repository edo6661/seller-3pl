<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('seller.wallet.index') }}"
                    class="text-secondary-600 hover:text-secondary-800 mr-4 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <h1 class="text-2xl font-bold text-neutral-900">Tarik Dana Manual</h1>
            </div>
            <p class="text-neutral-600">Tarik dana dari dompet Anda ke rekening bank</p>
        </div>

        <!-- Current Balance -->
        <div class="bg-primary-50 border border-primary-200 rounded-xl p-5 mb-6 shadow-sm">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-primary-800 flex items-center">
                    <i class="fas fa-wallet mr-2"></i> Saldo Tersedia:
                </span>
                <span class="text-lg font-bold text-primary-900">{{ $wallet->formatted_balance }}</span>
            </div>
        </div>

        <!-- Recent Withdraw Requests -->
        @if($withdrawRequests->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Permintaan Penarikan Terbaru</h3>
            <div class="space-y-3">
                @foreach($withdrawRequests as $request)
                <div class="flex justify-between items-center p-3 bg-neutral-50 rounded-lg">
                    <div>
                        <div class="font-medium text-neutral-900">{{ $request->formatted_amount }}</div>
                        <div class="text-sm text-neutral-500">{{ $request->withdrawal_code }} • {{ $request->created_at->format('d/m/Y H:i') }}</div>
                        <div class="text-xs text-neutral-400">{{ $request->bank_name }} - {{ $request->account_number }}</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $request->status === 'completed' ? 'bg-success-100 text-success-700' :
                               ($request->status === 'processing' ? 'bg-warning-100 text-warning-700' :
                               ($request->status === 'failed' || $request->status === 'cancelled' ? 'bg-error-100 text-error-700' : 'bg-info-100 text-info-700')) }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Withdraw Form -->
        <form action="{{ route('seller.wallet.manual-withdraw.submit') }}" method="POST"
            class="bg-white rounded-xl shadow-lg p-6">
            @csrf

            <!-- Amount Input -->
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-neutral-700 mb-2">
                    Jumlah Penarikan <span class="text-error-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-neutral-500">Rp</span>
                    <input type="number" id="amount" name="amount"
                        class="w-full pl-12 pr-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                        placeholder="50000" min="50000" value="{{ old('amount') }}" required>
                </div>
                <p class="text-sm text-neutral-500 mt-1">Minimum Rp 50.000</p>
                @error('amount')
                    <p class="text-sm text-error-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bank Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-neutral-700 mb-2">
                        Nama Bank <span class="text-error-500">*</span>
                    </label>
                    <select id="bank_name" name="bank_name"
                        class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                        required>
                        <option value="">Pilih Bank</option>
                        <option value="BCA" {{ old('bank_name') == 'BCA' ? 'selected' : '' }}>BCA</option>
                        <option value="BNI" {{ old('bank_name') == 'BNI' ? 'selected' : '' }}>BNI</option>
                        <option value="BRI" {{ old('bank_name') == 'BRI' ? 'selected' : '' }}>BRI</option>
                        <option value="Mandiri" {{ old('bank_name') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="CIMB Niaga" {{ old('bank_name') == 'CIMB Niaga' ? 'selected' : '' }}>CIMB Niaga</option>
                        <option value="Danamon" {{ old('bank_name') == 'Danamon' ? 'selected' : '' }}>Danamon</option>
                        <option value="Permata" {{ old('bank_name') == 'Permata' ? 'selected' : '' }}>Permata</option>
                        <option value="BTN" {{ old('bank_name') == 'BTN' ? 'selected' : '' }}>BTN</option>
                    </select>
                    @error('bank_name')
                        <p class="text-sm text-error-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="account_number" class="block text-sm font-medium text-neutral-700 mb-2">
                        Nomor Rekening <span class="text-error-500">*</span>
                    </label>
                    <input type="text" id="account_number" name="account_number"
                        class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                        placeholder="1234567890" value="{{ old('account_number') }}" required>
                    @error('account_number')
                        <p class="text-sm text-error-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="account_name" class="block text-sm font-medium text-neutral-700 mb-2">
                    Nama Pemilik Rekening <span class="text-error-500">*</span>
                </label>
                <input type="text" id="account_name" name="account_name"
                    class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                    placeholder="Nama sesuai rekening bank" value="{{ old('account_name') }}" required>
                @error('account_name')
                    <p class="text-sm text-error-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Admin Fee Info -->
            <div class="bg-info-50 border border-info-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0 pt-0.5">
                        <i class="fas fa-info-circle text-info-500"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-info-800">Biaya Admin</h4>
                        <div class="mt-1 text-sm text-info-700">
                            <p>• Penarikan < Rp 1.000.000: Rp 2.500</p>
                            <p>• Penarikan ≥ Rp 1.000.000: Rp 5.000</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit"
                    class="flex-1 bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-700 transition shadow-md hover:shadow-lg focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 flex items-center justify-center">
                    <i class="fas fa-money-bill-transfer mr-2"></i> Buat Permintaan Penarikan
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
                    <i class="fas fa-exclamation-triangle text-warning-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-base font-medium text-warning-800">Ketentuan Penarikan</h3>
                    <div class="mt-2 text-sm text-warning-700">
                        <ul class="list-disc list-inside space-y-1.5">
                            <li>Minimum penarikan Rp 50.000</li>
                            <li>Saldo akan langsung dipotong saat permintaan dibuat</li>
                            <li>Proses pencairan 1-3 hari kerja</li>
                            <li>Pastikan data rekening benar dan aktif</li>
                            <li>Jika ditolak, saldo akan dikembalikan otomatis</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Format number input
        document.getElementById('amount').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Format account number input
        document.getElementById('account_number').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</x-layouts.plain-app>