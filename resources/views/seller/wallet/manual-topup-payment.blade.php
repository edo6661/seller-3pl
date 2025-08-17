<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('seller.wallet.manual-topup') }}"
                    class="text-secondary-600 hover:text-secondary-800 mr-4 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <h1 class="text-2xl font-bold text-neutral-900">Pilih Metode Pembayaran</h1>
            </div>
            <p class="text-neutral-600">Pilih rekening bank untuk melakukan transfer</p>
        </div>

        <!-- Top Up Info -->
        <div class="bg-primary-50 border border-primary-200 rounded-xl p-5 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center md:text-left">
                    <span class="text-sm font-medium text-primary-800">Kode Permintaan</span>
                    <div class="text-lg font-bold text-primary-900">{{ $topUpRequest->request_code }}</div>
                </div>
                <div class="text-center">
                    <span class="text-sm font-medium text-primary-800">Jumlah</span>
                    <div class="text-xl font-bold text-primary-900">{{ $topUpRequest->formatted_amount }}</div>
                </div>
                <div class="text-center md:text-right">
                    <span class="text-sm font-medium text-primary-800">Status</span>
                    <div class="text-sm font-semibold text-primary-700">{{ $topUpRequest->status->label() }}</div>
                </div>
            </div>
        </div>

        <!-- Bank Selection -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-neutral-900 mb-6">Pilih Rekening Bank Tujuan</h3>
            
            @if($bankAccounts->count() > 0)
                <form action="{{ route('seller.wallet.manual-topup.set-bank', $topUpRequest->request_code) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($bankAccounts as $bank)
                        <label class="flex items-center p-4 border-2 border-neutral-200 rounded-lg hover:border-primary-300 cursor-pointer transition">
                            <input type="radio" name="bank_account_id" value="{{ $bank->id }}" class="h-4 w-4 text-primary-600 focus:ring-primary-500" required>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="text-lg font-semibold text-neutral-900">{{ $bank->bank_name }}</div>
                                        <div class="text-sm text-neutral-600">{{ $bank->account_number }}</div>
                                        <div class="text-sm text-neutral-600">a.n. {{ $bank->account_name }}</div>
                                    </div>
                                    @if($bank->qr_code_url)
                                    <div class="ml-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-success-100 text-success-700">
                                            <i class="fas fa-qrcode mr-1"></i> QR Available
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="flex-1 bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-700 transition">
                            <i class="fas fa-arrow-right mr-2"></i> Lanjutkan
                        </button>
                        <a href="{{ route('seller.wallet.manual-topup') }}" class="px-6 py-3 border border-neutral-300 rounded-lg font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                            Batal
                        </a>
                    </div>
                </form>
            @else
                <div class="text-center py-12">
                    <div class="text-neutral-300 text-5xl mb-4">
                        <i class="fas fa-university"></i>
                    </div>
                    <h4 class="text-lg font-medium text-neutral-900 mb-2">Tidak ada rekening tersedia</h4>
                    <p class="text-neutral-600">Silakan hubungi administrator</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.plain-app>