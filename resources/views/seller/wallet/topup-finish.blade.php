<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <div class="text-center">
            @if($status === 'settlement' || $status === 'capture')
                <!-- Success -->
                <div class="mb-8">
                    <div class="w-24 h-24 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-green-600 mb-2">Pembayaran Berhasil!</h1>
                    <p class="text-gray-600 mb-6">Top up saldo Anda telah berhasil diproses</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Detail Transaksi</h3>
                    <div class="space-y-2 text-left">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID Transaksi:</span>
                            <span class="font-medium">{{ $order_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium text-green-600">Berhasil</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Waktu:</span>
                            <span class="font-medium">{{ now()->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>

            @elseif($status === 'pending')
                <!-- Pending -->
                <div class="mb-8">
                    <div class="w-24 h-24 bg-yellow-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-yellow-600 mb-2">Pembayaran Sedang Diproses</h1>
                    <p class="text-gray-600 mb-6">Silakan selesaikan pembayaran Anda</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Detail Transaksi</h3>
                    <div class="space-y-2 text-left">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID Transaksi:</span>
                            <span class="font-medium">{{ $order_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium text-yellow-600">Pending</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Waktu:</span>
                            <span class="font-medium">{{ now()->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Pembayaran Anda sedang diproses. Saldo akan otomatis masuk setelah pembayaran dikonfirmasi.</p>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- Failed -->
                <div class="mb-8">
                    <div class="w-24 h-24 bg-red-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-red-600 mb-2">Pembayaran Gagal</h1>
                    <p class="text-gray-600 mb-6">Terjadi masalah dalam proses pembayaran</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Detail Transaksi</h3>
                    <div class="space-y-2 text-left">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID Transaksi:</span>
                            <span class="font-medium">{{ $order_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium text-red-600">Gagal</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Waktu:</span>
                            <span class="font-medium">{{ now()->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Gagal</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>Pembayaran tidak dapat diproses. Silakan coba lagi atau hubungi customer service.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ route('seller.wallet.index') }}" 
                   class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition inline-block">
                    Kembali ke Dompet
                </a>
                
                @if($status !== 'settlement' && $status !== 'capture')
                    <a href="{{ route('seller.wallet.topup') }}" 
                       class="w-full bg-gray-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-gray-700 transition inline-block">
                        Top Up Lagi
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-layouts.plain-app>