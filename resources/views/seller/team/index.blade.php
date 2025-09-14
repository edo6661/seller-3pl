<x-layouts.plain-app>
    <x-slot name="title">Manajemen Tim</x-slot>
    
    <div class="container mx-auto px-4 py-8" x-data="teamManager()">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-neutral-900 mb-4">Manajemen Tim</h1>
            
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-secondary-100 text-secondary-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600">Total Anggota</p>
                            <p class="text-2xl font-bold text-neutral-900">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-success-100 text-success-600">
                            <i class="fas fa-user-check text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600">Aktif</p>
                            <p class="text-2xl font-bold text-neutral-900">{{ $stats['active'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-error-100 text-error-600">
                            <i class="fas fa-user-times text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600">Nonaktif</p>
                            <p class="text-2xl font-bold text-neutral-900">{{ $stats['inactive'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-warning-100 text-warning-600">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-neutral-600">Pending</p>
                            <p class="text-2xl font-bold text-neutral-900">{{ $stats['pending_invitations'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search & Actions --}}
        <div class="flex flex-col lg:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex-1 w-full">
                <form action="{{ route('seller.team.index') }}" method="GET" class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-neutral-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari anggota tim..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                </form>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('seller.team.create') }}" 
                   class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-5 rounded-lg transition shadow-md hover:shadow-lg flex items-center">
                    <i class="fas fa-user-plus mr-2"></i>
                    Undang Anggota
                </a>
            </div>
        </div>

        {{-- Team Members Table --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            @if ($teamMembers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Anggota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Hak Akses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Terdaftar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-200">
                            @foreach ($teamMembers as $member)
                                <tr class="hover:bg-neutral-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 mr-3">
                                                <div class="h-10 w-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-semibold">
                                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-neutral-900">{{ $member->name }}</div>
                                                <div class="text-sm text-neutral-500">{{ $member->email }}</div>
                                                @if($member->phone)
                                                    <div class="text-xs text-neutral-400">{{ $member->phone }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach(($member->permissions ?? []) as $permission)
                                                @if(isset($availablePermissions[$permission]))
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $availablePermissions[$permission] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $member->is_active ? 'bg-success-100 text-success-800' : 'bg-error-100 text-error-800' }}">
                                                <i class="fas {{ $member->is_active ? 'fa-check-circle mr-1' : 'fa-times-circle mr-1' }}"></i>
                                                {{ $member->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                            
                                            @if($member->accepted_at)
                                                <div class="text-xs text-success-600 flex items-center">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Sudah bergabung
                                                </div>
                                            @else
                                                <div class="text-xs text-warning-600 flex items-center">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Menunggu konfirmasi
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-sm text-neutral-500">
                                        {{ $member->created_at->format('d M Y') }}
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('seller.team.edit', $member->id) }}" 
                                               class="text-primary-600 hover:text-primary-800 transition">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('seller.team.toggle-status', $member->id) }}" 
                                                  method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="text-warning-600 hover:text-warning-800 transition">
                                                    <i class="fas {{ $member->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('seller.team.destroy', $member->id) }}" 
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota tim ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-error-600 hover:text-error-800 transition">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if ($teamMembers->hasPages())
                    <div class="px-6 py-4 border-t border-neutral-200 bg-neutral-50">
                        {{ $teamMembers->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="text-neutral-300 text-6xl mb-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="mt-2 text-lg font-medium text-neutral-900">Belum ada anggota tim</h3>
                    <p class="mt-1 text-sm text-neutral-500">
                        @if ($search)
                            Tidak ada anggota yang cocok dengan pencarian "{{ $search }}".
                        @else
                            Mulai dengan mengundang anggota tim pertama Anda.
                        @endif
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('seller.team.create') }}" 
                           class="inline-flex items-center px-5 py-2.5 border border-transparent shadow-md text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition">
                            <i class="fas fa-user-plus mr-2"></i>
                            Undang Anggota Tim
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function teamManager() {
            return {
                // Add any Alpine.js functionality here if needed
            }
        }
    </script>
</x-layouts.plain-app>