<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Manajemen Wallet</h1>
            <p class="text-gray-600">Kelola semua wallet, transaksi, dan penarikan dana pengguna</p>
        </div>

        

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 0h10a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Wallet</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($walletStats['total_wallets']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Saldo</p>
                        <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($walletStats['total_balance'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Saldo Pending</p>
                        <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($walletStats['total_pending_balance'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Penarikan Pending</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $walletStats['pending_withdraws'] }}</p>
                        <p class="text-xs text-gray-500">Rp {{ number_format($walletStats['pending_withdraw_amount'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-2">Top Up Hari Ini</h3>
                <p class="text-2xl font-bold">Rp {{ number_format($walletStats['total_topup_today'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-2">Penarikan Hari Ini</h3>
                <p class="text-2xl font-bold">Rp {{ number_format($walletStats['total_withdraw_today'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-2">Transaksi Hari Ini</h3>
                <p class="text-2xl font-bold">{{ number_format($walletStats['total_transactions_today']) }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4">Filter & Pencarian</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Nama atau email user..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Transaksi</label>
                    <select name="transaction_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="topup" {{ $transactionType === 'topup' ? 'selected' : '' }}>Top Up</option>
                        <option value="withdraw" {{ $transactionType === 'withdraw' ? 'selected' : '' }}>Penarikan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Transaksi</label>
                    <select name="transaction_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="pending" {{ $transactionStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="success" {{ $transactionStatus === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ $transactionStatus === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ $transactionStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Penarikan</label>
                    <select name="withdraw_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pending Only</option>
                        <option value="all" {{ $withdrawStatus === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="pending" {{ $withdrawStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $withdrawStatus === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $withdrawStatus === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ $withdrawStatus === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Per Halaman</label>
                    <select name="per_page" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('wallets')" id="wallets-tab" 
                            class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                        Daftar Wallet
                    </button>
                    <button onclick="showTab('transactions')" id="transactions-tab" 
                            class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Transaksi
                    </button>
                    <button onclick="showTab('withdraws')" id="withdraws-tab" 
                            class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Penarikan Dana
                    </button>
                </nav>
            </div>
        </div>

        <!-- Wallets Tab -->
        <div id="wallets-content" class="tab-content">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4">Daftar Wallet Pengguna</h3>
                
                @if($wallets->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Pengguna</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Saldo Aktif</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Saldo Pending</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Terakhir Update</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($wallets as $wallet)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $wallet->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $wallet->user->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                                            Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm text-gray-900">
                                            Rp {{ number_format($wallet->pending_balance, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-500">
                                            {{ $wallet->updated_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">Detail</a>
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
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-4">👛</div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Tidak ada wallet ditemukan</h4>
                        <p class="text-gray-600">Belum ada wallet yang sesuai dengan filter pencarian</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Transactions Tab -->
        <div id="transactions-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4">Transaksi Terbaru</h3>
                
                @if($transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Pengguna</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Jenis</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Deskripsi</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Jumlah</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Status</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Ref ID</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $transaction->wallet->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $transaction->wallet->user->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $transaction->type->value === 'topup' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $transaction->type->value === 'topup' ? 'Top Up' : 'Penarikan' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ Str::limit($transaction->description, 40) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-medium
                                            {{ $transaction->type->value === 'topup' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->type->value === 'topup' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $transaction->status->value === 'success' ? 'bg-green-100 text-green-800' : 
                                                   ($transaction->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($transaction->status->value === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                                {{ ucfirst($transaction->status->value) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-500 font-mono">
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
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-4">📊</div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Tidak ada transaksi ditemukan</h4>
                        <p class="text-gray-600">Belum ada transaksi yang sesuai dengan filter pencarian</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Withdraw Requests Tab -->
        <div id="withdraws-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4">Permintaan Penarikan Dana</h3>
                
                @if($withdrawRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Pengguna</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Kode</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Jumlah</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Bank</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Status</th>
                                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($withdrawRequests as $withdraw)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $withdraw->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $withdraw->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $withdraw->user->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm font-mono text-gray-900">
                                            {{ $withdraw->withdrawal_code }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                                            Rp {{ number_format($withdraw->amount, 0, ',', '.') }}
                                            @if($withdraw->admin_fee > 0)
                                                <div class="text-xs text-gray-500">
                                                    Fee: Rp {{ number_format($withdraw->admin_fee, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="text-gray-900 font-medium">{{ $withdraw->bank_name }}</div>
                                            <div class="text-gray-500">{{ $withdraw->account_number }}</div>
                                            <div class="text-gray-500">{{ $withdraw->account_name }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $withdraw->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($withdraw->status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                                   ($withdraw->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) }}">
                                                {{ ucfirst($withdraw->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            @if($withdraw->status === 'pending')
                                                <div class="flex justify-center space-x-2">
                                                    <button onclick="processWithdraw('{{ $withdraw->id }}', 'processing')" 
                                                            class="text-blue-600 hover:text-blue-800 font-medium">
                                                        Proses
                                                    </button>
                                                    <button onclick="processWithdraw('{{ $withdraw->id }}', 'failed')" 
                                                            class="text-red-600 hover:text-red-800 font-medium">
                                                        Tolak
                                                    </button>
                                                </div>
                                            @elseif($withdraw->status === 'processing')
                                                <div class="flex justify-center space-x-2">
                                                    <button onclick="processWithdraw('{{ $withdraw->id }}', 'completed')" 
                                                            class="text-green-600 hover:text-green-800 font-medium">
                                                        Selesai
                                                    </button>
                                                    <button onclick="processWithdraw('{{ $withdraw->id }}', 'failed')" 
                                                            class="text-red-600 hover:text-red-800 font-medium">
                                                        Gagal
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-gray-500">-</span>
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
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-4">💸</div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Tidak ada permintaan penarikan</h4>
                        <p class="text-gray-600">Belum ada permintaan penarikan yang sesuai dengan filter</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Process Withdraw Modal -->
    <div id="processModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Proses Penarikan Dana</h3>
            <form id="processForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="withdrawId" name="withdraw_id">
                <input type="hidden" id="newStatus" name="status">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin (Opsional)</label>
                    <textarea name="admin_notes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                        Batal
                    </button>
                    <button type="submit" id="confirmButton"
                            class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">
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
                tab.classList.remove('border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected content
            document.getElementById(tabName + '-content').classList.remove('hidden');
            
            // Add active state to selected tab
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-blue-500', 'text-blue-600');
        }

        // Process withdraw modal
        function processWithdraw(withdrawId, status) {
            document.getElementById('withdrawId').value = withdrawId;
            document.getElementById('newStatus').value = status;
            
            const form = document.getElementById('processForm');
            form.action = `/admin/withdraw-requests/${withdrawId}/process`;
            
            const confirmButton = document.getElementById('confirmButton');
            const statusText = {
                'processing': 'Proses',
                'completed': 'Selesaikan', 
                'failed': 'Tolak/Gagalkan'
            };
            
            confirmButton.textContent = statusText[status] || 'Konfirmasi';
            confirmButton.className = status === 'failed' ? 
                'px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700 transition' :
                'px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 transition';
            
            document.getElementById('processModal').classList.remove('hidden');
            document.getElementById('processModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('processModal').classList.add('hidden');
            document.getElementById('processModal').classList.remove('flex');
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
                const currentTab = document.querySelector('.tab-button.border-blue-500');
                if (currentTab && currentTab.id === 'withdraws-tab') {
                    location.reload();
                }
            }
        }, 30000);
    </script>
</x-layouts.plain-app>