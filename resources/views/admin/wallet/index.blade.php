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
            <!-- Top Up Pending Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-warning-100 text-warning-600">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Top Up Pending</p>
                        <p class="text-2xl font-semibold text-neutral-900">{{ $walletStats['pending_topups'] }}</p>
                        <p class="text-xs text-neutral-500">Rp
                            {{ number_format($walletStats['pending_topup_amount'], 0, ',', '.') }}</p>
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
                        <option value="withdraw" {{ $transactionType === 'withdraw' ? 'selected' : '' }}>Penarikan</option>
                    </select>
                </div>
                <!-- Status Transaksi -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Status Transaksi</label>
                    <select name="transaction_status"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua</option>
                        <option value="pending" {{ $transactionStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $transactionStatus === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="success" {{ $transactionStatus === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ $transactionStatus === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ $transactionStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                        <option value="processing" {{ $withdrawStatus === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $withdrawStatus === 'completed' ? 'selected' : '' }}>Completed</option>
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
                    <button onclick="showTab('topups')" id="topups-tab"
                        class="tab-button py-3 px-1 border-b-2 font-medium text-sm border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                        <i class="fas fa-plus-circle mr-2"></i> Top Up Pending
                        @if($walletStats['pending_topups'] > 0)
                            <span class="ml-1 bg-warning-100 text-warning-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $walletStats['pending_topups'] }}</span>
                        @endif
                    </button>
                    <button onclick="showTab('withdraws')" id="withdraws-tab"
                        class="tab-button py-3 px-1 border-b-2 font-medium text-sm border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                        <i class="fas fa-money-bill-wave mr-2"></i> Penarikan Dana
                        @if($walletStats['pending_withdraws'] > 0)
                            <span class="ml-1 bg-error-100 text-error-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ $walletStats['pending_withdraws'] }}</span>
                        @endif
                    </button>
                    <button onclick="showTab('banks')" id="banks-tab"
                        class="tab-button py-3 px-1 border-b-2 font-medium text-sm border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                        <i class="fas fa-university mr-2"></i> Bank Accounts
                    </button>
                </nav>
            </div>
        </div>
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-success-50 border border-success-200 text-success-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-error-50 border border-error-200 text-error-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif
        <!-- Wallets Tab -->
        <div id="wallets-content" class="tab-content">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-neutral-800">Daftar Wallet Pengguna</h3>
                    <div class="text-sm text-neutral-500">
                        Total: {{ $wallets->total() }} wallet
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
                                    <th class="px-4 py-3 text-right">Available Balance</th>
                                    <th class="px-4 py-3 text-center">Terakhir Update</th>
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
                                            {{ $wallet->formatted_balance }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm text-neutral-900">
                                            Rp {{ number_format($wallet->pending_balance, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-success-600">
                                            {{ $wallet->formatted_available_balance }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-neutral-500">
                                            {{ $wallet->updated_at->format('d/m/Y H:i') }}
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
                    <div class="text-sm text-neutral-500">
                        Total: {{ $transactions->total() }} transaksi
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
                                    <th class="px-4 py-3 text-center">Aksi</th>
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
                                                {{ $transaction->type->label() }}
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
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $transaction->status->color() }}-100 text-{{ $transaction->status->color() }}-800">
                                                {{ $transaction->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-neutral-500 font-mono">
                                            {{ $transaction->reference_id ? Str::limit($transaction->reference_id, 15) : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="{{ route('admin.wallets.transaction.detail', $transaction->id) }}"
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
        <!-- Top Up Pending Tab -->
        <div id="topups-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-neutral-800">Top Up Menunggu Persetujuan</h3>
                    <div class="text-sm text-neutral-500">
                        Total: {{ $pendingTopUps->total() }} permintaan
                    </div>
                </div>
                @if ($pendingTopUps->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-neutral-50">
                                <tr class="text-left text-sm font-medium text-neutral-500 border-b border-neutral-200">
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Pengguna</th>
                                    <th class="px-4 py-3 text-center">Ref ID</th>
                                    <th class="px-4 py-3 text-right">Jumlah</th>
                                    <th class="px-4 py-3">Bank Tujuan</th>
                                    <th class="px-4 py-3 text-center">Bukti</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200">
                                @foreach ($pendingTopUps as $topup)
                                    <tr class="hover:bg-neutral-50 transition">
                                        <td class="px-4 py-3 text-sm text-neutral-900">
                                            {{ $topup->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="text-sm font-medium text-neutral-900">
                                                    {{ $topup->wallet->user->name }}</div>
                                                <div class="text-sm text-neutral-500">{{ $topup->wallet->user->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm font-mono text-neutral-900">
                                            {{ $topup->reference_id }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-success-600">
                                            +Rp {{ number_format($topup->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="text-neutral-900 font-medium">{{ $topup->bank_name ?? '-' }}</div>
                                            <div class="text-neutral-500">{{ $topup->bank_account_number ?? '-' }}</div>
                                            <div class="text-neutral-500">{{ $topup->bank_account_name ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($topup->payment_proof_path)
                                                <button onclick="viewPaymentProof('{{ $topup->payment_proof_url }}')"
                                                    class="text-secondary-600 hover:text-secondary-800 transition">
                                                    <i class="fas fa-image mr-1"></i> Lihat
                                                </button>
                                            @else
                                                <span class="text-neutral-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <div class="flex justify-center space-x-2">
                                                <button onclick="approveTopUp({{ $topup->id }})"
                                                    class="text-success-600 hover:text-success-800 font-medium transition">
                                                    <i class="fas fa-check mr-1"></i> Setuju
                                                </button>
                                                <button onclick="rejectTopUp({{ $topup->id }})"
                                                    class="text-error-600 hover:text-error-800 font-medium transition">
                                                    <i class="fas fa-times mr-1"></i> Tolak
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $pendingTopUps->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-neutral-300 text-5xl mb-4">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h4 class="text-lg font-medium text-neutral-900 mb-2">Tidak ada top up pending</h4>
                        <p class="text-neutral-600">Belum ada permintaan top up yang perlu diproses</p>
                    </div>
                @endif
            </div>
        </div>
        <!-- Withdraw Requests Tab -->
        <div id="withdraws-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-neutral-800">Permintaan Penarikan Dana</h3>
                    <div class="text-sm text-neutral-500">
                        Total: {{ $withdrawStatus === 'all' ? $withdrawRequests->total() : $pendingWithdraws->total() }} permintaan
                    </div>
                </div>
                @php
                    $displayWithdraws = $withdrawStatus === 'all' ? $withdrawRequests : $pendingWithdraws;
                @endphp
                @if ($displayWithdraws->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-neutral-50">
                                <tr class="text-left text-sm font-medium text-neutral-500 border-b border-neutral-200">
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Pengguna</th>
                                    <th class="px-4 py-3 text-center">Ref ID</th>
                                    <th class="px-4 py-3 text-right">Jumlah</th>
                                    <th class="px-4 py-3">Bank Tujuan</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200">
                                @foreach ($displayWithdraws as $withdraw)
                                    <tr class="hover:bg-neutral-50 transition">
                                        <td class="px-4 py-3 text-sm text-neutral-900">
                                            {{ $withdraw->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="text-sm font-medium text-neutral-900">
                                                    {{ $withdraw->wallet->user->name }}</div>
                                                <div class="text-sm text-neutral-500">{{ $withdraw->wallet->user->email }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm font-mono text-neutral-900">
                                            {{ $withdraw->reference_id }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-neutral-900">
                                            -Rp {{ number_format($withdraw->amount, 0, ',', '.') }}
                                            @if ($withdraw->admin_fee > 0)
                                                <div class="text-xs text-neutral-500">
                                                    Fee: Rp {{ number_format($withdraw->admin_fee, 0, ',', '.') }}
                                                </div>
                                                <div class="text-xs text-success-600 font-semibold">
                                                    Net: Rp {{ number_format($withdraw->amount - $withdraw->admin_fee, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="text-neutral-900 font-medium">{{ $withdraw->bank_name ?? '-' }}</div>
                                            <div class="text-neutral-500 font-mono">{{ $withdraw->account_number ?? '-' }}</div>
                                            <div class="text-neutral-500">{{ $withdraw->account_name ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $withdraw->status->color() }}-100 text-{{ $withdraw->status->color() }}-800">
                                                {{ $withdraw->status->label() }}
                                            </span>
                                            @if($withdraw->processed_at)
                                                <div class="text-xs text-neutral-500 mt-1">
                                                    {{ $withdraw->processed_at->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            @if ($withdraw->status->value === 'pending')
                                                <div class="flex justify-center space-x-1">
                                                    {{-- <button
                                                        onclick="processWithdraw('{{ $withdraw->id }}', 'processing')"
                                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 font-medium transition"
                                                        title="Mulai Proses">
                                                        <i class="fas fa-cog mr-1"></i> Proses
                                                    </button> --}}
                                                    <button 
                                                        onclick="processWithdraw('{{ $withdraw->id }}', 'completed')"
                                                        class="px-3 py-1 text-xs bg-success-100 text-success-700 rounded-md hover:bg-success-200 font-medium transition"
                                                        title="Langsung Selesaikan">
                                                        <i class="fas fa-check mr-1"></i> Selesai
                                                    </button>
                                                    <button onclick="processWithdraw('{{ $withdraw->id }}', 'failed')"
                                                        class="px-3 py-1 text-xs bg-error-100 text-error-700 rounded-md hover:bg-error-200 font-medium transition"
                                                        title="Tolak/Gagalkan">
                                                        <i class="fas fa-times mr-1"></i> Tolak
                                                    </button>
                                                </div>
                                            @elseif($withdraw->status->value === 'processing')
                                                <div class="flex justify-center space-x-1">
                                                    <button
                                                        onclick="processWithdraw('{{ $withdraw->id }}', 'completed')"
                                                        class="px-3 py-1 text-xs bg-success-100 text-success-700 rounded-md hover:bg-success-200 font-medium transition"
                                                        title="Selesaikan Transfer">
                                                        <i class="fas fa-check-double mr-1"></i> Selesai
                                                    </button>
                                                    <button onclick="processWithdraw('{{ $withdraw->id }}', 'failed')"
                                                        class="px-3 py-1 text-xs bg-error-100 text-error-700 rounded-md hover:bg-error-200 font-medium transition"
                                                        title="Gagalkan Transfer">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i> Gagal
                                                    </button>
                                                </div>
                                            @else
                                                <div class="flex justify-center space-x-1">
                                                    <a href="{{ route('admin.wallets.transaction.detail', $withdraw->id) }}"
                                                        class="px-3 py-1 text-xs bg-neutral-100 text-neutral-700 rounded-md hover:bg-neutral-200 transition">
                                                        <i class="fas fa-eye mr-1"></i> Detail
                                                    </a>
                                                    @if($withdraw->admin_notes)
                                                        <button onclick="showAdminNotes('{{ $withdraw->admin_notes }}')"
                                                            class="px-3 py-1 text-xs bg-warning-100 text-warning-700 rounded-md hover:bg-warning-200 transition"
                                                            title="Lihat Catatan Admin">
                                                            <i class="fas fa-sticky-note mr-1"></i> Catatan
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $displayWithdraws->appends(request()->query())->links() }}
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
        <!-- Bank Accounts Tab -->
        <div id="banks-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm border border-neutral-200">
                <!-- Add Bank Account Button -->
                <div class="p-6 border-b border-neutral-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-neutral-800">Manajemen Rekening Bank</h3>
                        <button onclick="showAddBankModal()"
                            class="bg-primary-500 text-white px-4 py-2 rounded-md hover:bg-primary-600 transition focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <i class="fas fa-plus mr-2"></i> Tambah Rekening
                        </button>
                    </div>
                </div>
                <!-- Bank Accounts List -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="bankAccountsList">
                        <!-- Bank accounts will be loaded here -->
                        <div class="text-center py-8">
                            <div class="text-neutral-400 text-4xl mb-4">
                                <i class="fas fa-university"></i>
                            </div>
                            <p class="text-neutral-500">Loading bank accounts...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Proof Modal -->
    <div id="paymentProofModal" class="fixed inset-0 bg-neutral-900/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-5xl mx-4 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-neutral-900">Bukti Pembayaran</h3>
                <button onclick="closePaymentProofModal()" class="text-neutral-400 hover:text-neutral-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="text-center">
                <img id="paymentProofImage" src="" alt="Bukti Pembayaran" class="max-w-full h-auto rounded-lg shadow-md">
            </div>
        </div>
    </div>
    <!-- Top Up Approval Modal -->
    <div id="topupModal" class="fixed inset-0 bg-neutral-900/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 min-w-md mx-4 shadow-xl">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4" id="topupModalTitle">Setujui Top Up</h3>
            <form id="topupForm" method="POST">
                @csrf
                <input type="hidden" id="topupId" name="topup_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Catatan Admin (Opsional)</label>
                    <textarea name="admin_notes" rows="3"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Tambahkan catatan..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeTopupModal()"
                        class="px-4 py-2 text-neutral-700 bg-neutral-200 rounded-md hover:bg-neutral-300 transition">
                        Batal
                    </button>
                    <button type="submit" id="topupConfirmButton"
                        class="px-4 py-2 text-white bg-success-500 rounded-md hover:bg-success-600 transition">
                        <i class="fas fa-check mr-2"></i> Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Process Withdraw Modal -->
    <div id="processModal" class="fixed inset-0 bg-neutral-900/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 min-w-md mx-4 shadow-xl">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4" id="processModalTitle">Proses Penarikan Dana</h3>
            <form id="processForm" method="POST">
                @csrf
                <input type="hidden" id="withdrawId" name="withdraw_id">
                <input type="hidden" id="newStatus" name="status">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Catatan Admin</label>
                    <textarea name="admin_notes" id="adminNotesTextarea" rows="3"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Tambahkan catatan..."></textarea>
                    <p class="text-xs text-neutral-500 mt-1" id="notesHint">Opsional untuk proses dan selesai, wajib untuk tolak/gagal</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeProcessModal()"
                        class="px-4 py-2 text-neutral-700 bg-neutral-200 rounded-md hover:bg-neutral-300 transition">
                        Batal
                    </button>
                    <button type="submit" id="confirmButton"
                        class="px-4 py-2 text-white bg-primary-500 rounded-md hover:bg-primary-600 transition">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Admin Notes Modal -->
    <div id="adminNotesModal" class="fixed inset-0 bg-neutral-900/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-5xl mx-4 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-neutral-900">Catatan Admin</h3>
                <button onclick="closeAdminNotesModal()" class="text-neutral-400 hover:text-neutral-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="bg-neutral-50 p-4 rounded-lg">
                <p id="adminNotesContent" class="text-neutral-700"></p>
            </div>
        </div>
    </div>
    <!-- Add Bank Account Modal -->
    <div id="addBankModal" class="fixed inset-0 bg-neutral-900/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-5xl mx-4 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-neutral-900">Tambah Rekening Bank</h3>
                <button onclick="closeAddBankModal()" class="text-neutral-400 hover:text-neutral-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="addBankForm" method="POST" action="{{ route('admin.wallets.bank-accounts.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Nama Bank</label>
                    <select name="bank_name" required
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Pilih Bank</option>
                        <option value="BCA">BCA</option>
                        <option value="BNI">BNI</option>
                        <option value="BRI">BRI</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="CIMB Niaga">CIMB Niaga</option>
                        <option value="Danamon">Danamon</option>
                        <option value="Permata">Permata</option>
                        <option value="BTN">BTN</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Nomor Rekening</label>
                    <input type="text" name="account_number" required
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Masukkan nomor rekening">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Nama Pemilik</label>
                    <input type="text" name="account_name" required
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Masukkan nama pemilik rekening">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">QR Code (Opsional)</label>
                    <input type="file" name="qr_code" accept="image/*"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-neutral-500 mt-1">Format: JPG, JPEG, PNG. Max: 2MB</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddBankModal()"
                        class="px-4 py-2 text-neutral-700 bg-neutral-200 rounded-md hover:bg-neutral-300 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-white bg-primary-500 rounded-md hover:bg-primary-600 transition">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit Bank Account Modal -->
    <div id="editBankModal" class="fixed inset-0 bg-neutral-900/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-5xl mx-4 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-neutral-900">Edit Rekening Bank</h3>
                <button onclick="closeEditBankModal()" class="text-neutral-400 hover:text-neutral-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="editBankForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editBankId" name="bank_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Nama Bank</label>
                    <select name="bank_name" id="editBankName" required
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Pilih Bank</option>
                        <option value="BCA">BCA</option>
                        <option value="BNI">BNI</option>
                        <option value="BRI">BRI</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="CIMB Niaga">CIMB Niaga</option>
                        <option value="Danamon">Danamon</option>
                        <option value="Permata">Permata</option>
                        <option value="BTN">BTN</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Nomor Rekening</label>
                    <input type="text" name="account_number" id="editAccountNumber" required
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Nama Pemilik</label>
                    <input type="text" name="account_name" id="editAccountName" required
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="editIsActive" value="1" class="mr-2">
                        <span class="text-sm font-medium text-neutral-700">Aktif</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">QR Code (Opsional)</label>
                    <input type="file" name="qr_code" accept="image/*"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-neutral-500 mt-1">Biarkan kosong jika tidak ingin mengubah</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditBankModal()"
                        class="px-4 py-2 text-neutral-700 bg-neutral-200 rounded-md hover:bg-neutral-300 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-white bg-primary-500 rounded-md hover:bg-primary-600 transition">
                        <i class="fas fa-save mr-2"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.querySelectorAll('.tab-button').forEach(tab => {
                tab.classList.remove('border-primary-500', 'text-primary-600');
                tab.classList.add('border-transparent', 'text-neutral-500');
            });
            document.getElementById(tabName + '-content').classList.remove('hidden');
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.classList.remove('border-transparent', 'text-neutral-500');
            activeTab.classList.add('border-primary-500', 'text-primary-600');
            if (tabName === 'banks') {
                fetchBankAccounts();
            }
        }
        function viewPaymentProof(imageUrl) {
            document.getElementById('paymentProofImage').src = imageUrl;
            document.getElementById('paymentProofModal').classList.remove('hidden');
            document.getElementById('paymentProofModal').classList.add('flex');
        }
        function closePaymentProofModal() {
            document.getElementById('paymentProofModal').classList.add('hidden');
            document.getElementById('paymentProofModal').classList.remove('flex');
        }
        function approveTopUp(topupId) {
            document.getElementById('topupId').value = topupId;
            document.getElementById('topupForm').action = `/admin/wallet/topup/${topupId}/approve`;
            document.getElementById('topupModalTitle').textContent = 'Setujui Top Up';
            document.getElementById('topupConfirmButton').innerHTML = '<i class="fas fa-check mr-2"></i> Setujui';
            document.getElementById('topupConfirmButton').className = 'px-4 py-2 text-white bg-success-500 rounded-md hover:bg-success-600 transition';
            document.getElementById('topupModal').classList.remove('hidden');
            document.getElementById('topupModal').classList.add('flex');
        }
        function rejectTopUp(topupId) {
            document.getElementById('topupId').value = topupId;
            document.getElementById('topupForm').action = `/admin/wallet/topup/${topupId}/reject`;
            document.getElementById('topupModalTitle').textContent = 'Tolak Top Up';
            document.getElementById('topupConfirmButton').innerHTML = '<i class="fas fa-times mr-2"></i> Tolak';
            document.getElementById('topupConfirmButton').className = 'px-4 py-2 text-white bg-error-500 rounded-md hover:bg-error-600 transition';
            document.querySelector('#topupForm textarea[name="admin_notes"]').setAttribute('required', 'required');
            document.querySelector('#topupForm textarea[name="admin_notes"]').placeholder = 'Alasan penolakan harus diisi...';
            document.getElementById('topupModal').classList.remove('hidden');
            document.getElementById('topupModal').classList.add('flex');
        }
        function closeTopupModal() {
            document.getElementById('topupModal').classList.add('hidden');
            document.getElementById('topupModal').classList.remove('flex');
            document.getElementById('topupForm').reset();
            document.querySelector('#topupForm textarea[name="admin_notes"]').removeAttribute('required');
        }
        function processWithdraw(withdrawId, status) {
            document.getElementById('withdrawId').value = withdrawId;
            document.getElementById('newStatus').value = status;
            document.getElementById('processForm').action = `/admin/wallet/withdraw/${withdrawId}/process`;
            const processModalTitle = document.getElementById('processModalTitle');
            const confirmButton = document.getElementById('confirmButton');
            const adminNotesTextarea = document.getElementById('adminNotesTextarea');
            const notesHint = document.getElementById('notesHint');
            adminNotesTextarea.value = '';
            adminNotesTextarea.removeAttribute('required');
            const statusConfig = {
                'processing': {
                    title: 'Mulai Proses Penarikan',
                    buttonText: '<i class="fas fa-cog mr-2"></i> Mulai Proses',
                    buttonClass: 'px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 transition',
                    required: false,
                    hint: 'Tambahkan catatan proses (opsional)'
                },
                'completed': {
                    title: 'Selesaikan Penarikan',
                    buttonText: '<i class="fas fa-check mr-2"></i> Selesaikan',
                    buttonClass: 'px-4 py-2 text-white bg-success-500 rounded-md hover:bg-success-600 transition',
                    required: false,
                    hint: 'Konfirmasi bahwa transfer berhasil (catatan opsional)'
                },
                'failed': {
                    title: 'Tolak/Gagalkan Penarikan',
                    buttonText: '<i class="fas fa-times mr-2"></i> Tolak/Gagalkan',
                    buttonClass: 'px-4 py-2 text-white bg-error-500 rounded-md hover:bg-error-600 transition',
                    required: true,
                    hint: 'Alasan penolakan/kegagalan harus diisi'
                }
            };
            const config = statusConfig[status];
            processModalTitle.textContent = config.title;
            confirmButton.innerHTML = config.buttonText;
            confirmButton.className = config.buttonClass;
            notesHint.textContent = config.hint;
            if (config.required) {
                adminNotesTextarea.setAttribute('required', 'required');
                adminNotesTextarea.placeholder = 'Alasan penolakan/kegagalan harus diisi...';
            } else {
                adminNotesTextarea.placeholder = 'Tambahkan catatan...';
            }
            document.getElementById('processModal').classList.remove('hidden');
            document.getElementById('processModal').classList.add('flex');
        }
        function closeProcessModal() {
            document.getElementById('processModal').classList.add('hidden');
            document.getElementById('processModal').classList.remove('flex');
            document.getElementById('processForm').reset();
            document.getElementById('adminNotesTextarea').removeAttribute('required');
        }
        function showAdminNotes(notes) {
            document.getElementById('adminNotesContent').textContent = notes;
            document.getElementById('adminNotesModal').classList.remove('hidden');
            document.getElementById('adminNotesModal').classList.add('flex');
        }
        function closeAdminNotesModal() {
            document.getElementById('adminNotesModal').classList.add('hidden');
            document.getElementById('adminNotesModal').classList.remove('flex');
        }
        // Ganti fungsi ini di kode Anda

        async function fetchBankAccounts() {
            const bankListContainer = document.getElementById('bankAccountsList');
            const url = "{{ route('admin.wallets.bank-accounts.load') }}";

            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const bankAccounts = await response.json();
                
                // Kosongkan dulu kontainer list banknya
                bankListContainer.innerHTML = ''; 

                if (bankAccounts.length > 0) {
                    // Jika ada data, loop dan buat HTML untuk setiap rekening bank
                    bankAccounts.forEach(bank => {
                        console.log('Bank Account:', bank);
                        const bankCard = `
                            <div class="bg-white rounded-lg shadow-sm p-4 border border-neutral-200 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="text-lg font-semibold text-neutral-800">${bank.bank_name}</h4>
                                        <span class="text-xs font-medium px-2 py-1 rounded-full ${
                                            bank.is_active 
                                            ? 'bg-success-100 text-success-800' 
                                            : 'bg-neutral-200 text-neutral-700'
                                        }">
                                            ${bank.is_active ? 'Aktif' : 'Nonaktif'}
                                        </span>
                                    </div>
                                    <p class="text-neutral-600 text-sm">No. Rek: <span class="font-mono font-medium">${bank.account_number}</span></p>
                                    <p class="text-neutral-600 text-sm">a.n: <span class="font-medium">${bank.account_name}</span></p>
                                    ${bank.qr_code ? `<img src="${bank.qr_code}" alt="QR Code" class="mt-2 w-full object-cover">` : ''}
                                </div>
                                <div class="flex justify-end items-center mt-4 pt-4 border-t border-neutral-200 space-x-2">
                                    <button onclick="showEditBankModal(${bank.id}, '${bank.bank_name}', '${bank.account_number}', '${bank.account_name}', ${bank.is_active})"
                                        class="text-sm text-secondary-600 hover:text-secondary-800 transition font-medium">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </button>
                                    <button onclick="deleteBankAccount(${bank.id})"
                                        class="text-sm text-error-600 hover:text-error-800 transition font-medium">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        `;
                        // Masukkan HTML card bank ke dalam kontainer
                        bankListContainer.innerHTML += bankCard;
                    });
                } else {
                    // Jika tidak ada data, tampilkan pesan 'tidak ada data'
                    bankListContainer.innerHTML = `
                        <div class="col-span-1 md:col-span-2 text-center py-12">
                            <div class="text-neutral-300 text-5xl mb-4">
                                <i class="fas fa-university"></i>
                            </div>
                            <h4 class="text-lg font-medium text-neutral-900 mb-2">Belum ada rekening bank</h4>
                            <p class="text-neutral-600">Silakan tambahkan rekening bank baru.</p>
                        </div>
                    `;
                }

            } catch (error) {
                console.error('Gagal mengambil data rekening bank:', error);
                // Tampilkan pesan error di UI jika gagal fetch
                bankListContainer.innerHTML = `
                    <div class="col-span-1 md:col-span-2 text-center py-12">
                        <div class="text-error-400 text-5xl mb-4">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4 class="text-lg font-medium text-error-800 mb-2">Gagal memuat data</h4>
                        <p class="text-neutral-600">Terjadi kesalahan saat mengambil data rekening bank. Silakan coba lagi.</p>
                    </div>
                `;
            }
        }
        function showAddBankModal() {
            document.getElementById('addBankModal').classList.remove('hidden');
            document.getElementById('addBankModal').classList.add('flex');
        }
        function closeAddBankModal() {
            document.getElementById('addBankModal').classList.add('hidden');
            document.getElementById('addBankModal').classList.remove('flex');
        }
        function showEditBankModal(bankId, bankName, accountNumber, accountName, isActive) {
            document.getElementById('editBankId').value = bankId;
            document.getElementById('editBankName').value = bankName;
            document.getElementById('editAccountNumber').value = accountNumber;
            document.getElementById('editAccountName').value = accountName;
            document.getElementById('editIsActive').checked = isActive;
            document.getElementById('editBankForm').action = `/admin/wallet/bank-accounts/${bankId}`;
            document.getElementById('editBankModal').classList.remove('hidden');
            document.getElementById('editBankModal').classList.add('flex');
        }
        function closeEditBankModal() {
            document.getElementById('editBankModal').classList.add('hidden');
            document.getElementById('editBankModal').classList.remove('flex');
        }
        function deleteBankAccount(bankId) {
            if (confirm('Apakah Anda yakin ingin menghapus rekening bank ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/wallet/bank-accounts/${bankId}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        document.getElementById('paymentProofModal').addEventListener('click', function(e) {
            if (e.target === this) closePaymentProofModal();
        });
        document.getElementById('topupModal').addEventListener('click', function(e) {
            if (e.target === this) closeTopupModal();
        });
        document.getElementById('processModal').addEventListener('click', function(e) {
            if (e.target === this) closeProcessModal();
        });
        document.getElementById('adminNotesModal').addEventListener('click', function(e) {
            if (e.target === this) closeAdminNotesModal();
        });
        document.getElementById('addBankModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddBankModal();
        });
        document.getElementById('editBankModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditBankModal();
        });
        setInterval(function() {
            const currentTab = document.querySelector('.tab-button.border-primary-500');
            if (currentTab && (currentTab.id === 'topups-tab' || currentTab.id === 'withdraws-tab')) {
                const urlParams = new URLSearchParams(window.location.search);
                if (!urlParams.get('withdraw_status') || urlParams.get('withdraw_status') === '' || urlParams.get('withdraw_status') === 'pending') {
                    location.reload();
                }
            }
        }, 30000); 
        function viewWalletDetail(walletId) {
            alert('Detail wallet akan ditampilkan untuk wallet ID: ' + walletId);
        }
        document.getElementById('processForm').addEventListener('submit', function(e) {
            const status = document.getElementById('newStatus').value;
            const adminNotes = document.getElementById('adminNotesTextarea').value.trim();
            if ((status === 'failed' || status === 'cancelled') && !adminNotes) {
                e.preventDefault();
                alert('Catatan admin wajib diisi untuk status tolak/gagal!');
                document.getElementById('adminNotesTextarea').focus();
                return false;
            }
            const statusText = {
                'processing': 'memulai proses',
                'completed': 'menyelesaikan',
                'failed': 'menolak/gagalkan'
            };
            if (!confirm(`Apakah Anda yakin ingin ${statusText[status]} penarikan ini?`)) {
                e.preventDefault();
                return false;
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const transactionType = urlParams.get('transaction_type');
            const withdrawStatus = urlParams.get('withdraw_status');
            if (transactionType === 'topup') {
                showTab('topups');
            } else if (transactionType === 'withdraw' || withdrawStatus) {
                showTab('withdraws');
            } else {
                showTab('wallets'); 
            }
        });
        document.getElementById('addBankForm').addEventListener('submit', function(e) {
            const bankName = this.querySelector('[name="bank_name"]').value;
            const accountNumber = this.querySelector('[name="account_number"]').value;
            const accountName = this.querySelector('[name="account_name"]').value;
            if (!bankName || !accountNumber || !accountName) {
                e.preventDefault();
                alert('Semua field wajib harus diisi!');
                return false;
            }
            if (accountNumber.length < 8 || accountNumber.length > 20) {
                e.preventDefault();
                alert('Nomor rekening harus antara 8-20 digit!');
                return false;
            }
        });
        document.getElementById('editBankForm').addEventListener('submit', function(e) {
            const bankName = this.querySelector('[name="bank_name"]').value;
            const accountNumber = this.querySelector('[name="account_number"]').value;
            const accountName = this.querySelector('[name="account_name"]').value;
            if (!bankName || !accountNumber || !accountName) {
                e.preventDefault();
                alert('Semua field wajib harus diisi!');
                return false;
            }
        });
        @if(session('success'))
            setTimeout(function() {
                const successAlert = document.querySelector('.bg-success-50');
                if (successAlert) {
                    successAlert.style.opacity = '0';
                    setTimeout(() => successAlert.style.display = 'none', 300);
                }
            }, 5000);
        @endif
        @if(session('error'))
            setTimeout(function() {
                const errorAlert = document.querySelector('.bg-error-50');
                if (errorAlert) {
                    errorAlert.style.opacity = '0';
                    setTimeout(() => errorAlert.style.display = 'none', 300);
                }
            }, 5000);
        @endif
        function setupSearch() {
            const searchInputs = document.querySelectorAll('input[name="search"]');
            searchInputs.forEach(input => {
                let timeout;
                input.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        if (this.value.length >= 3 || this.value.length === 0) {
                            this.closest('form').submit();
                        }
                    }, 500);
                });
            });
        }
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('input[name="selected_transactions[]"]');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateBulkActions();
        }
        function updateBulkActions() {
            const selectedCount = document.querySelectorAll('input[name="selected_transactions[]"]:checked').length;
            const bulkActionsPanel = document.getElementById('bulkActionsPanel');
            if (selectedCount > 0) {
                bulkActionsPanel?.classList.remove('hidden');
                document.getElementById('selectedCount').textContent = selectedCount;
            } else {
                bulkActionsPanel?.classList.add('hidden');
            }
        }
        function quickFilterStatus(status) {
            const form = document.querySelector('form');
            const statusSelect = form.querySelector('[name="transaction_status"]');
            statusSelect.value = status;
            form.submit();
        }
        function exportTransactions(format = 'excel') {
            const params = new URLSearchParams(window.location.search);
            params.set('export', format);
            window.open(`${window.location.pathname}?${params.toString()}`);
        }
        function initializeWebSocket() {
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modals = [
                    'paymentProofModal', 'topupModal', 'processModal', 
                    'adminNotesModal', 'addBankModal', 'editBankModal'
                ];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal && !modal.classList.contains('hidden')) {
                        const closeFunction = window[`close${modalId.replace('Modal', '').replace(/^[a-z]/, c => c.toUpperCase())}Modal`];
                        if (closeFunction) closeFunction();
                        else {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        }
                    }
                });
            }
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.querySelector('input[name="search"]')?.focus();
            }
        });
        setupSearch();
        function initializeTooltips() {
            const tooltipElements = document.querySelectorAll('[title]');
            tooltipElements.forEach(element => {
                element.addEventListener('mouseenter', function(e) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'absolute bg-neutral-800 text-white text-xs px-2 py-1 rounded shadow-lg z-50 -mt-8';
                    tooltip.textContent = this.getAttribute('title');
                    tooltip.id = 'tooltip-' + Math.random().toString(36).substr(2, 9);
                    this.setAttribute('data-tooltip-id', tooltip.id);
                    this.removeAttribute('title'); 
                    document.body.appendChild(tooltip);
                    const rect = this.getBoundingClientRect();
                    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
                    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
                });
                element.addEventListener('mouseleave', function() {
                    const tooltipId = this.getAttribute('data-tooltip-id');
                    if (tooltipId) {
                        const tooltip = document.getElementById(tooltipId);
                        if (tooltip) tooltip.remove();
                        this.removeAttribute('data-tooltip-id');
                    }
                });
            });
        }
        setTimeout(initializeTooltips, 100);
    </script>
</x-layouts.plain-app>