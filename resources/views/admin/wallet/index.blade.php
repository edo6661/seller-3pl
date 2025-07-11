<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900 mb-2">Manajemen Wallet</h1>
            <p class="text-neutral-600">Kelola semua wallet, transaksi, dan penarikan dana pengguna</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Wallet Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-secondary-100 text-secondary-600">
                        <i class="fas fa-wallet text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Total Wallet</p>
                        <p class="text-2xl font-semibold text-neutral-900">
                            {{ number_format($walletStats['total_wallets']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Saldo Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-success-100 text-success-600">
                        <i class="fas fa-coins text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Total Saldo</p>
                        <p class="text-2xl font-semibold text-neutral-900">Rp
                            {{ number_format($walletStats['total_balance'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Saldo Pending Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-warning-100 text-warning-600">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Saldo Pending</p>
                        <p class="text-2xl font-semibold text-neutral-900">Rp
                            {{ number_format($walletStats['total_pending_balance'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Penarikan Pending Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-error-100 text-error-600">
                        <i class="fas fa-money-bill-wave text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Penarikan Pending</p>
                        <p class="text-2xl font-semibold text-neutral-900">{{ $walletStats['pending_withdraws'] }}</p>
                        <p class="text-xs text-neutral-500">Rp
                            {{ number_format($walletStats['pending_withdraw_amount'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Top Up Hari Ini -->
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-2">Top Up Hari Ini</h3>
                <p class="text-2xl font-bold">Rp {{ number_format($walletStats['total_topup_today'], 0, ',', '.') }}</p>
            </div>

            <!-- Penarikan Hari Ini -->
            <div class="bg-gradient-to-r from-error-500 to-error-600 text-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-2">Penarikan Hari Ini</h3>
                <p class="text-2xl font-bold">Rp {{ number_format($walletStats['total_withdraw_today'], 0, ',', '.') }}
                </p>
            </div>

            <!-- Transaksi Hari Ini -->
            <div class="bg-gradient-to-r from-secondary-500 to-secondary-600 text-white rounded-lg p-6 shadow-md">
                <h3 class="text-lg font-semibold mb-2">Transaksi Hari Ini</h3>
                <p class="text-2xl font-bold">{{ number_format($walletStats['total_transactions_today']) }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8 border border-neutral-200">
            <h3 class="text-lg font-semibold mb-4 text-neutral-800">Filter & Pencarian</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Pencarian -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Pencarian</label>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Nama atau email user..."
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Jenis Transaksi -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Jenis Transaksi</label>
                    <select name="transaction_type"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua</option>
                        <option value="topup" {{ $transactionType === 'topup' ? 'selected' : '' }}>Top Up</option>
                        <option value="withdraw" {{ $transactionType === 'withdraw' ? 'selected' : '' }}>Penarikan
                        </option>
                    </select>
                </div>

                <!-- Status Transaksi -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Status Transaksi</label>
                    <select name="transaction_status"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua</option>
                        <option value="pending" {{ $transactionStatus === 'pending' ? 'selected' : '' }}>Pending
                        </option>
                        <option value="success" {{ $transactionStatus === 'success' ? 'selected' : '' }}>Success
                        </option>
                        <option value="failed" {{ $transactionStatus === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ $transactionStatus === 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>

                <!-- Status Penarikan -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Status Penarikan</label>
                    <select name="withdraw_status"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Pending Only</option>
                        <option value="all" {{ $withdrawStatus === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="pending" {{ $withdrawStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $withdrawStatus === 'processing' ? 'selected' : '' }}>Processing
                        </option>
                        <option value="completed" {{ $withdrawStatus === 'completed' ? 'selected' : '' }}>Completed
                        </option>
                        <option value="failed" {{ $withdrawStatus === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Per Halaman -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Per Halaman</label>
                    <select name="per_page"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <!-- Tombol Filter -->
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-primary-500 text-white px-4 py-2 rounded-md hover:bg-primary-600 transition focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-6">
            <div class="border-b border-neutral-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('wallets')" id="wallets-tab"
                        class="tab-button py-3 px-1 border-b-2 font-medium text-sm border-primary-500 text-primary-600">
                        <i class="fas fa-wallet mr-2"></i> Daftar Wallet
                    </button>
                    <button onclick="showTab('transactions')" id="transactions-tab"
                        class="tab-button py-3 px-1 border-b-2 font-medium text-sm border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                        <i class="fas fa-exchange-alt mr-2"></i> Transaksi
                    </button>
                    <button onclick="showTab('withdraws')" id="withdraws-tab"
                        class="tab-button py-3 px-1 border-b-2 font-medium text-sm border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                        <i class="fas fa-money-bill-wave mr-2"></i> Penarikan Dana
                    </button>
                </nav>
            </div>
        </div>

        <!-- Wallets Tab -->
        <div id="wallets-content" class="tab-content">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-neutral-800">Daftar Wallet Pengguna</h3>
                    <div class="relative">
                        <input type="text" placeholder="Cari wallet..."
                            class="pl-8 pr-4 py-2 border border-neutral-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <i class="fas fa-search absolute left-3 top-3 text-neutral-400"></i>
                    </div>
                </div>

                @if ($wallets->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-neutral-50">
                                <tr class="text-left text-sm font-medium text-neutral-500 border-b border-neutral-200">
                                    <th class="px-4 py-3">Pengguna</th>
                                    <th class="px-4 py-3 text-right">Saldo Aktif</th>
                                    <th class="px-4 py-3 text-right">Saldo Pending</th>
                                    <th class="px-4 py-3 text-center">Terakhir Update</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200">
                                @foreach ($wallets as $wallet)
                                    <tr class="hover:bg-neutral-50 transition">
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="text-sm font-medium text-neutral-900">
                                                    {{ $wallet->user->name }}</div>
                                                <div class="text-sm text-neutral-500">{{ $wallet->user->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-neutral-900">
                                            Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm text-neutral-900">
                                            Rp {{ number_format($wallet->pending_balance, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-neutral-500">
                                            {{ $wallet->updated_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="#"
                                                class="text-secondary-600 hover:text-secondary-800 transition">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $wallets->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-neutral-300 text-5xl mb-4">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4 class="text-lg font-medium text-neutral-900 mb-2">Tidak ada wallet ditemukan</h4>
                        <p class="text-neutral-600">Belum ada wallet yang sesuai dengan filter pencarian</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Transactions Tab -->
        <div id="transactions-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-neutral-800">Transaksi Terbaru</h3>
                    <div class="relative">
                        <input type="text" placeholder="Cari transaksi..."
                            class="pl-8 pr-4 py-2 border border-neutral-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <i class="fas fa-search absolute left-3 top-3 text-neutral-400"></i>
                    </div>
                </div>

                @if ($transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-neutral-50">
                                <tr class="text-left text-sm font-medium text-neutral-500 border-b border-neutral-200">
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Pengguna</th>
                                    <th class="px-4 py-3">Jenis</th>
                                    <th class="px-4 py-3">Deskripsi</th>
                                    <th class="px-4 py-3 text-right">Jumlah</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Ref ID</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200">
                                @foreach ($transactions as $transaction)
                                    <tr class="hover:bg-neutral-50 transition">
                                        <td class="px-4 py-3 text-sm text-neutral-900">
                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="text-sm font-medium text-neutral-900">
                                                    {{ $transaction->wallet->user->name }}</div>
                                                <div class="text-sm text-neutral-500">
                                                    {{ $transaction->wallet->user->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $transaction->type->value === 'topup' ? 'bg-success-100 text-success-800' : 'bg-error-100 text-error-800' }}">
                                                {{ $transaction->type->value === 'topup' ? 'Top Up' : 'Penarikan' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-neutral-900">
                                            {{ Str::limit($transaction->description, 40) }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm text-right font-medium
                                            {{ $transaction->type->value === 'topup' ? 'text-success-600' : 'text-error-600' }}">
                                            {{ $transaction->type->value === 'topup' ? '+' : '-' }}Rp
                                            {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $transaction->status->value === 'success'
                                                    ? 'bg-success-100 text-success-800'
                                                    : ($transaction->status->value === 'pending'
                                                        ? 'bg-warning-100 text-warning-800'
                                                        : ($transaction->status->value === 'failed'
                                                            ? 'bg-error-100 text-error-800'
                                                            : 'bg-neutral-100 text-neutral-800')) }}">
                                                {{ ucfirst($transaction->status->value) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-neutral-500 font-mono">
                                            {{ $transaction->reference_id ? Str::limit($transaction->reference_id, 15) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-neutral-300 text-5xl mb-4">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <h4 class="text-lg font-medium text-neutral-900 mb-2">Tidak ada transaksi ditemukan</h4>
                        <p class="text-neutral-600">Belum ada transaksi yang sesuai dengan filter pencarian</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Withdraw Requests Tab -->
        <div id="withdraws-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-neutral-800">Permintaan Penarikan Dana</h3>
                    <div class="relative">
                        <input type="text" placeholder="Cari penarikan..."
                            class="pl-8 pr-4 py-2 border border-neutral-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <i class="fas fa-search absolute left-3 top-3 text-neutral-400"></i>
                    </div>
                </div>

                @if ($withdrawRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-neutral-50">
                                <tr class="text-left text-sm font-medium text-neutral-500 border-b border-neutral-200">
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Pengguna</th>
                                    <th class="px-4 py-3 text-center">Kode</th>
                                    <th class="px-4 py-3 text-right">Jumlah</th>
                                    <th class="px-4 py-3">Bank</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200">
                                @foreach ($withdrawRequests as $withdraw)
                                    <tr class="hover:bg-neutral-50 transition">
                                        <td class="px-4 py-3 text-sm text-neutral-900">
                                            {{ $withdraw->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="text-sm font-medium text-neutral-900">
                                                    {{ $withdraw->user->name }}</div>
                                                <div class="text-sm text-neutral-500">{{ $withdraw->user->email }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm font-mono text-neutral-900">
                                            {{ $withdraw->withdrawal_code }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-neutral-900">
                                            Rp {{ number_format($withdraw->amount, 0, ',', '.') }}
                                            @if ($withdraw->admin_fee > 0)
                                                <div class="text-xs text-neutral-500">
                                                    Fee: Rp {{ number_format($withdraw->admin_fee, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="text-neutral-900 font-medium">{{ $withdraw->bank_name }}</div>
                                            <div class="text-neutral-500">{{ $withdraw->account_number }}</div>
                                            <div class="text-neutral-500">{{ $withdraw->account_name }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $withdraw->status === 'pending'
                                                    ? 'bg-warning-100 text-warning-800'
                                                    : ($withdraw->status === 'processing'
                                                        ? 'bg-secondary-100 text-secondary-800'
                                                        : ($withdraw->status === 'completed'
                                                            ? 'bg-success-100 text-success-800'
                                                            : 'bg-error-100 text-error-800')) }}">
                                                {{ ucfirst($withdraw->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            @if ($withdraw->status === 'pending')
                                                <div class="flex justify-center space-x-2">
                                                    <button
                                                        onclick="processWithdraw('{{ $withdraw->id }}', 'processing')"
                                                        class="text-secondary-600 hover:text-secondary-800 font-medium transition">
                                                        <i class="fas fa-cog mr-1"></i> Proses
                                                    </button>
                                                    <button onclick="processWithdraw('{{ $withdraw->id }}', 'failed')"
                                                        class="text-error-600 hover:text-error-800 font-medium transition">
                                                        <i class="fas fa-times mr-1"></i> Tolak
                                                    </button>
                                                </div>
                                            @elseif($withdraw->status === 'processing')
                                                <div class="flex justify-center space-x-2">
                                                    <button
                                                        onclick="processWithdraw('{{ $withdraw->id }}', 'completed')"
                                                        class="text-success-600 hover:text-success-800 font-medium transition">
                                                        <i class="fas fa-check mr-1"></i> Selesai
                                                    </button>
                                                    <button onclick="processWithdraw('{{ $withdraw->id }}', 'failed')"
                                                        class="text-error-600 hover:text-error-800 font-medium transition">
                                                        <i class="fas fa-times mr-1"></i> Gagal
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-neutral-500">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $withdrawRequests->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-neutral-300 text-5xl mb-4">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h4 class="text-lg font-medium text-neutral-900 mb-2">Tidak ada permintaan penarikan</h4>
                        <p class="text-neutral-600">Belum ada permintaan penarikan yang sesuai dengan filter</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Process Withdraw Modal -->
    <div id="processModal"
        class="fixed inset-0 bg-neutral-900 bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-lg p-4 min-w-lg mx-4 shadow-xl transform transition-all duration-300">
            <h3 class="text-md font-semibold text-neutral-900 mb-3">Proses Penarikan Dana</h3>
            <form id="processForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="withdrawId" name="withdraw_id">
                <input type="hidden" id="newStatus" name="status">

                <div class="mb-3">
                    <label class="block text-xs font-medium text-neutral-700 mb-1">Catatan Admin (Opsional)</label>
                    <textarea name="admin_notes" rows="2"
                        class="w-full px-2 py-1 text-sm border border-neutral-300 rounded-md focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Tambahkan catatan..."></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()"
                        class="px-3 py-1 text-xs text-neutral-700 bg-neutral-200 rounded-md hover:bg-neutral-300 transition focus:outline-none focus:ring-1 focus:ring-neutral-500">
                        Batal
                    </button>
                    <button type="submit" id="confirmButton"
                        class="px-3 py-1 text-xs text-white bg-primary-500 rounded-md hover:bg-primary-600 transition focus:outline-none focus:ring-1 focus:ring-primary-500">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab functionality
        function showTab(tabName) {
            // Hide all content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tabs
            document.querySelectorAll('.tab-button').forEach(tab => {
                tab.classList.remove('border-primary-500', 'text-primary-600');
                tab.classList.add('border-transparent', 'text-neutral-500');
            });

            // Show selected content
            document.getElementById(tabName + '-content').classList.remove('hidden');

            // Add active state to selected tab
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.classList.remove('border-transparent', 'text-neutral-500');
            activeTab.classList.add('border-primary-500', 'text-primary-600');
        }

        // Process withdraw modal
        function processWithdraw(withdrawId, status) {
            document.getElementById('withdrawId').value = withdrawId;
            document.getElementById('newStatus').value = status;

            const form = document.getElementById('processForm');
            form.action = `/admin/withdraw-requests/${withdrawId}/process`;

            const confirmButton = document.getElementById('confirmButton');
            const statusText = {
                'processing': '<i class="fas fa-cog mr-2"></i> Proses',
                'completed': '<i class="fas fa-check mr-2"></i> Selesaikan',
                'failed': '<i class="fas fa-times mr-2"></i> Tolak/Gagalkan'
            };

            confirmButton.innerHTML = statusText[status] || 'Konfirmasi';

            // Update button color based on action
            // Update button color based on action
            if (status === 'failed') {
                confirmButton.className =
                    'px-3 py-1 text-xs text-white bg-error-500 rounded-md hover:bg-error-600 transition focus:outline-none focus:ring-1 focus:ring-error-500';
            } else if (status === 'completed') {
                confirmButton.className =
                    'px-3 py-1 text-xs text-white bg-success-500 rounded-md hover:bg-success-600 transition focus:outline-none focus:ring-1 focus:ring-success-500';
            } else {
                confirmButton.className =
                    'px-3 py-1 text-xs text-white bg-primary-500 rounded-md hover:bg-primary-600 transition focus:outline-none focus:ring-1 focus:ring-primary-500';
            }

            // Show modal with animation
            const modal = document.getElementById('processModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.querySelector('div').classList.add('opacity-100', 'scale-100');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('processModal');
            modal.querySelector('div').classList.remove('opacity-100', 'scale-100');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('processModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Auto-refresh every 30 seconds for pending status
        setInterval(function() {
            if (new URLSearchParams(window.location.search).get('withdraw_status') === '' ||
                new URLSearchParams(window.location.search).get('withdraw_status') === 'pending') {
                // Only refresh if we're viewing pending withdrawals
                const currentTab = document.querySelector('.tab-button.border-primary-500');
                if (currentTab && currentTab.id === 'withdraws-tab') {
                    location.reload();
                }
            }
        }, 30000);
    </script>
</x-layouts.plain-app>
