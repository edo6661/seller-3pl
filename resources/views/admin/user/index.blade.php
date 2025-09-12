<x-layouts.plain-app>
    <x-slot name="title">Kelola Pengguna</x-slot>
    <div class="container mx-auto px-4 py-6" 
        x-data="{ 
            showImageModal: false, 
            showRejectModal: false, 
            modalImageUrl: '', 
            rejectActionUrl: '' 
        }">
        <div class="bg-gradient-to-r from-primary-50 to-secondary-50 rounded-xl p-6 mb-8 border border-primary-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-neutral-800">Kelola Pengguna</h1>
                    <p class="text-sm text-neutral-600 mt-1">Manajemen data pengguna sistem</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div
                class="bg-gradient-to-br from-secondary-50 to-white rounded-xl shadow-xs p-5 border border-secondary-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-secondary-100 text-secondary-600">
                        <i class="fas fa-users text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Total Pengguna</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div
                class="bg-gradient-to-br from-success-50 to-white rounded-xl shadow-xs p-5 border border-success-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-success-100 text-success-600">
                        <i class="fas fa-store text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Penjual</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ $stats['sellers'] }}</p>
                    </div>
                </div>
            </div>
            <div
                class="bg-gradient-to-br from-primary-50 to-white rounded-xl shadow-xs p-5 border border-primary-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-primary-100 text-primary-600">
                        <i class="fas fa-user-shield text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Admin</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ $stats['admins'] }}</p>
                    </div>
                </div>
            </div>
            <div
                class="bg-gradient-to-br from-warning-50 to-white rounded-xl shadow-xs p-5 border border-warning-100 hover:shadow-sm transition-all duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-warning-100 text-warning-600">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Email Terverifikasi</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ $stats['verified'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="bg-gradient-to-r from-neutral-50 to-neutral-50 rounded-xl shadow-xs p-5 border border-neutral-200 mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                <div class="w-full lg:w-auto lg:flex-1 ">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="relative">
                        <input type="hidden" name="role" value="{{ $role }}">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Cari nama, email, atau telepon..."
                                class="w-full pl-10 pr-4 py-2.5 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-400 focus:border-transparent text-neutral-700 placeholder-neutral-400 bg-white">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-neutral-400"></i>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="inline">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <select name="role" onchange="this.form.submit()"
                            class="border border-neutral-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-400 focus:border-transparent text-neutral-700 text-sm w-full bg-white">
                            <option value="">Semua Role</option>
                            <option value="seller" {{ $role === 'seller' ? 'selected' : '' }}>Penjual</option>
                            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </form>
                    <form action="{{ route('admin.users.index') }}" method="GET" class="inline">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <input type="hidden" name="role" value="{{ $role }}">
                        <select name="status" onchange="this.form.submit()"
                            class="border border-neutral-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-400 focus:border-transparent text-neutral-700 text-sm w-full bg-white">
                            <option value="">Semua Status</option>
                            <option value="verified" {{ $status === 'verified' ? 'selected' : '' }}>Terverifikasi
                            </option>
                            <option value="unverified" {{ $status === 'unverified' ? 'selected' : '' }}>Belum
                                Terverifikasi</option>
                        </select>
                    </form>
                    @if ($search || $role || $status)
                        <a href="{{ route('admin.users.index') }}"
                            class="bg-white hover:bg-neutral-50 text-neutral-700 font-medium py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center text-sm border border-neutral-200">
                            <i class="fas fa-times mr-2"></i>
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-xs overflow-hidden border border-neutral-200">
            @if ($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Pengguna</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Kontak</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Role</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Bergabung</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Aksi
                                </th>

                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-100">
                            @foreach ($users as $user)
                                <tr
                                    class="{{ $loop->odd ? 'bg-neutral-50' : 'bg-white' }} hover:bg-primary-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                        src="{{ $user->avatar_url }}"
                                                        alt="{{ $user->name }}">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-neutral-800">
                                                    {{ $user->name }}</div>
                                                <div class="text-xs text-neutral-500 mt-1">ID: {{ $user->id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-neutral-800">{{ $user->email }}</div>
                                        <div class="text-xs text-neutral-500 mt-1">
                                            @if ($user->phone)
                                                {{ $user->phone }}
                                            @else
                                                <span class="text-neutral-400 italic">Tidak ada telepon</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role->value === 'admin' ? 'bg-primary-100 text-primary-800' : 'bg-secondary-100 text-secondary-800' }}">
                                            {{ $user->role_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{-- MODIFIKASI STATUS VERIFIKASI SELLER --}}
                                        @if ($user->isSeller() && $user->sellerProfile)
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                                    bg-{{ $user->sellerProfile->verification_status->color() }}-100 
                                                    text-{{ $user->sellerProfile->verification_status->color() }}-800">
                                                    {{ $user->sellerProfile->verification_status->label() }}
                                                </span>
                                            </div>
                                            {{-- Tombol lihat dokumen --}}
                                            <div class="flex items-center gap-1.5">
                                                @if($user->sellerProfile->ktp_image_url)
                                                    <button @click="showImageModal = true; modalImageUrl = '{{ $user->sellerProfile->ktp_image_url }}'"
                                                            class="text-xs text-primary-600 hover:underline">Lihat KTP</button>
                                                @endif
                                                @if($user->sellerProfile->passbook_image_url)
                                                    <span class="text-xs text-neutral-400">|</span>
                                                    <button @click="showImageModal = true; modalImageUrl = '{{ $user->sellerProfile->passbook_image_url }}'"
                                                            class="text-xs text-primary-600 hover:underline">Lihat Rekening</button>
                                                @endif
                                            </div>
                                        @else
                                            {{-- Status verifikasi email (jika bukan seller) --}}
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $user->isEmailVerified() ? 'bg-success-100 text-success-800' : 'bg-error-100 text-error-800' }}">
                                                Email {{ $user->isEmailVerified() ? 'Terverifikasi' : 'Belum' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                                        {{ $user->created_at->format('d M Y') }}
                                        <div class="text-xs text-neutral-400 mt-1">
                                            {{ $user->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        @if ($user->isSeller() && $user->sellerProfile && $user->sellerProfile->verification_status === \App\Enums\SellerVerificationStatus::PENDING)
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- Tombol Setujui --}}
                                                <form action="{{ route('admin.users.approve', $user) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyetujui verifikasi seller ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-success-600 hover:text-success-900" title="Setujui">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                                {{-- Tombol Tolak --}}
                                                <button @click="showRejectModal = true; rejectActionUrl = '{{ route('admin.users.reject', $user) }}'"
                                                        class="text-error-600 hover:text-error-900" title="Tolak">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-neutral-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="bg-neutral-50 px-4 py-3 border-t border-neutral-200 sm:px-6 rounded-b-xl">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-12 bg-gradient-to-br from-neutral-50 to-white rounded-b-xl">
                    <div
                        class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-white text-neutral-400 mb-4 border border-neutral-200">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <h3 class="text-sm font-medium text-neutral-700">Tidak ada pengguna</h3>
                    <p class="mt-1 text-sm text-neutral-500 mx-auto">
                        @if ($search)
                            Tidak ada pengguna yang cocok dengan pencarian "{{ $search }}".
                        @elseif($role || $status)
                            Tidak ada pengguna dengan filter yang dipilih.
                        @else
                            Belum ada pengguna yang terdaftar.
                        @endif
                    </p>
                </div>
            @endif
        </div>
        <div x-show="showImageModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.away="showImageModal = false"
             x-on:keydown.escape.window="showImageModal = false"
             x-cloak
             >
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full" @click.stop x-on:click.outside="showImageModal = false">
                <img :src="modalImageUrl" alt="Detail Dokumen" class="w-full max-h-[80vh] rounded-t-lg">
                <div class="p-4 text-right">
                    <button @click="showImageModal = false" class="px-4 py-2 bg-neutral-200 text-neutral-800 rounded-lg hover:bg-neutral-300">Tutup</button>
                </div>
            </div>
        </div>

        {{-- MODAL UNTUK MENOLAK VERIFIKASI --}}
        <div x-show="showRejectModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.away="showRejectModal = false"
             x-on:keydown.escape.window="showRejectModal = false"
             x-cloak
             >
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full" @click.stop x-on:click.outside="showRejectModal = false">
                <form :action="rejectActionUrl" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-neutral-900">Tolak Verifikasi</h3>
                        <p class="mt-1 text-sm text-neutral-600">Berikan alasan penolakan. Alasan ini akan dapat dilihat oleh seller.</p>
                        <div class="mt-4">
                            <textarea name="notes" rows="4" required class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm" placeholder="Contoh: Foto KTP buram, nama di rekening berbeda, dll."></textarea>
                        </div>
                    </div>
                    <div class="bg-neutral-50 px-6 py-3 flex justify-end gap-3 rounded-b-lg">
                        <button type="button" @click="showRejectModal = false" class="px-4 py-2 bg-white text-neutral-800 rounded-lg border hover:bg-neutral-100">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-error-600 text-white rounded-lg hover:bg-error-700">Tolak Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.plain-app>
