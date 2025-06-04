<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                @if($transaction->isPending() && $transaction->isTopup())
                     <button id="pay-button" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Bayar Sekarang
                    </button>
                @endif
                <a href="{{ route('seller.wallet.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    ← Kembali ke Dompet
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Detail Transaksi</h1>
            </div>
            <p class="text-gray-600">Informasi lengkap tentang transaksi ini</p>
        </div>

        <!-- Transaction Detail Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Status Header -->
            <div class="px-6 py-4 border-b border-gray-200 {{ $transaction->type->value === 'topup' ? 'bg-green-50' : 'bg-red-50' }}">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">
                            {{ $transaction->type_label }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            ID: {{ $transaction->reference_id ?? $transaction->id }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold {{ $transaction->type->value === 'topup' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->formatted_amount }}
                        </div>
                        <p class="text-sm text-gray-500">
                            {{ $transaction->status_label }}
                        </p>
                    </div>
                </div>
            </div>
            <!-- Transaction Info -->   
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Tanggal</h3>
                        <p class="text-gray-600">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    {{-- <div>
                        <h3 class="text-sm font-medium text-gray-700">Metode Pembayaran</h3>
                        <p class="text-gray-600">{{ $transaction->payment_method_label }}</p>
                    </div> --}}
                    {{-- @if($transaction->isWithdrawal())
                        <div>
                            <h3 class="text-sm font-medium text-gray-700">Bank Tujuan</h3>
                            <p class="text-gray-600">{{ $transaction->bank_name }} ({{ $transaction->bank_account }})</p>
                        </div>
                    @endif --}}
                </div>
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-700">Deskripsi</h3>
                    <p class="text-gray-600">{{ $transaction->description ?? 'Tidak ada deskripsi' }}</p>
                </div>  
            </div>
            <!-- Action Buttons -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('seller.wallet.index') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Kembali ke Dompet
                    </a>
                    @if($transaction->status !== 'settlement' && $transaction->status !== 'capture')
                        <a href="{{ route('seller.wallet.topup') }}" 
                           class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">
                            Top Up Lagi
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <!-- Important Info -->
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
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
    </div>
    <!-- Midtrans Snap Script -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        document.getElementById('pay-button').onclick = function() {
            const snapToken = '{{ $transaction->snap_token }}'.trim();
            
            
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