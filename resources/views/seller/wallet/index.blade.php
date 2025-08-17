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
                        <!-- Top Up Options -->
                        <div class="relative group">
                            <button class="bg-white text-primary-600 px-5 py-2.5 rounded-lg font-semibold hover:bg-primary-50 transition inline-flex items-center shadow-sm hover:shadow-md">
                                <i class="fas fa-wallet mr-2"></i>Top Up
                                <i class="fas fa-chevron-down ml-2 text-sm"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                                <div class="py-1">
                                    <a href="{{ route('seller.wallet.manual-topup') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        <i class="fas fa-university mr-2"></i>Top Up Manual
                                    </a>
                                    <a href="{{ route('seller.wallet.topup') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        <i class="fas fa-credit-card mr-2"></i>Top Up Otomatis
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Withdraw Options -->
                        <div class="relative group">
                            <button class="bg-primary-700 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-primary-800 transition inline-flex items-center shadow-sm hover:shadow-md">
                                <i class="fas fa-money-bill-transfer mr-2"></i>Tarik Dana
                                <i class="fas fa-chevron-down ml-2 text-sm"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                                <div class="py-1">
                                    <a href="{{ route('seller.wallet.manual-withdraw') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        <i class="fas fa-university mr-2"></i>Tarik Manual
                                    </a>
                                    <a href="{{ route('seller.wallet.withdraw') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        <i class="fas fa-credit-card mr-2"></i>Tarik Otomatis
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        @if(isset($manualRequests) && $manualRequests['has_pending'])
        <div class="bg-warning-50 border border-warning-200 rounded-xl p-5 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-warning-500 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-base font-medium text-warning-800">Permintaan Pending</h3>
                    <div class="mt-1 text-sm text-warning-700">
                        @if($manualRequests['pending_topup'] > 0)
                            <span class="inline-flex items-center mr-4">
                                <i class="fas fa-arrow-up mr-1"></i>
                                {{ $manualRequests['pending_topup'] }} Manual Top Up pending
                            </span>
                            <br>
                            <a href="{{ route('seller.wallet.manual-topup') }}"
                               class="text-warning-600 hover:text-warning-800 transition">
                                Lihat semua permintaan
                            </a>
                        @endif
                        @if($manualRequests['pending_withdraw'] > 0)
                            <br>
                            <span class="inline-flex items-center">
                                <i class="fas fa-arrow-down mr-1"></i>
                                {{ $manualRequests['pending_withdraw'] }} Withdraw pending
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
                                            {{ $transaction->type->value === 'topup' ? 'bg-success-100 text-success-700' : 'bg-error-100 text-error-700' }}">
                                            <i class="fas {{ $transaction->type === 'topup' ? 'fa-arrow-down mr-1' : 'fa-arrow-up mr-1' }}"></i>
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
                                            {{ $transaction->status->value === 'pending'
                                                ? 'bg-warning-100 text-warning-700'
                                                : ($transaction->status->value === 'success'
                                                    ? 'bg-success-100 text-success-700'
                                                    : 'bg-error-100 text-error-700') }}">
                                            <i class="fas {{ $transaction->status->value === 'pending'
                                                    ? 'fa-clock mr-1'
                                                    : ($transaction->status->value === 'success'
                                                        ? 'fa-check-circle mr-1'
                                                        : 'fa-times-circle mr-1') }}"></i>
                                            {{ $transaction->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center text-sm whitespace-nowrap">
                                        <div class="flex justify-center space-x-3">
                                            <a href="{{ route('seller.wallet.transaction.detail', $transaction->id) }}"
                                                class="text-secondary-600 hover:text-secondary-800 transition flex items-center"
                                                x-data="{ tooltip: 'Detail' }" x-tooltip="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($transaction->canBeCancelled())
                                                <form action="{{ route('seller.wallet.transaction.cancel', $transaction->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="text-error-600 hover:text-error-800 transition flex items-center"
                                                        onclick="return confirm('Yakin ingin membatalkan transaksi ini?')"
                                                        x-data="{ tooltip: 'Batalkan' }" x-tooltip="tooltip">
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
                        <a href="{{ route('seller.wallet.manual-topup') }}"
                            class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition inline-flex items-center shadow-md hover:shadow-lg">
                            <i class="fas fa-university mr-2"></i> Top Up Manual
                        </a>
                        <a href="{{ route('seller.wallet.topup') }}"
                            class="bg-secondary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-secondary-700 transition inline-flex items-center shadow-md hover:shadow-lg">
                            <i class="fas fa-credit-card mr-2"></i> Top Up Otomatis
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <!-- Manual Top Up Info -->
            <div class="bg-info-50 border border-info-200 rounded-xl p-5">
                <div class="flex">
                    <div class="flex-shrink-0 pt-0.5">
                        <i class="fas fa-university text-info-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-base font-medium text-info-800">Top Up Manual</h3>
                        <div class="mt-2 text-sm text-info-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Transfer langsung ke rekening bank</li>
                                <li>Verifikasi manual oleh admin</li>
                                <li>Proses 1x24 jam</li>
                                <li>Tidak ada biaya tambahan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auto Top Up Info -->
            <div class="bg-success-50 border border-success-200 rounded-xl p-5">
                <div class="flex">
                    <div class="flex-shrink-0 pt-0.5">
                        <i class="fas fa-credit-card text-success-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-base font-medium text-success-800">Top Up Otomatis</h3>
                        <div class="mt-2 text-sm text-success-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Menggunakan gateway pembayaran</li>
                                <li>Proses instan dan otomatis</li>
                                <li>Berbagai metode pembayaran</li>
                                <li>Aman dan terpercaya</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .group:hover .group-hover\:opacity-100 {
            opacity: 1;
        }
        .group:hover .group-hover\:visible {
            visibility: visible;
        }
    </style>
</x-layouts.plain-app>