<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('seller.wallet.index') }}"
                    class="text-secondary-600 hover:text-secondary-800 mr-4 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dompet
                </a>
                <h1 class="text-2xl font-bold text-neutral-900">Detail Permintaan Top Up</h1>
            </div>
        </div>

        <!-- Top Up Request Details -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Status Header -->
            <div class="px-6 py-4 border-b border-neutral-200 bg-primary-50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-xl font-semibold text-neutral-900">
                            Top Up Manual
                        </h2>
                        <p class="text-sm text-neutral-500 mt-1">
                            <i class="fas fa-hashtag mr-1"></i> {{ $topUpRequest->request_code }}
                        </p>
                    </div>
                    <div class="text-left md:text-right">
                        <div class="text-2xl font-bold text-primary-600">
                            {{ $topUpRequest->formatted_amount }}
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mt-1
                            {{ $topUpRequest->status->color() === 'success' ? 'bg-success-100 text-success-700' :
                               ($topUpRequest->status->color() === 'warning' ? 'bg-warning-100 text-warning-700' :
                               ($topUpRequest->status->color() === 'danger' ? 'bg-error-100 text-error-700' : 'bg-info-100 text-info-700')) }}">
                            {{ $topUpRequest->status->label() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Request Info -->
            <div class="px-6 py-4 divide-y divide-neutral-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-4">
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-calendar-day mr-2 text-neutral-400"></i> Tanggal Permintaan
                        </h3>
                        <p class="text-neutral-600">{{ $topUpRequest->requested_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    
                    @if($topUpRequest->bank_name)
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-university mr-2 text-neutral-400"></i> Bank Tujuan
                        </h3>
                        <p class="text-neutral-600">{{ $topUpRequest->bank_name }}</p>
                        <p class="text-sm text-neutral-500">{{ $topUpRequest->bank_account_number }}</p>
                        <p class="text-sm text-neutral-500">a.n. {{ $topUpRequest->bank_account_name }}</p>
                    </div>
                    @endif
                </div>

                @if($topUpRequest->payment_proof_path)
                <div class="pt-4">
                    <h3 class="text-sm font-medium text-neutral-700 mb-3 flex items-center">
                        <i class="fas fa-receipt mr-2 text-neutral-400"></i> Bukti Pembayaran
                    </h3>
                    <div class="bg-neutral-50 rounded-lg p-4">
                        <img src="{{ $topUpRequest->payment_proof_url }}" alt="Bukti Pembayaran" 
                             class="max-w-full max-h-96 mx-auto rounded shadow-md">
                    </div>
                </div>
                @endif

                @if($topUpRequest->admin_notes)
                <div class="pt-4">
                    <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                        <i class="fas fa-comment mr-2 text-neutral-400"></i> Catatan Admin
                    </h3>
                    <p class="text-neutral-600">{{ $topUpRequest->admin_notes }}</p>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 border-t border-neutral-200 bg-neutral-50">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
                    <div class="flex space-x-3">
                        @if(in_array($topUpRequest->status->value, ['pending', 'waiting_payment']))
                            <form action="{{ route('seller.wallet.manual-topup.cancel', $topUpRequest->request_code) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin membatalkan permintaan ini?')">
                                @csrf
                                <button type="submit"
                                    class="bg-error-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-error-700 transition">
                                    <i class="fas fa-ban mr-2"></i> Batalkan Permintaan
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('seller.wallet.index') }}"
                            class="bg-secondary-600 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-secondary-700 transition">
                            <i class="fas fa-wallet mr-2"></i> Kembali ke Dompet
                        </a>
                        <a href="{{ route('seller.wallet.manual-topup') }}"
                            class="bg-white text-primary-600 border border-primary-200 px-5 py-2.5 rounded-lg font-semibold hover:bg-primary-50 transition">
                            <i class="fas fa-plus-circle mr-2"></i> Top Up Lagi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.plain-app>