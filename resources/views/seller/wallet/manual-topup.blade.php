<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('seller.wallet.index') }}"
                    class="text-secondary-600 hover:text-secondary-800 mr-4 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <h1 class="text-2xl font-bold text-neutral-900">Top Up Manual</h1>
            </div>
            <p class="text-neutral-600">Isi saldo dompet Anda dengan transfer bank manual</p>
        </div>

        <!-- Alert Messages -->
        @if(session('info'))
        <div class="bg-info-50 border border-info-200 rounded-xl p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-info-500 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-info-700">{{ session('info') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Current Balance -->
        <div class="bg-primary-50 border border-primary-200 rounded-xl p-5 mb-6 shadow-sm">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-primary-800 flex items-center">
                    <i class="fas fa-wallet mr-2"></i> Saldo Saat Ini:
                </span>
                <span class="text-lg font-bold text-primary-900">{{ $wallet->formatted_balance }}</span>
            </div>
        </div>
        <!-- Ganti bagian resumable request menjadi seperti ini -->
        @if($resumableRequests->count() > 0)
            <div class="bg-warning-50 border border-warning-200 rounded-xl p-5 mb-6 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 pt-0.5">
                        <i class="fas fa-exclamation-triangle text-warning-500 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-medium text-warning-800">
                            Permintaan Belum Selesai ({{ $resumableRequests->count() }})
                        </h3>
                        <div class="mt-2 text-sm text-warning-700">
                            <p>Anda memiliki permintaan top up yang belum diselesaikan:</p>
                            <div class="mt-3 space-y-3">
                                @foreach($resumableRequests as $request)
                                <div class="bg-warning-100 rounded-lg p-3">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium text-warning-900">{{ $request->formatted_amount }}</div>
                                            <div class="text-xs text-warning-700">
                                                {{ $request->request_code }} • {{ $request->created_at->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="text-xs text-warning-600 mt-1">
                                                Status: 
                                                <span class="font-medium">
                                                    @if($request->status === App\Enums\ManualTopUpStatus::PENDING)
                                                        Menunggu pilih bank
                                                    @elseif($request->status === App\Enums\ManualTopUpStatus::WAITING_PAYMENT)
                                                        Menunggu upload bukti
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-right space-x-2">
                                            <a href="{{ route('seller.wallet.manual-topup.resume', $request->request_code) }}" 
                                            class="bg-warning-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-warning-700 transition inline-flex items-center">
                                                <i class="fas fa-play mr-1"></i> Lanjutkan
                                            </a>
                                            <form action="{{ route('seller.wallet.manual-topup.cancel', $request->request_code) }}" 
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Yakin ingin membatalkan permintaan {{ $request->request_code }}?')"
                                                        class="bg-gray-500 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-gray-600 transition inline-flex items-center">
                                                    <i class="fas fa-times mr-1"></i> Batal
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Recent Top Up Requests -->
        @if($topUpRequests->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Permintaan Top Up Terbaru</h3>
            <div class="space-y-3">
                @foreach($topUpRequests as $request)
                <div class="flex justify-between items-center p-3 bg-neutral-50 rounded-lg">
                    <div>
                        <div class="font-medium text-neutral-900">{{ $request->formatted_amount }}</div>
                        <div class="text-sm text-neutral-500">{{ $request->request_code }} • {{ $request->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $request->status->color() === 'success' ? 'bg-success-100 text-success-700' :
                               ($request->status->color() === 'warning' ? 'bg-warning-100 text-warning-700' :
                               ($request->status->color() === 'danger' ? 'bg-error-100 text-error-700' : 'bg-info-100 text-info-700')) }}">
                            {{ $request->status->label() }}
                        </span>
                        <div class="mt-1 flex space-x-2">
                            <a href="{{ route('seller.wallet.manual-topup.detail', $request->request_code) }}" 
                               class="text-xs text-secondary-600 hover:text-secondary-800">Detail</a>
                            
                            {{-- Tombol lanjutkan jika status masih bisa dilanjutkan --}}
                            @if(in_array($request->status->value, ['pending', 'waiting_payment']))
                                @if($request->status->value === 'pending')
                                    <a href="{{ route('seller.wallet.manual-topup.payment', $request->request_code) }}" 
                                       class="text-xs text-primary-600 hover:text-primary-800 font-medium">Lanjutkan</a>
                                @elseif($request->status->value === 'waiting_payment')
                                    <a href="{{ route('seller.wallet.manual-topup.upload', $request->request_code) }}" 
                                       class="text-xs text-primary-600 hover:text-primary-800 font-medium">Upload Bukti</a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Top Up Form -->
        <form action="{{ route('seller.wallet.manual-topup.submit') }}" method="POST"
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
                @error('amount')
                    <p class="text-sm text-error-600 mt-1">{{ $message }}</p>
                @enderror
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

            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit"
                    class="flex-1 bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-700 transition shadow-md hover:shadow-lg focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 flex items-center justify-center">
                    <i class="fas fa-university mr-2"></i> Buat Permintaan Top Up
                </button>
                <a href="{{ route('seller.wallet.index') }}"
                    class="px-6 py-3 border border-neutral-300 rounded-lg font-semibold text-neutral-700 hover:bg-neutral-50 transition text-center">
                    Batal
                </a>
            </div>
        </form>

        <!-- Info Box -->
        <div class="mt-6 bg-info-50 border border-info-200 rounded-xl p-5">
            <div class="flex">
                <div class="flex-shrink-0 pt-0.5">
                    <i class="fas fa-info-circle text-info-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-base font-medium text-info-800">Cara Top Up Manual</h3>
                    <div class="mt-2 text-sm text-info-700">
                        <ol class="list-decimal list-inside space-y-1.5">
                            <li>Klik tombol "Buat Permintaan Top Up" di atas</li>
                            <li>Pilih rekening bank tujuan transfer</li>
                            <li>Lakukan transfer sesuai jumlah yang diminta</li>
                            <li>Upload bukti transfer untuk verifikasi</li>
                            <li>Admin akan memverifikasi dalam 1x24 jam</li>
                            <li>Saldo akan otomatis masuk setelah verifikasi</li>
                        </ol>
                        <div class="mt-3 p-3 bg-info-100 rounded-lg">
                            <p class="font-medium">💡 Tips:</p>
                            <p>Jika Anda sudah membuat permintaan tapi belum selesai, sistem akan otomatis melanjutkan proses yang tertunda saat Anda kembali ke halaman ini.</p>
                        </div>
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
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</x-layouts.plain-app>