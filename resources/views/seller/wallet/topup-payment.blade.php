<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold text-neutral-900 mb-2">Pembayaran Top Up</h1>
            <p class="text-neutral-600">Selesaikan pembayaran untuk menambah saldo</p>
        </div>

        <!-- Payment Info -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="text-center mb-6">
                <div class="bg-primary-50 rounded-xl p-5 mb-6 border border-primary-200">
                    <h3 class="text-lg font-semibold text-neutral-900 mb-4 flex items-center justify-center">
                        <i class="fas fa-receipt mr-2 text-primary-500"></i> Detail Pembayaran
                    </h3>
                    <div class="space-y-3 text-left">
                        <div class="flex justify-between items-center py-2 border-b border-primary-100">
                            <span class="text-sm text-neutral-600">ID Transaksi:</span>
                            <span class="text-sm font-medium text-neutral-900">{{ $order_id }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-primary-100">
                            <span class="text-sm text-neutral-600">Jumlah:</span>
                            <span class="text-lg font-bold text-primary-600">Rp
                                {{ number_format($amount, 0, ',', '.') }}</span>
                        </div>
                        @if ($snap_token)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-neutral-600">Status:</span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-warning-100 text-warning-700">
                                    <i class="fas fa-clock mr-1"></i> Menunggu Pembayaran
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Button -->
                <button id="pay-button"
                    class="w-full bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-700 transition shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="fas fa-credit-card mr-2"></i> Bayar Sekarang
                </button>
            </div>
        </div>

        <!-- Info -->
        <div class="bg-warning-50 border border-warning-200 rounded-xl p-5">
            <div class="flex">
                <div class="flex-shrink-0 pt-0.5">
                    <i class="fas fa-exclamation-triangle text-warning-500 text-xl"></i>
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

        <!-- Cancel Button -->
        <div class="mt-6 text-center">
            <a href="{{ route('seller.wallet.index') }}"
                class="text-secondary-600 hover:text-secondary-800 transition flex items-center justify-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dompet
            </a>
        </div>
    </div>

    <!-- Midtrans Snap Script -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        document.getElementById('pay-button').onclick = function() {
            var snapToken = '{{ $snap_token }}'.trim();

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
        };
    </script>
</x-layouts.plain-app>
