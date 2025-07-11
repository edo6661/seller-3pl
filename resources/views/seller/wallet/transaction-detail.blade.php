<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8 ">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <a href="{{ route('seller.wallet.index') }}"
                        class="text-secondary-600 hover:text-secondary-800 mr-4 transition flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dompet
                    </a>
                    <h1 class="text-2xl font-bold text-neutral-900">Detail Transaksi</h1>
                </div>
                @if ($transaction->isPending() && $transaction->isTopup())
                    <button id="pay-button"
                        class="bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-700 transition shadow-md hover:shadow-lg flex items-center">
                        <i class="fas fa-credit-card mr-2"></i> Bayar Sekarang
                    </button>
                @endif
            </div>
            <p class="text-neutral-600">Informasi lengkap tentang transaksi ini</p>
        </div>

        <!-- Transaction Detail Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Status Header -->
            <div
                class="px-6 py-4 border-b border-neutral-200 {{ $transaction->type->value === 'topup' ? 'bg-success-50' : 'bg-error-50' }}">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-xl font-semibold text-neutral-900">
                            {{ $transaction->type_label }}
                        </h2>
                        <p class="text-sm text-neutral-500 mt-1">
                            <i class="fas fa-hashtag mr-1"></i> {{ $transaction->reference_id ?? $transaction->id }}
                        </p>
                    </div>
                    <div class="text-left md:text-right">
                        <div
                            class="text-2xl font-bold {{ $transaction->type->value === 'topup' ? 'text-success-600' : 'text-error-600' }}">
                            {{ $transaction->formatted_amount }}
                        </div>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mt-1
                            {{ $transaction->status === 'pending'
                                ? 'bg-warning-100 text-warning-700'
                                : ($transaction->status === 'success' || $transaction->status === 'settlement' || $transaction->status === 'capture'
                                    ? 'bg-success-100 text-success-700'
                                    : 'bg-error-100 text-error-700') }}">
                            <i
                                class="fas {{ $transaction->status === 'pending'
                                    ? 'fa-clock mr-1'
                                    : ($transaction->status === 'success' || $transaction->status === 'settlement' || $transaction->status === 'capture'
                                        ? 'fa-check-circle mr-1'
                                        : 'fa-times-circle mr-1') }}"></i>
                            {{ $transaction->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Transaction Info -->
            <div class="px-6 py-4 divide-y divide-neutral-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-4">
                    <div>
                        <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                            <i class="fas fa-calendar-day mr-2 text-neutral-400"></i> Tanggal
                        </h3>
                        <p class="text-neutral-600">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    @if ($transaction->payment_method_label)
                        <div>
                            <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                                <i class="fas fa-credit-card mr-2 text-neutral-400"></i> Metode Pembayaran
                            </h3>
                            <p class="text-neutral-600">{{ $transaction->payment_method_label }}</p>
                        </div>
                    @endif
                    @if ($transaction->isWithdrawal() && $transaction->bank_name)
                        <div>
                            <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                                <i class="fas fa-university mr-2 text-neutral-400"></i> Bank Tujuan
                            </h3>
                            <p class="text-neutral-600">{{ $transaction->bank_name }}
                                ({{ $transaction->bank_account }})</p>
                        </div>
                    @endif
                </div>

                <div class="pt-4">
                    <h3 class="text-sm font-medium text-neutral-700 mb-1 flex items-center">
                        <i class="fas fa-align-left mr-2 text-neutral-400"></i> Deskripsi
                    </h3>
                    <p class="text-neutral-600">{{ $transaction->description ?? 'Tidak ada deskripsi' }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 border-t border-neutral-200 bg-neutral-50">
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('seller.wallet.index') }}"
                        class="bg-secondary-600 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-secondary-700 transition shadow-sm hover:shadow-md flex items-center justify-center">
                        <i class="fas fa-wallet mr-2"></i> Kembali ke Dompet
                    </a>
                    @if ($transaction->status !== 'settlement' && $transaction->status !== 'capture')
                        <a href="{{ route('seller.wallet.topup') }}"
                            class="bg-white text-primary-600 border border-primary-200 px-5 py-2.5 rounded-lg font-semibold hover:bg-primary-50 transition shadow-sm hover:shadow-md flex items-center justify-center">
                            <i class="fas fa-plus-circle mr-2"></i> Top Up Lagi
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Important Info -->
        <div class="mt-8 bg-warning-50 border border-warning-200 rounded-xl p-5">
            <div class="flex">
                <div class="flex-shrink-0 pt-0.5">
                    <i class="fas fa-exclamation-circle text-warning-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-base font-medium text-warning-800">Penting!</h3>
                    <div class="mt-2 text-sm text-warning-700">
                        <ul class="list-disc list-inside space-y-1.5">
                            <li>Selesaikan pembayaran dalam waktu yang ditentukan</li>
                            <li>Saldo akan otomatis masuk setelah pembayaran berhasil</li>
                            <li>Jika mengalami masalah, hubungi customer service</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Midtrans Snap Script -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        document.getElementById('pay-button')?.addEventListener('click', function() {
            const snapToken = '{{ $transaction->snap_token }}'.trim();

            if (!snapToken) {
                alert('Error: Snap Token kosong! Pembayaran tidak bisa dilanjutkan.');
                return;
            }

            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    window.location.href =
                        '{{ route('seller.wallet.topup.finish') }}?order_id={{ $order_id }}&transaction_status=settlement';
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    window.location.href =
                        '{{ route('seller.wallet.topup.finish') }}?order_id={{ $order_id }}&transaction_status=pending';
                },
                onError: function(result) {
                    console.log('Payment error:', result);
                    alert('Pembayaran gagal! Silakan coba lagi.');
                },
                onClose: function() {
                    console.log('Customer closed the popup without finishing the payment');
                    alert('Pembayaran dibatalkan. Silakan coba lagi.');
                }
            });
        });
    </script>
</x-layouts.plain-app>
