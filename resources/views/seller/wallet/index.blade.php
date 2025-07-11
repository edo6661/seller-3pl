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
                            class="bg-white text-primary-600 px-5 py-2.5 rounded-lg font-semibold hover:bg-primary-50 transition inline-block shadow-sm hover:shadow-md">
                            <i class="fas fa-wallet mr-2"></i>Top Up
                        </a>
                        <br>
                        <a href="{{ route('seller.wallet.withdraw') }}"
                            class="bg-primary-700 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-primary-800 transition inline-block shadow-sm hover:shadow-md">
                            <i class="fas fa-money-bill-transfer mr-2"></i>Tarik Dana
                        </a>
                    </div>
                </div>
            </div>
        </div>

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
                                <th
                                    class="px-4 py-3 text-left text-sm font-medium text-neutral-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-4 py-3 text-left text-sm font-medium text-neutral-500 uppercase tracking-wider">
                                    Jenis</th>
                                <th
                                    class="px-4 py-3 text-left text-sm font-medium text-neutral-500 uppercase tracking-wider">
                                    Deskripsi</th>
                                <th
                                    class="px-4 py-3 text-right text-sm font-medium text-neutral-500 uppercase tracking-wider">
                                    Jumlah</th>
                                <th
                                    class="px-4 py-3 text-center text-sm font-medium text-neutral-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-center text-sm font-medium text-neutral-500 uppercase tracking-wider">
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
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            {{ $transaction->type->value === 'topup' ? 'bg-success-100 text-success-700' : 'bg-error-100 text-error-700' }}">
                                            <i
                                                class="fas {{ $transaction->type->value === 'topup' ? 'fa-arrow-down mr-1' : 'fa-arrow-up mr-1' }}"></i>
                                            {{ $transaction->type_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-neutral-900">
                                        {{ Str::limit($transaction->description, 50) }}
                                        @if (strlen($transaction->description) > 50)
                                            <span class="text-neutral-400">...</span>
                                        @endif
                                    </td>
                                    <td
                                        class="px-4 py-4 text-sm text-right font-medium whitespace-nowrap
                                        {{ $transaction->type->value === 'topup' ? 'text-success-600' : 'text-error-600' }}">
                                        {{ $transaction->formatted_amount }}
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                            {{ $transaction->status === 'pending'
                                                ? 'bg-warning-100 text-warning-700'
                                                : ($transaction->status === 'success'
                                                    ? 'bg-success-100 text-success-700'
                                                    : 'bg-error-100 text-error-700') }}">
                                            <i
                                                class="fas {{ $transaction->status === 'pending'
                                                    ? 'fa-clock mr-1'
                                                    : ($transaction->status === 'success'
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
                                                <form
                                                    action="{{ route('seller.wallet.transaction.cancel', $transaction->id) }}"
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
                    <a href="{{ route('seller.wallet.topup') }}"
                        class="bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition inline-flex items-center shadow-md hover:shadow-lg">
                        <i class="fas fa-plus-circle mr-2"></i> Top Up Sekarang
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.plain-app>
