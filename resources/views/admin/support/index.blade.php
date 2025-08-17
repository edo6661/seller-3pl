<x-layouts.plain-app>
    <x-slot name="title">Kelola Tiket Support</x-slot>

    <div class="container mx-auto px-4 py-6" x-data="{ 
        showBulkModal: false, 
        selectedTickets: [], 
        bulkAction: '', 
        bulkAdminId: '',
        bulkStatus: '',
        selectAll: false,
        toggleAll() {
            this.selectAll = !this.selectAll;
            if (this.selectAll) {
                this.selectedTickets = [...document.querySelectorAll('input[name=\'ticket_ids[]\']')].map(cb => cb.value);
            } else {
                this.selectedTickets = [];
            }
        }
    }">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-50 to-secondary-50 rounded-xl p-6 mb-8 border border-primary-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-neutral-800">Kelola Tiket Support</h1>
                    <p class="text-sm text-neutral-600 mt-1">Manajemen tiket bantuan dari pengguna</p>
                </div>
                <div class="flex gap-3 mt-4 md:mt-0">
                    <a href="{{ route('admin.support.dashboard') }}"
                        class="bg-white hover:bg-neutral-50 text-neutral-700 font-medium py-2.5 px-4 rounded-lg transition duration-200 flex items-center text-sm border border-neutral-200">
                        <i class="fas fa-chart-pie mr-2"></i>
                        Dashboard
                    </a>
                    <button @click="selectedTickets.length > 0 ? showBulkModal = true : alert('Pilih tiket terlebih dahulu')"
                        class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 flex items-center text-sm">
                        <i class="fas fa-tasks mr-2"></i>
                        Bulk Action
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
            <div class="bg-gradient-to-br from-secondary-50 to-white rounded-xl shadow-xs p-5 border border-secondary-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-secondary-100 text-secondary-600">
                        <i class="fas fa-ticket-alt text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Total Tiket</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-xs p-5 border border-blue-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-blue-100 text-blue-600">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Terbuka</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ number_format($stats['open']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-white rounded-xl shadow-xs p-5 border border-yellow-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-yellow-100 text-yellow-600">
                        <i class="fas fa-hourglass-half text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Dalam Proses</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ number_format($stats['in_progress']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-white rounded-xl shadow-xs p-5 border border-orange-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-orange-100 text-orange-600">
                        <i class="fas fa-user-clock text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Menunggu User</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ number_format($stats['waiting_user']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-success-50 to-white rounded-xl shadow-xs p-5 border border-success-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-success-100 text-success-600">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Diselesaikan</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ number_format($stats['resolved']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-gradient-to-r from-neutral-50 to-neutral-50 rounded-xl shadow-xs p-5 border border-neutral-200 mb-6">
            <form action="{{ route('admin.support.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" name="search" value="{{ $filters['search'] }}"
                            placeholder="Cari tiket, user, atau subjek..."
                            class="w-full pl-10 pr-4 py-2.5 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-400 focus:border-transparent text-neutral-700 placeholder-neutral-400 bg-white">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-neutral-400"></i>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <select name="status" class="border border-neutral-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-400 focus:border-transparent text-neutral-700 text-sm bg-white">
                        <option value="">Semua Status</option>
                        <option value="open" {{ $filters['status'] === 'open' ? 'selected' : '' }}>Terbuka</option>
                        <option value="in_progress" {{ $filters['status'] === 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                        <option value="waiting_user" {{ $filters['status'] === 'waiting_user' ? 'selected' : '' }}>Menunggu User</option>
                        <option value="resolved" {{ $filters['status'] === 'resolved' ? 'selected' : '' }}>Diselesaikan</option>
                        <option value="closed" {{ $filters['status'] === 'closed' ? 'selected' : '' }}>Ditutup</option>
                    </select>

                    <!-- Priority Filter -->
                    <select name="priority" class="border border-neutral-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-400 focus:border-transparent text-neutral-700 text-sm bg-white">
                        <option value="">Semua Prioritas</option>
                        <option value="low" {{ $filters['priority'] === 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ $filters['priority'] === 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ $filters['priority'] === 'high' ? 'selected' : '' }}>Tinggi</option>
                        <option value="urgent" {{ $filters['priority'] === 'urgent' ? 'selected' : '' }}>Mendesak</option>
                    </select>

                    <!-- Category Filter -->
                    <select name="category" class="border border-neutral-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-400 focus:border-transparent text-neutral-700 text-sm bg-white">
                        <option value="">Semua Kategori</option>
                        <option value="delivery_issue" {{ $filters['category'] === 'delivery_issue' ? 'selected' : '' }}>Masalah Pengiriman</option>
                        <option value="payment_issue" {{ $filters['category'] === 'payment_issue' ? 'selected' : '' }}>Masalah Pembayaran</option>
                        <option value="item_damage" {{ $filters['category'] === 'item_damage' ? 'selected' : '' }}>Barang Rusak</option>
                        <option value="item_lost" {{ $filters['category'] === 'item_lost' ? 'selected' : '' }}>Barang Hilang</option>
                        <option value="wrong_address" {{ $filters['category'] === 'wrong_address' ? 'selected' : '' }}>Alamat Salah</option>
                        <option value="courier_service" {{ $filters['category'] === 'courier_service' ? 'selected' : '' }}>Masalah Kurir</option>
                        <option value="app_technical" {{ $filters['category'] === 'app_technical' ? 'selected' : '' }}>Masalah Teknis</option>
                        <option value="account_issue" {{ $filters['category'] === 'account_issue' ? 'selected' : '' }}>Masalah Akun</option>
                        <option value="other" {{ $filters['category'] === 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>

                    <!-- Assigned Admin Filter -->
                    <select name="assigned_to" class="border border-neutral-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-400 focus:border-transparent text-neutral-700 text-sm bg-white">
                        <option value="">Semua Admin</option>
                        @foreach ($adminUsers as $admin)
                            <option value="{{ $admin->id }}" {{ $filters['assigned_to'] == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-between">
                    <div class="flex gap-2">
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 flex items-center text-sm">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                        @if (array_filter($filters))
                            <a href="{{ route('admin.support.index') }}"
                                class="bg-white hover:bg-neutral-50 text-neutral-700 font-medium py-2.5 px-4 rounded-lg transition duration-200 flex items-center text-sm border border-neutral-200">
                                <i class="fas fa-times mr-2"></i>
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="bg-white rounded-xl shadow-xs overflow-hidden border border-neutral-200">
            @if ($tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" 
                                        @change="toggleAll()" 
                                        :checked="selectAll"
                                        class="rounded border-neutral-300 text-primary-600 focus:ring-primary-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Tiket
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Pengguna
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Subjek & Kategori
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Status & Prioritas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Assigned To
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Dibuat
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-100">
                            @foreach ($tickets as $ticket)
                                <tr class="{{ $loop->odd ? 'bg-neutral-50' : 'bg-white' }} hover:bg-primary-50 transition-colors duration-150">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" 
                                            name="ticket_ids[]" 
                                            value="{{ $ticket->id }}"
                                            x-model="selectedTickets"
                                            class="rounded border-neutral-300 text-primary-600 focus:ring-primary-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-neutral-900">
                                            {{ $ticket->ticket_number }}
                                        </div>
                                        @if ($ticket->isShipmentType() && $ticket->getReferenceNumber())
                                            <div class="text-xs text-neutral-500 mt-1">
                                                <i class="fas fa-shipping-fast mr-1"></i>
                                                {{ $ticket->getReferenceNumber() }}
                                            </div>
                                        @endif
                                        <div class="text-xs text-neutral-400 mt-1">
                                            {{ $ticket->ticket_type === 'shipment' ? 'Pengiriman' : 'Umum' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <img class="h-8 w-8 rounded-full object-cover"
                                                    src="{{ $ticket->user->avatar_url }}"
                                                    alt="{{ $ticket->user->name }}">
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-neutral-900">
                                                    {{ $ticket->user->name }}
                                                </div>
                                                <div class="text-xs text-neutral-500">
                                                    {{ $ticket->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-neutral-900 mb-1">
                                            {{ Str::limit($ticket->subject, 40) }}
                                        </div>
                                        <div class="text-xs text-neutral-600 mb-2">
                                            {{ $ticket->getCategoryLabel() }}
                                        </div>
                                        @if ($ticket->responses->count() > 0)
                                            <div class="text-xs text-neutral-500">
                                                <i class="fas fa-comments mr-1"></i>
                                                {{ $ticket->responses->count() }} respons
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="mb-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->getStatusBadgeClass() }}">
                                                {{ $ticket->getStatusLabel() }}
                                            </span>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->getPriorityBadgeClass() }}">
                                            {{ $ticket->getPriorityLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($ticket->assignedAdmin)
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-6 w-6">
                                                    <img class="h-6 w-6 rounded-full object-cover"
                                                        src="{{ $ticket->assignedAdmin->avatar_url }}"
                                                        alt="{{ $ticket->assignedAdmin->name }}">
                                                </div>
                                                <div class="ml-2">
                                                    <div class="text-xs font-medium text-neutral-700">
                                                        {{ $ticket->assignedAdmin->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-neutral-400 italic">Belum di-assign</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                                        {{ $ticket->created_at->format('d M Y H:i') }}
                                        <div class="text-xs text-neutral-400 mt-1">
                                            {{ $ticket->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.support.show', $ticket->id) }}"
                                                class="text-primary-600 hover:text-primary-900 transition" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if (!$ticket->assignedAdmin)
                                                <form action="{{ route('admin.support.assign', $ticket->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="admin_id" value="{{ auth()->id() }}">
                                                    <button type="submit" class="text-blue-600 hover:text-blue-900 transition" title="Assign ke saya">
                                                        <i class="fas fa-user-plus"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if ($ticket->canBeResolved())
                                                <button onclick="quickResolve({{ $ticket->id }})" 
                                                    class="text-green-600 hover:text-green-900 transition" title="Quick Resolve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="bg-neutral-50 px-4 py-3 border-t border-neutral-200 sm:px-6 rounded-b-xl">
                </div>
            @else
                <div class="text-center py-12 bg-gradient-to-br from-neutral-50 to-white rounded-b-xl">
                    <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-white text-neutral-400 mb-4 border border-neutral-200">
                        <i class="fas fa-ticket-alt text-xl"></i>
                    </div>
                    <h3 class="text-sm font-medium text-neutral-700">Tidak ada tiket support</h3>
                    <p class="mt-1 text-sm text-neutral-500 max-w-md mx-auto">
                        @if (array_filter($filters))
                            Tidak ada tiket yang cocok dengan filter yang dipilih.
                        @else
                            Belum ada tiket support yang masuk.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Bulk Action Modal -->
        <div x-show="showBulkModal" x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" 
             @click.away="showBulkModal = false" x-on:keydown.escape.window="showBulkModal = false" x-cloak>
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full" @click.stop>
                <form action="{{ route('admin.support.bulk-action') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ticket_ids" :value="JSON.stringify(selectedTickets)">
                    
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-neutral-900 mb-4">Bulk Action</h3>
                        <p class="text-sm text-neutral-600 mb-4">
                            <span x-text="selectedTickets.length"></span> tiket dipilih
                        </p>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-2">Pilih Aksi</label>
                                <select name="action" x-model="bulkAction" required class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="">-- Pilih Aksi --</option>
                                    <option value="assign">Assign ke Admin</option>
                                    <option value="change_status">Ubah Status</option>
                                    <option value="close">Tutup Tiket</option>
                                </select>
                            </div>
                            
                            <!-- Admin Selection (only show when assign is selected) -->
                            <div x-show="bulkAction === 'assign'">
                                <label class="block text-sm font-medium text-neutral-700 mb-2">Pilih Admin</label>
                                <select name="admin_id" x-model="bulkAdminId" class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="">-- Pilih Admin --</option>
                                    @foreach ($adminUsers as $admin)
                                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Status Selection (only show when change_status is selected) -->
                            <div x-show="bulkAction === 'change_status'">
                                <label class="block text-sm font-medium text-neutral-700 mb-2">Pilih Status</label>
                                <select name="status" x-model="bulkStatus" class="w-full border border-neutral-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="open">Terbuka</option>
                                    <option value="in_progress">Dalam Proses</option>
                                    <option value="waiting_user">Menunggu User</option>
                                    <option value="resolved">Diselesaikan</option>
                                    <option value="closed">Ditutup</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-neutral-50 px-6 py-3 flex justify-end gap-3 rounded-b-lg">
                        <button type="button" @click="showBulkModal = false" class="px-4 py-2 bg-white text-neutral-800 rounded-lg border hover:bg-neutral-100">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Jalankan Aksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function quickResolve(ticketId) {
        if (confirm('Apakah Anda yakin ingin menyelesaikan tiket ini?')) {
            const resolution = prompt('Masukkan resolusi singkat:');
            if (resolution) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/support/${ticketId}/resolve`;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="resolution" value="${resolution}">
                `;
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
    </script>
</x-layouts.plain-app>