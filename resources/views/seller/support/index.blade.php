<x-layouts.plain-app>
    <x-slot name="title">Tiket Bantuan</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8 p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl text-white">
            <h1 class="text-2xl font-bold mb-2 flex items-center">
                <i class="fas fa-life-ring mr-3"></i>
                Tiket Bantuan
            </h1>
            <p class="opacity-90">Kelola semua tiket bantuan dan keluhan Anda</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-blue-100">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-ticket-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Total Tiket</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-orange-100">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Terbuka</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['open']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-yellow-100">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-hourglass-half text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Dalam Proses</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['in_progress']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-green-100">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Selesai</p>
                        <p class="text-2xl font-bold text-neutral-900">{{ number_format($stats['resolved']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-xl shadow-lg mb-6 border border-neutral-100">
            <div class="p-6">
                <form method="GET" action="{{ route('seller.support.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-neutral-400"></i>
                                </div>
                                <input type="text" name="search" value="{{ $filters['search'] }}"
                                    placeholder="Cari tiket..."
                                    class="w-full pl-10 pr-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <select name="status" class="w-full border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent px-4 py-2.5 transition">
                                <option value="">Semua Status</option>
                                <option value="open" {{ $filters['status'] === 'open' ? 'selected' : '' }}>Terbuka</option>
                                <option value="in_progress" {{ $filters['status'] === 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                                <option value="waiting_user" {{ $filters['status'] === 'waiting_user' ? 'selected' : '' }}>Menunggu Respons</option>
                                <option value="resolved" {{ $filters['status'] === 'resolved' ? 'selected' : '' }}>Selesai</option>
                                <option value="closed" {{ $filters['status'] === 'closed' ? 'selected' : '' }}>Ditutup</option>
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <select name="category" class="w-full border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent px-4 py-2.5 transition">
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
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-md flex items-center justify-center">
                                <i class="fas fa-filter mr-2"></i>
                                Filter
                            </button>
                            <a href="{{ route('seller.support.create') }}" class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-md flex items-center justify-center">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-neutral-100">
            @if ($tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Tiket
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Subjek
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Prioritas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Dibuat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-200">
                            @foreach ($tickets as $ticket)
                                <tr class="hover:bg-neutral-50 transition">
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
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-neutral-900">
                                            {{ Str::limit($ticket->subject, 40) }}
                                        </div>
                                        @if ($ticket->responses->count() > 0)
                                            <div class="text-xs text-neutral-500 mt-1">
                                                <i class="fas fa-comments mr-1"></i>
                                                {{ $ticket->responses->count() }} respons
                                                @if ($ticket->responses->where('is_admin_response', true)->where('is_read', false)->count() > 0)
                                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        baru
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-xs font-medium text-neutral-600">
                                            {{ $ticket->getCategoryLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->getStatusBadgeClass() }}">
                                            {{ $ticket->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->getPriorityBadgeClass() }}">
                                            {{ $ticket->getPriorityLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500">
                                        {{ $ticket->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('seller.support.show', $ticket->id) }}"
                                                class="text-primary-600 hover:text-primary-800 transition" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($ticket->canBeReopened())
                                                <form method="POST" action="{{ route('seller.support.reopen', $ticket->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-800 transition" title="Buka Ulang">
                                                        <i class="fas fa-redo"></i>
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
            @else
                <div class="p-12 text-center">
                    <div class="text-neutral-300 text-5xl mb-4">
                        <i class="fas fa-life-ring"></i>
                    </div>
                    <h3 class="text-lg font-medium text-neutral-900">Belum ada tiket bantuan</h3>
                    <p class="mt-1 text-sm text-neutral-600">Buat tiket bantuan pertama Anda jika mengalami masalah.</p>
                    <div class="mt-6">
                        <a href="{{ route('seller.support.create') }}"
                            class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition shadow-md">
                            <i class="fas fa-plus mr-2"></i>
                            Buat Tiket Bantuan
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.plain-app>