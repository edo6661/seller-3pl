<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Dompet Saya</h1>
            <p class="text-gray-600">Kelola saldo dan transaksi dompet Anda</p>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Wallet Balance Card -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold mb-2">Saldo Tersedia</h2>
                    <p class="text-3xl font-bold">{{ $wallet->formatted_balance }}</p>
                    @if($wallet->pending_balance > 0)
                        <p class="text-blue-200 text-sm mt-1">
                            Pending: Rp {{ number_format($wallet->pending_balance, 0, ',', '.') }}
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    <div class="space-y-2">
                        <a href="{{ route('seller.wallet.topup') }}" 
                           class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition inline-block">
                            Top Up
                        </a>
                        <br>
                        <a href="{{ route('seller.wallet.withdraw') }}" 
                           class="bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-800 transition inline-block">
                            Tarik Dana
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions History -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4">Riwayat Transaksi</h3>
            
            @if($transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Tanggal</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Jenis</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Deskripsi</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Jumlah</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Status</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($transactions as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $transaction->type->value === 'topup' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $transaction->type_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ Str::limit($transaction->description, 50) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-medium
                                        {{ $transaction->type->value === 'topup' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->formatted_amount }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->status_color }}">
                                            {{ $transaction->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('seller.wallet.transaction.detail', $transaction->id) }}" 
                                               class="text-blue-600 hover:text-blue-800">
                                                Detail
                                            </a>
                                            @if($transaction->canBeCancelled())
                                                <form action="{{ route('seller.wallet.transaction.cancel', $transaction->id) }}" 
                                                      method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800"
                                                            onclick="return confirm('Yakin ingin membatalkan transaksi ini?')">
                                                        Batal
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 text-4xl mb-4">💳</div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Belum ada transaksi</h4>
                    <p class="text-gray-600 mb-4">Mulai dengan melakukan top up saldo</p>
                    <a href="{{ route('seller.wallet.topup') }}" 
                       class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Top Up Sekarang
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.plain-app>