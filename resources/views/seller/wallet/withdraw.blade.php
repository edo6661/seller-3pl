<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('seller.wallet.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    ← Kembali
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Tarik Dana</h1>
            </div>
            <p class="text-gray-600">Tarik saldo dari dompet Anda ke rekening bank</p>
        </div>

        <!-- Current Balance -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-blue-800">Saldo Tersedia:</span>
                <span class="text-lg font-bold text-blue-900">{{ $wallet->formatted_balance }}</span>
            </div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Withdraw Form -->
        <form action="{{ route('seller.wallet.withdraw.submit') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
            @csrf
            
            <!-- Amount Input -->
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Jumlah Penarikan <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                    <input type="number" 
                           id="amount" 
                           name="amount" 
                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="50000"
                           min="50000"
                           max="{{ $wallet->balance }}"
                           value="{{ old('amount') }}"
                           required>
                </div>
                <p class="text-sm text-gray-500 mt-1">Minimum Rp 50.000 - Maksimum Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
            </div>

            <!-- Quick Amount Buttons -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Cepat:</label>
                <div class="grid grid-cols-2 gap-3">
                    @php
                        $quickAmounts = [];
                        if ($wallet->balance >= 100000) $quickAmounts[] = 100000;
                        if ($wallet->balance >= 250000) $quickAmounts[] = 250000;
                        if ($wallet->balance >= 500000) $quickAmounts[] = 500000;
                        if ($wallet->balance >= 1000000) $quickAmounts[] = 1000000;
                        if ($wallet->balance >= 2000000) $quickAmounts[] = 2000000;
                        if ($wallet->balance >= 5000000) $quickAmounts[] = 5000000;
                    @endphp
                    
                    @foreach($quickAmounts as $amount)
                        <button type="button" onclick="setAmount({{ $amount }})" 
                                class="quick-amount-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                            Rp {{ number_format($amount, 0, ',', '.') }}
                        </button>
                    @endforeach
                    
                    @if($wallet->balance >= 50000)
                        <button type="button" onclick="setAmount({{ $wallet->balance }})" 
                                class="quick-amount-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">
                            Semua Saldo
                        </button>
                    @endif
                </div>
            </div>

            <!-- Bank Details -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Rekening Bank</h3>
                
                <!-- Bank Name -->
                <div class="mb-4">
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Bank <span class="text-red-500">*</span>
                    </label>
                    <select id="bank_name" 
                            name="bank_name" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                        <option value="BII" {{ old('bank_name') == 'BII' ? 'selected' : '' }}>BII</option>
                        <option value="Bank Syariah Indonesia" {{ old('bank_name') == 'Bank Syariah Indonesia' ? 'selected' : '' }}>Bank Syariah Indonesia</option>
                        <option value="Lainnya" {{ old('bank_name') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Account Name -->
                <div class="mb-4">
                    <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Pemilik Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="account_name" 
                           name="account_name" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nama sesuai dengan rekening bank"
                           value="{{ old('account_name') }}"
                           required>
                    <p class="text-sm text-gray-500 mt-1">Masukkan nama sesuai dengan yang tertera di rekening bank</p>
                </div>

                <!-- Account Number -->
                <div class="mb-6">
                    <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="account_number" 
                           name="account_number" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nomor rekening"
                           value="{{ old('account_number') }}"
                           required>
                    <p class="text-sm text-gray-500 mt-1">Masukkan nomor rekening tanpa spasi atau tanda baca</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex space-x-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Ajukan Penarikan
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
                            <li>Penarikan akan diproses dalam 1-3 hari kerja</li>
                            <li>Pastikan data rekening yang dimasukkan benar</li>
                            <li>Minimum penarikan adalah Rp 50.000</li>
                            <li>Tidak ada biaya administrasi untuk penarikan</li>
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
            // Remove any non-digit characters
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Format account number input
        document.getElementById('account_number').addEventListener('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</x-layouts.plain-app>