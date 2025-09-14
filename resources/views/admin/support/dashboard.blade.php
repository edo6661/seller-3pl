<x-layouts.plain-app>
    <x-slot name="title">Dashboard Support</x-slot>

    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-50 to-secondary-50 rounded-xl p-6 mb-8 border border-primary-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-neutral-800">Dashboard Support</h1>
                    <p class="text-sm text-neutral-600 mt-1">Overview dan statistik tiket support</p>
                </div>
                <div class="flex gap-3 mt-4 md:mt-0">
                    <a href="{{ route('admin.support.index') }}"
                        class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition duration-200 flex items-center text-sm">
                        <i class="fas fa-list mr-2"></i>
                        Semua Tiket
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-xs p-5 border border-blue-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-blue-100 text-blue-600">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Tiket Terbuka</p>
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

            <div class="bg-gradient-to-br from-red-50 to-white rounded-xl shadow-xs p-5 border border-red-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-red-100 text-red-600">
                        <i class="fas fa-exclamation-triangle text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Prioritas Tinggi</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ number_format($stats['high_priority']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-success-50 to-white rounded-xl shadow-xs p-5 border border-success-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-success-100 text-success-600">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Diselesaikan Hari Ini</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ number_format($stats['resolved']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- High Priority Tickets -->
            <div class="bg-white rounded-xl shadow-xs overflow-hidden border border-neutral-200">
                <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                    <h3 class="text-lg font-semibold text-red-900 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                        Tiket Prioritas Tinggi
                    </h3>
                    <p class="text-sm text-red-600 mt-1">Memerlukan perhatian segera</p>
                </div>
                <div class="p-6">
                    @if ($highPriorityTickets->count() > 0)
                        <div class="space-y-4">
                            @foreach ($highPriorityTickets as $ticket)
                                <div class="border border-neutral-200 rounded-lg p-4 hover:bg-neutral-50 transition">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-sm font-semibold text-neutral-900">
                                                    {{ $ticket->ticket_number }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ticket->getPriorityBadgeClass() }}">
                                                    {{ $ticket->getPriorityLabel() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-neutral-700 mb-1">
                                                {{ Str::limit($ticket->subject, 50) }}
                                            </p>
                                            <div class="flex items-center text-xs text-neutral-500">
                                                <span class="mr-3">{{ $ticket->user->name }}</span>
                                                <span>{{ $ticket->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 ml-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ticket->getStatusBadgeClass() }}">
                                                {{ $ticket->getStatusLabel() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-xs text-neutral-500">
                                            {{ $ticket->getCategoryLabel() }}
                                        </div>
                                        <a href="{{ route('admin.support.show', $ticket->id) }}" 
                                            class="text-xs text-primary-600 hover:text-primary-800 font-medium">
                                            Lihat Detail →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.support.index', ['priority' => 'high']) }}" 
                                class="text-sm text-red-600 hover:text-red-800 font-medium">
                                Lihat Semua Tiket Prioritas Tinggi
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-neutral-300 text-3xl mb-2">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="text-sm text-neutral-500">Tidak ada tiket prioritas tinggi saat ini</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Unassigned Tickets -->
            <div class="bg-white rounded-xl shadow-xs overflow-hidden border border-neutral-200">
                <div class="px-6 py-4 bg-orange-50 border-b border-orange-200">
                    <h3 class="text-lg font-semibold text-orange-900 flex items-center gap-2">
                        <i class="fas fa-user-slash text-orange-600"></i>
                        Tiket Belum Di-assign
                    </h3>
                    <p class="text-sm text-orange-600 mt-1">Perlu penugasan admin</p>
                </div>
                <div class="p-6">
                    @if ($unassignedTickets->count() > 0)
                        <div class="space-y-4">
                            @foreach ($unassignedTickets as $ticket)
                                <div class="border border-neutral-200 rounded-lg p-4 hover:bg-neutral-50 transition">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-sm font-semibold text-neutral-900">
                                                    {{ $ticket->ticket_number }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ticket->getPriorityBadgeClass() }}">
                                                    {{ $ticket->getPriorityLabel() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-neutral-700 mb-1">
                                                {{ Str::limit($ticket->subject, 50) }}
                                            </p>
                                            <div class="flex items-center text-xs text-neutral-500">
                                                <span class="mr-3">{{ $ticket->user->name }}</span>
                                                <span>{{ $ticket->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 ml-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $ticket->getStatusBadgeClass() }}">
                                                {{ $ticket->getStatusLabel() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-xs text-neutral-500">
                                            {{ $ticket->getCategoryLabel() }}
                                        </div>
                                        <div class="flex gap-2">
                                            <form action="{{ route('admin.support.assign', $ticket->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="admin_id" value="{{ auth()->id() }}">
                                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                    Assign ke Saya
                                                </button>
                                            </form>
                                            <span class="text-xs text-neutral-300">|</span>
                                            <a href="{{ route('admin.support.show', $ticket->id) }}" 
                                                class="text-xs text-primary-600 hover:text-primary-800 font-medium">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.support.index', ['assigned_to' => '']) }}" 
                                class="text-sm text-orange-600 hover:text-orange-800 font-medium">
                                Lihat Semua Tiket Belum Di-assign
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-neutral-300 text-3xl mb-2">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <p class="text-sm text-neutral-500">Semua tiket sudah di-assign</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Status Distribution -->
            <div class="bg-white rounded-xl shadow-xs overflow-hidden border border-neutral-200">
                <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                    <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-primary-500"></i>
                        Distribusi Status
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm text-neutral-700">Terbuka</span>
                            </div>
                            <span class="text-sm font-semibold text-neutral-900">{{ $stats['open'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                <span class="text-sm text-neutral-700">Dalam Proses</span>
                            </div>
                            <span class="text-sm font-semibold text-neutral-900">{{ $stats['in_progress'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                                <span class="text-sm text-neutral-700">Menunggu User</span>
                            </div>
                            <span class="text-sm font-semibold text-neutral-900">{{ $stats['waiting_user'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm text-neutral-700">Diselesaikan</span>
                            </div>
                            <span class="text-sm font-semibold text-neutral-900">{{ $stats['resolved'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-gray-500 rounded-full mr-3"></div>
                                <span class="text-sm text-neutral-700">Ditutup</span>
                            </div>
                            <span class="text-sm font-semibold text-neutral-900">{{ $stats['closed'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-xs overflow-hidden border border-neutral-200">
                <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                    <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                        <i class="fas fa-bolt text-primary-500"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('admin.support.index', ['status' => 'open']) }}" 
                            class="flex items-center justify-between p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition group">
                            <div class="flex items-center">
                                <i class="fas fa-clock text-blue-500 mr-3"></i>
                                <span class="text-sm text-neutral-700">Tiket Terbuka</span>
                            </div>
                            <i class="fas fa-chevron-right text-neutral-400 group-hover:text-primary-500"></i>
                        </a>
                        
                        <a href="{{ route('admin.support.index', ['priority' => 'urgent']) }}" 
                            class="flex items-center justify-between p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition group">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                                <span class="text-sm text-neutral-700">Mendesak</span>
                            </div>
                            <i class="fas fa-chevron-right text-neutral-400 group-hover:text-primary-500"></i>
                        </a>
                        
                        <a href="{{ route('admin.support.index', ['status' => 'waiting_user']) }}" 
                            class="flex items-center justify-between p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition group">
                            <div class="flex items-center">
                                <i class="fas fa-user-clock text-orange-500 mr-3"></i>
                                <span class="text-sm text-neutral-700">Menunggu User</span>
                            </div>
                            <i class="fas fa-chevron-right text-neutral-400 group-hover:text-primary-500"></i>
                        </a>
                        
                        <a href="{{ route('admin.support.index', ['category' => 'delivery_issue']) }}" 
                            class="flex items-center justify-between p-3 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition group">
                            <div class="flex items-center">
                                <i class="fas fa-shipping-fast text-purple-500 mr-3"></i>
                                <span class="text-sm text-neutral-700">Masalah Pengiriman</span>
                            </div>
                            <i class="fas fa-chevron-right text-neutral-400 group-hover:text-primary-500"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="bg-white rounded-xl shadow-xs overflow-hidden border border-neutral-200">
                <div class="px-6 py-4 bg-neutral-50 border-b border-neutral-200">
                    <h3 class="text-lg font-semibold text-neutral-900 flex items-center gap-2">
                        <i class="fas fa-chart-line text-primary-500"></i>
                        Performance
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-neutral-600">Total Tiket</span>
                                <span class="text-sm font-semibold text-neutral-900">{{ $stats['total'] }}</span>
                            </div>
                            <div class="w-full bg-neutral-200 rounded-full h-2">
                                <div class="bg-primary-500 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-neutral-600">Tingkat Penyelesaian</span>
                                <span class="text-sm font-semibold text-neutral-900">
                                    {{ $stats['total'] > 0 ? round(($stats['resolved'] / $stats['total']) * 100, 1) : 0 }}%
                                </span>
                            </div>
                            <div class="w-full bg-neutral-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" 
                                     style="width: {{ $stats['total'] > 0 ? ($stats['resolved'] / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-neutral-600">Prioritas Tinggi</span>
                                <span class="text-sm font-semibold text-neutral-900">{{ $stats['high_priority'] }}</span>
                            </div>
                            <div class="w-full bg-neutral-200 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" 
                                     style="width: {{ $stats['total'] > 0 ? ($stats['high_priority'] / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.plain-app>