<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900 mb-2">Dompet Saya</h1>
            <p class="text-neutral-600">Kelola saldo dan transaksi dompet Anda</p>
        </div>

        <!-- Wallet Balance Card -->
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl p-6 mb-8 shadow-lg">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold mb-2">Saldo Tersedia</h2>
                    <p class="text-3xl font-bold">{{ $wallet->formatted_balance }}</p>
                    @if ($wallet->pending_balance > 0)
                        <p class="text-primary-200 text-sm mt-1">
                            Pending: Rp {{ number_format($wallet->pending_balance, 0, ',', '.') }}
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    <div class="space-y-3">
                        <a href="{{ route('seller.wallet.topup') }}" 
                           class="bg-white text-primary-600 px-5 py-2.5 rounded-lg font-semibold hover:bg-primary-50 transition inline-flex items-center shadow-sm hover:shadow-md">
                            <i class="fas fa-plus mr-2"></i>Top Up
                        </a>
                        
                        <a href="{{ route('seller.wallet.withdraw') }}" 
                           class="bg-primary-700 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-primary-800 transition inline-flex items-center shadow-sm hover:shadow-md">
                            <i class="fas fa-money-bill-transfer mr-2"></i>Tarik Dana
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        @if($pendingRequests['has_pending'])
        <div class="bg-warning-50 border border-warning-200 rounded-xl p-5 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-warning-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-base font-medium text-warning-800">Permintaan Pending</h3>
                    <div class="mt-1 text-sm text-warning-700">
                        @if($pendingRequests['pending_topup'] > 0)
                            <span class="inline-flex items-center mr-4">
                                <i class="fas fa-arrow-up mr-1"></i>
                                {{ $pendingRequests['pending_topup'] }} Top Up pending
                            </span>
                        @endif
                        @if($pendingRequests['pending_withdraw'] > 0)
                            <span class="inline-flex items-center">
                                <i class="fas fa-arrow-down mr-1"></i>
                                {{ $pendingRequests['pending_withdraw'] }} Withdraw pending
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Transactions History -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-neutral-900">Riwayat Transaksi</h3>
                @if ($transactions->count() > 0)
                    <div class="text-sm text-neutral-500">
                        Total {{ $transactions->total() }} transaksi
                    </div>
                @endif
            </div>

            @if ($transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-neutral-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-neutral-500 uppercase tracking-wider">
                                    Jenis</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-neutral-500 uppercase tracking-wider">
                                    Deskripsi</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-neutral-500 uppercase tracking-wider">
                                    Jumlah</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-neutral-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-neutral-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @foreach ($transactions as $transaction)
                                <tr class="hover:bg-neutral-50 transition">
                                    <td class="px-4 py-4 text-sm text-neutral-900 whitespace-nowrap">
                                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            {{ $transaction->type->increasesBalance() ? 'bg-success-100 text-success-700' : 'bg-error-100 text-error-700' }}">
                                            <i class="fas {{ $transaction->type->increasesBalance() ? 'fa-arrow-down mr-1' : 'fa-arrow-up mr-1' }}"></i>
                                            {{ $transaction->type_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-neutral-900">
                                        {{ Str::limit($transaction->description, 50) }}
                                        @if (strlen($transaction->description) > 50)
                                            <span class="text-neutral-400">...</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-right font-medium whitespace-nowrap">
                                        {{ $transaction->formatted_amount }}
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                            {{ $transaction->status->color() === 'success' ? 'bg-success-100 text-success-700' :
                                               ($transaction->status->color() === 'warning' ? 'bg-warning-100 text-warning-700' :
                                               ($transaction->status->color() === 'danger' ? 'bg-error-100 text-error-700' : 
                                               ($transaction->status->color() === 'info' ? 'bg-info-100 text-info-700' : 'bg-secondary-100 text-secondary-700'))) }}">
                                            <i class="fas {{ $transaction->status->value === 'pending' ? 'fa-clock mr-1' :
                                                    ($transaction->status->value === 'success' ? 'fa-check-circle mr-1' :
                                                    ($transaction->status->value === 'processing' ? 'fa-spinner mr-1' : 'fa-times-circle mr-1')) }}"></i>
                                            {{ $transaction->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center text-sm whitespace-nowrap">
                                        <div class="flex justify-center space-x-3">
                                            <a href="{{ route('seller.wallet.transaction.detail', $transaction->id) }}"
                                                class="text-secondary-600 hover:text-secondary-800 transition flex items-center"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($transaction->canBeCancelled())
                                                <form action="{{ route('seller.wallet.transaction.cancel', $transaction->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="text-error-600 hover:text-error-800 transition flex items-center"
                                                        onclick="return confirm('Yakin ingin membatalkan transaksi ini?')"
                                                        title="Batalkan">
                                                        <i class="fas fa-ban"></i>
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
                <div class="text-center py-12">
                    <div class="text-neutral-300 text-6xl mb-6">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h4 class="text-lg font-medium text-neutral-900 mb-3">Belum ada transaksi</h4>
                    <p class="text-neutral-600 mb-6">Mulai dengan melakukan top up saldo</p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('seller.wallet.topup') }}"
                            class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition inline-flex items-center shadow-md hover:shadow-lg">
                            <i class="fas fa-plus mr-2"></i> Top Up Saldo
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <!-- Top Up Info -->
            <div class="bg-info-50 border border-info-200 rounded-xl p-5">
                <div class="flex">
                    <div class="flex-shrink-0 pt-0.5">
                        <i class="fas fa-university text-info-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-base font-medium text-info-800">Top Up Saldo</h3>
                        <div class="mt-2 text-sm text-info-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Transfer manual ke rekening bank</li>
                                <li>Verifikasi manual oleh admin</li>
                                <li>Proses 1x24 jam</li>
                                <li>Tidak ada biaya tambahan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Withdraw Info -->
            <div class="bg-warning-50 border border-warning-200 rounded-xl p-5">
                <div class="flex">
                    <div class="flex-shrink-0 pt-0.5">
                        <i class="fas fa-money-bill-transfer text-warning-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-base font-medium text-warning-800">Tarik Dana</h3>
                        <div class="mt-2 text-sm text-warning-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Transfer manual ke rekening Anda</li>
                                <li>Proses 1-3 hari kerja</li>
                                <li>Biaya admin sesuai nominal</li>
                                <li>Minimum Rp 50.000</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.plain-app>