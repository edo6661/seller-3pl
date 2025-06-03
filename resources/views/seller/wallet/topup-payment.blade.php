<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Pembayaran Top Up</h1>
            <p class="text-gray-600">Selesaikan pembayaran untuk menambah saldo</p>
        </div>

        <!-- Payment Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="text-center mb-6">
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Detail Pembayaran</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID Transaksi:</span>
                            <span class="font-medium">{{ $order_id }}</span>
                            <span class="text-gray-600">Snap Token:</span>
                            <span class="font-medium">{{ $snap_token }}</span>
                            <span class="text-gray-600">Snap Url:</span>
                            <span class="font-medium">{{ $snap_url }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah:</span>
                            <span class="font-bold text-blue-600">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Button -->
                <button id="pay-button" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Bayar Sekarang
                </button>
            </div>
        </div>

        <!-- Info -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Penting!</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
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
            <a href="{{ route('seller.wallet.index') }}" class="text-gray-600 hover:text-gray-800">
                ← Kembali ke Dompet
            </a>
        </div>
    </div>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        document.getElementById('pay-button').onclick = function() {
            var snapToken = '{{ $snap_token }}'.trim();
            
            
            if (!snapToken) {
                alert('Error: Snap Token kosong! Pembayaran tidak bisa dilanjutkan.');
                return;
            }
            
            snap.pay(snapToken, {
                onSuccess: function(result){
                    console.log('Payment success:', result);
                    window.location.href = '{{ route("seller.wallet.topup.finish") }}?order_id={{ $order_id }}&transaction_status=settlement';
                },
                onPending: function(result){
                    console.log('Payment pending:', result);
                    window.location.href = '{{ route("seller.wallet.topup.finish") }}?order_id={{ $order_id }}&transaction_status=pending';
                },
                onError: function(result){
                    console.log('Payment error:', result);
                    alert('Pembayaran gagal! Silakan coba lagi.');
                },
                onClose: function(){
                    console.log('Customer closed the popup without finishing the payment');
                    alert('Pembayaran dibatalkan. Silakan coba lagi.');
            }});
        };
    </script>
</x-layouts.plain-app>