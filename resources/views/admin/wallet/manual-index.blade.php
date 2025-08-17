<x-layouts.plain-app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900 mb-2">Manajemen Wallet Manual</h1>
            <p class="text-neutral-600">Kelola permintaan top up dan penarikan dana manual</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Pending Top Up -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-warning-100 text-warning-600">
                        <i class="fas fa-arrow-up text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Top Up Pending</p>
                        <p class="text-2xl font-semibold text-neutral-900">{{ $stats['pending_topup'] }}</p>
                        <p class="text-xs text-neutral-500">Rp {{ number_format($stats['pending_topup_amount'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Withdraw -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-error-100 text-error-600">
                        <i class="fas fa-arrow-down text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Withdraw Pending</p>
                        <p class="text-2xl font-semibold text-neutral-900">{{ $stats['pending_withdraw'] }}</p>
                        <p class="text-xs text-neutral-500">Rp {{ number_format($stats['pending_withdraw_amount'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Today Approved -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-success-100 text-success-600">
                        <i class="fas fa-check text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Top Up Disetujui Hari Ini</p>
                        <p class="text-2xl font-semibold text-neutral-900">{{ $stats['today_approved_topup'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Today Completed -->
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-secondary-100 text-secondary-600">
                        <i class="fas fa-money-bill-transfer text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Withdraw Selesai Hari Ini</p>
                        <p class="text-2xl font-semibold text-neutral-900">{{ $stats['today_completed_withdraw'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8 border border-neutral-200">
            <h3 class="text-lg font-semibold mb-4 text-neutral-800">Filter & Pencarian</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Pencarian</label>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Nama, email, atau kode..."
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Status Top Up</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="waiting_approval" {{ $status === 'waiting_approval' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="waiting_payment" {{ $status === 'waiting_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Submit -->
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-primary-500 text-white px-4 py-2 rounded-md hover:bg-primary-600 transition focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6">
            <div class="border-b border-neutral-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('topup')" id="topup-tab"
                        class="tab-button py-3 px-1 border-b-2 font-medium text-sm border-primary-500 text-primary-600">
                        <i class="fas fa-arrow-up mr-2"></i> Permintaan Top Up
                    </button>
                    <button onclick="showTab('withdraw')" id="withdraw-tab"
                        class="tab-button py-3 px-1 border-b-2 font-medium text-sm border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                        <i class="fas fa-arrow-down mr-2"></i> Permintaan Withdraw
                    </button>
                </nav>
            </div>
        </div>

        <!-- Top Up Requests Tab -->
        <div id="topup-content" class="tab-content">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-neutral-800">Permintaan Top Up Manual</h3>
                </div>

                @if ($topUpRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-neutral-50">
                                <tr class="text-left text-sm font-medium text-neutral-500 border-b border-neutral-200">
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Pengguna</th>
                                    <th class="px-4 py-3">Kode</th>
                                    <th class="px-4 py-3 text-right">Jumlah</th>
                                    <th class="px-4 py-3">Bank</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200">
                                @foreach ($topUpRequests as $request)
                                    <tr class="hover:bg-neutral-50 transition">
                                        <td class="px-4 py-3 text-sm text-neutral-900">
                                            {{ $request->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="text-sm font-medium text-neutral-900">{{ $request->user->name }}</div>
                                                <div class="text-sm text-neutral-500">{{ $request->user->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-mono text-neutral-900">
                                            {{ $request->request_code }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-neutral-900">
                                            {{ $request->formatted_amount }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($request->bank_name)
                                                <div class="text-neutral-900 font-medium">{{ $request->bank_name }}</div>
                                                <div class="text-neutral-500">{{ $request->bank_account_number }}</div>
                                            @else
                                                <span class="text-neutral-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $request->status->color() === 'success' ? 'bg-success-100 text-success-800' :
                                                   ($request->status->color() === 'warning' ? 'bg-warning-100 text-warning-800' :
                                                   ($request->status->color() === 'danger' ? 'bg-error-100 text-error-800' : 'bg-info-100 text-info-800')) }}">
                                                {{ $request->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('admin.manual-wallet.topup.detail', $request->id) }}"
                                                    class="text-secondary-600 hover:text-secondary-800 font-medium transition">
                                                    <i class="fas fa-eye mr-1"></i> Detail
                                                </a>
                                                
                                                @if($request->status->value === 'waiting_approval')
                                                    <button onclick="approveTopUp('{{ $request->id }}')"
                                                        class="text-success-600 hover:text-success-800 font-medium transition">
                                                        <i class="fas fa-check mr-1"></i> Setujui
                                                    </button>
                                                    <button onclick="rejectTopUp('{{ $request->id }}')"
                                                        class="text-error-600 hover:text-error-800 font-medium transition">
                                                        <i class="fas fa-times mr-1"></i> Tolak
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $topUpRequests->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-neutral-300 text-5xl mb-4">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <h4 class="text-lg font-medium text-neutral-900 mb-2">Tidak ada permintaan top up</h4>
                        <p class="text-neutral-600">Belum ada permintaan top up yang sesuai dengan filter</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Withdraw Requests Tab -->
        <div id="withdraw-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-neutral-800">Permintaan Penarikan Manual</h3>
                </div>

                @if ($withdrawRequests->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-neutral-50">
                                <tr class="text-left text-sm font-medium text-neutral-500 border-b border-neutral-200">
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Pengguna</th>
                                    <th class="px-4 py-3">Kode</th>
                                    <th class="px-4 py-3 text-right">Jumlah</th>
                                    <th class="px-4 py-3">Bank Tujuan</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200">
                                @foreach ($withdrawRequests as $request)
                                    <tr class="hover:bg-neutral-50 transition">
                                        <td class="px-4 py-3 text-sm text-neutral-900">
                                            {{ $request->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="text-sm font-medium text-neutral-900">{{ $request->user->name }}</div>
                                                <div class="text-sm text-neutral-500">{{ $request->user->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-mono text-neutral-900">
                                            {{ $request->withdrawal_code }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-neutral-900">
                                            Rp {{ number_format($request->amount, 0, ',', '.') }}
                                            @if($request->admin_fee > 0)
                                                <div class="text-xs text-neutral-500">
                                                    Fee: Rp {{ number_format($request->admin_fee, 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="text-neutral-900 font-medium">{{ $request->bank_name }}</div>
                                            <div class="text-neutral-500">{{ $request->account_number }}</div>
                                            <div class="text-neutral-500">{{ $request->account_name }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $request->status === 'pending' ? 'bg-warning-100 text-warning-800' :
                                                   ($request->status === 'processing' ? 'bg-info-100 text-info-800' :
                                                   ($request->status === 'completed' ? 'bg-success-100 text-success-800' : 'bg-error-100 text-error-800')) }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('admin.manual-wallet.withdraw.detail', $request->id) }}"
                                                    class="text-secondary-600 hover:text-secondary-800 font-medium transition">
                                                    <i class="fas fa-eye mr-1"></i> Detail
                                                </a>
                                                
                                                @if($request->status === 'pending')
                                                    <button onclick="processWithdraw('{{ $request->id }}', 'processing')"
                                                        class="text-info-600 hover:text-info-800 font-medium transition">
                                                        <i class="fas fa-cog mr-1"></i> Proses
                                                    </button>
                                                    <button onclick="processWithdraw('{{ $request->id }}', 'completed')"
                                                        class="text-success-600 hover:text-success-800 font-medium transition">
                                                        <i class="fas fa-check mr-1"></i> Selesai
                                                    </button>
                                                    <button onclick="processWithdraw('{{ $request->id }}', 'failed')"
                                                        class="text-error-600 hover:text-error-800 font-medium transition">
                                                        <i class="fas fa-times mr-1"></i> Tolak
                                                    </button>
                                                @elseif($request->status === 'processing')
                                                    <button onclick="processWithdraw('{{ $request->id }}', 'completed')"
                                                        class="text-success-600 hover:text-success-800 font-medium transition">
                                                        <i class="fas fa-check mr-1"></i> Selesai
                                                    </button>
                                                    <button onclick="processWithdraw('{{ $request->id }}', 'failed')"
                                                        class="text-error-600 hover:text-error-800 font-medium transition">
                                                        <i class="fas fa-times mr-1"></i> Gagal
                                                    </button>
                                                @endif
                                            </div>
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
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <h4 class="text-lg font-medium text-neutral-900 mb-2">Tidak ada permintaan penarikan</h4>
                        <p class="text-neutral-600">Belum ada permintaan penarikan yang pending</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Approve Top Up Modal -->
    <div id="approveModal" class="fixed inset-0 bg-neutral-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 min-w-lg mx-4 shadow-xl">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Setujui Permintaan Top Up</h3>
            <form id="approveForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Catatan Admin (Opsional)</label>
                    <textarea name="admin_notes" rows="3"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Tambahkan catatan..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('approveModal')"
                        class="px-4 py-2 text-sm text-neutral-700 bg-neutral-200 rounded-md hover:bg-neutral-300 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm text-white bg-success-500 rounded-md hover:bg-success-600 transition">
                        <i class="fas fa-check mr-2"></i> Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Top Up Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-neutral-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 min-w-lg mx-4 shadow-xl">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Tolak Permintaan Top Up</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Alasan Penolakan <span class="text-error-500">*</span></label>
                    <textarea name="admin_notes" rows="3" required
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('rejectModal')"
                        class="px-4 py-2 text-sm text-neutral-700 bg-neutral-200 rounded-md hover:bg-neutral-300 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm text-white bg-error-500 rounded-md hover:bg-error-600 transition">
                        <i class="fas fa-times mr-2"></i> Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Process Withdraw Modal -->
    <div id="processModal" class="fixed inset-0 bg-neutral-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 min-w-lg mx-4 shadow-xl">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Proses Permintaan Penarikan</h3>
            <form id="processForm" method="POST">
                @csrf
                <input type="hidden" id="withdrawStatus" name="status">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">Catatan Admin (Opsional)</label>
                    <textarea name="admin_notes" rows="3"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Tambahkan catatan..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('processModal')"
                        class="px-4 py-2 text-sm text-neutral-700 bg-neutral-200 rounded-md hover:bg-neutral-300 transition">
                        Batal
                    </button>
                    <button type="submit" id="processSubmitBtn"
                        class="px-4 py-2 text-sm text-white bg-primary-500 rounded-md hover:bg-primary-600 transition">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab functionality
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
        }

        // Top Up functions
        function approveTopUp(id) {
            document.getElementById('approveForm').action = `/admin/manual-wallet/topup/${id}/approve`;
            document.getElementById('approveModal').classList.remove('hidden');
            document.getElementById('approveModal').classList.add('flex');
        }

        function rejectTopUp(id) {
            document.getElementById('rejectForm').action = `/admin/manual-wallet/topup/${id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        }

        // Withdraw functions
        function processWithdraw(id, status) {
            document.getElementById('processForm').action = `/admin/manual-wallet/withdraw/${id}/process`;
            document.getElementById('withdrawStatus').value = status;
            
            const submitBtn = document.getElementById('processSubmitBtn');
            const statusText = {
                'processing': 'Proses',
                'completed': 'Selesaikan',
                'failed': 'Tolak/Gagalkan'
            };

            submitBtn.innerHTML = `<i class="fas fa-${status === 'completed' ? 'check' : (status === 'failed' ? 'times' : 'cog')} mr-2"></i> ${statusText[status]}`;

            if (status === 'failed') {
                submitBtn.className = 'px-4 py-2 text-sm text-white bg-error-500 rounded-md hover:bg-error-600 transition';
            } else if (status === 'completed') {
                submitBtn.className = 'px-4 py-2 text-sm text-white bg-success-500 rounded-md hover:bg-success-600 transition';
            } else {
                submitBtn.className = 'px-4 py-2 text-sm text-white bg-info-500 rounded-md hover:bg-info-600 transition';
            }

            document.getElementById('processModal').classList.remove('hidden');
            document.getElementById('processModal').classList.add('flex');
        }

        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.getElementById(modalId).classList.remove('flex');
        }

        // Close modals when clicking outside
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        // Auto refresh every 30 seconds
        setInterval(() => {
            if (document.querySelector('.tab-button.border-primary-500').id === 'topup-tab') {
                location.reload();
            }
        }, 30000);
    </script>
</x-layouts.plain-app>
