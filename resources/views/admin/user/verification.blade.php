<x-layouts.plain-app>
    <x-slot name="title">Verifikasi Seller</x-slot>
    <div class="container mx-auto px-4 py-6" 
        x-data="{ 
            showImageModal: false, 
            showRejectModal: false, 
            modalImageUrl: '', 
            modalImageTitle: '',
            rejectActionUrl: '' 
        }">
        <div class="bg-gradient-to-r from-primary-50 to-secondary-50 rounded-xl p-6 mb-8 border border-primary-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-neutral-800">Verifikasi Seller</h1>
                    <p class="text-sm text-neutral-600 mt-1">Kelola verifikasi dokumen seller</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="bg-gradient-to-br from-warning-50 to-white rounded-xl shadow-xs p-5 border border-warning-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-warning-100 text-warning-600">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Menunggu Verifikasi</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ $verificationStats['pending'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-success-50 to-white rounded-xl shadow-xs p-5 border border-success-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-success-100 text-success-600">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Terverifikasi</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ $verificationStats['verified'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-error-50 to-white rounded-xl shadow-xs p-5 border border-error-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-error-100 text-error-600">
                        <i class="fas fa-times-circle text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-neutral-600">Ditolak</p>
                        <p class="text-2xl font-semibold text-neutral-800 mt-1">{{ $verificationStats['rejected'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-neutral-50 to-neutral-50 rounded-xl shadow-xs p-5 border border-neutral-200 mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                <div class="w-full lg:w-auto lg:flex-1">
                    <form action="{{ route('admin.sellers.verification') }}" method="GET" class="relative">
                        <input type="hidden" name="verification_status" value="{{ $verificationStatus }}">
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Cari nama seller, email, atau nama bisnis..."
                                class="w-full pl-10 pr-4 py-2.5 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-400 focus:border-transparent text-neutral-700 placeholder-neutral-400 bg-white">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-neutral-400"></i>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="flex gap-3 w-full lg:w-auto">
                    <form action="{{ route('admin.sellers.verification') }}" method="GET" class="inline">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <select name="verification_status" onchange="this.form.submit()"
                            class="border border-neutral-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-primary-400 focus:border-transparent text-neutral-700 text-sm bg-white">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ $verificationStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ $verificationStatus === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="rejected" {{ $verificationStatus === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </form>
                    @if ($search || $verificationStatus)
                        <a href="{{ route('admin.sellers.verification') }}"
                            class="bg-white hover:bg-neutral-50 text-neutral-700 font-medium py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center text-sm border border-neutral-200">
                            <i class="fas fa-times mr-2"></i>Reset
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-xs overflow-hidden border border-neutral-200">
            @if ($sellers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Seller
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Bisnis
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Dokumen Verifikasi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-neutral-600 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-100">
                            @foreach ($sellers as $seller)
                                <tr class="{{ $loop->odd ? 'bg-neutral-50' : 'bg-white' }} hover:bg-primary-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img class="h-10 w-10 rounded-full object-cover" 
                                                 src="{{ $seller->avatar_url }}" 
                                                 alt="{{ $seller->name }}">
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-neutral-800">{{ $seller->name }}</div>
                                                <div class="text-xs text-neutral-500">{{ $seller->email }}</div>
                                                @if($seller->phone)
                                                    <div class="text-xs text-neutral-500">{{ $seller->phone }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-neutral-800">
                                            {{ $seller->sellerProfile->business_name ?? $seller->name }}
                                        </div>
                                        @if($seller->sellerProfile)
                                            <div class="text-xs text-neutral-500 mt-1">
                                                {{ Str::limit($seller->sellerProfile->full_address, 50) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-neutral-600">KTP:</span>
                                                @if($seller->sellerProfile && $seller->sellerProfile->ktp_image_url)
                                                    <button @click="showImageModal = true; modalImageUrl = '{{ $seller->sellerProfile->ktp_image_url }}'; modalImageTitle = 'KTP - {{ $seller->name }}'"
                                                            class="text-xs text-primary-600 hover:text-primary-800 hover:underline flex items-center gap-1">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </button>
                                                @else
                                                    <span class="text-xs text-error-600">Belum upload</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-neutral-600">Rekening:</span>
                                                @if($seller->sellerProfile && $seller->sellerProfile->passbook_image_url)
                                                    <button @click="showImageModal = true; modalImageUrl = '{{ $seller->sellerProfile->passbook_image_url }}'; modalImageTitle = 'Buku Tabungan - {{ $seller->name }}'"
                                                            class="text-xs text-primary-600 hover:text-primary-800 hover:underline flex items-center gap-1">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </button>
                                                @else
                                                    <span class="text-xs text-error-600">Belum upload</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($seller->sellerProfile)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                                bg-{{ $seller->sellerProfile->verification_status->color() }}-100 
                                                text-{{ $seller->sellerProfile->verification_status->color() }}-800">
                                                {{ $seller->sellerProfile->verification_status->label() }}
                                            </span>
                                            @if($seller->sellerProfile->verification_notes)
                                                <div class="text-xs text-neutral-500 mt-1 italic">
                                                    "{{ Str::limit($seller->sellerProfile->verification_notes, 50) }}"
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-xs text-neutral-400">Profil belum lengkap</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($seller->sellerProfile && $seller->sellerProfile->verification_status === \App\Enums\SellerVerificationStatus::PENDING)
                                            <div class="flex items-center justify-center gap-2">
                                                <form action="{{ route('admin.users.approve', $seller) }}" method="POST" 
                                                      onsubmit="return confirm('Anda yakin ingin menyetujui verifikasi seller ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-success-600 hover:text-success-900 text-lg" title="Setujui Verifikasi">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                                <button @click="showRejectModal = true; rejectActionUrl = '{{ route('admin.users.reject', $seller) }}'"
                                                        class="text-error-600 hover:text-error-900 text-lg" title="Tolak Verifikasi">
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
                <div class="bg-neutral-50 px-4 py-3 border-t border-neutral-200">
                    {{ $sellers->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-neutral-100 text-neutral-400 mb-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <h3 class="text-sm font-medium text-neutral-700">Tidak ada seller ditemukan</h3>
                    <p class="mt-1 text-sm text-neutral-500">
                        @if($search)
                            Tidak ada seller yang cocok dengan pencarian "{{ $search }}".
                        @else
                            Belum ada seller yang terdaftar.
                        @endif
                    </p>
                </div>
            @endif
        </div>
        <div x-show="showImageModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" 
             @click.away="showImageModal = false" x-on:keydown.escape.window="showImageModal = false" x-cloak>
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden" @click.stop>
                <div class="p-4 border-b">
                    <h3 class="text-lg font-semibold text-neutral-800" x-text="modalImageTitle"></h3>
                </div>
                <div class="p-4 max-h-[calc(90vh-120px)] overflow-auto">
                    <img :src="modalImageUrl" alt="Dokumen" class="w-full h-auto rounded">
                </div>
                <div class="p-4 border-t text-right">
                    <button @click="showImageModal = false" 
                            class="px-4 py-2 bg-neutral-200 text-neutral-800 rounded-lg hover:bg-neutral-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
        <div x-show="showRejectModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" 
              x-on:keydown.escape.window="showRejectModal = false" x-cloak>
            <div @click.away="showRejectModal = false" class="bg-white rounded-lg shadow-xl max-w-4xl w-full" @click.stop>
                <form :action="rejectActionUrl" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-neutral-900">Tolak Verifikasi</h3>
                        <p class="mt-1 text-sm text-neutral-600">Berikan alasan penolakan. Alasan ini akan dapat dilihat oleh seller.</p>
                        <div class="mt-4">
                            <textarea name="notes" rows="4" required 
                                      class="block w-full rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm" 
                                      placeholder="Contoh: Foto KTP buram, nama di rekening berbeda, dll."></textarea>
                        </div>
                    </div>
                    <div class="bg-neutral-50 px-6 py-3 flex justify-end gap-3 rounded-b-lg">
                        <button type="button" @click="showRejectModal = false" 
                                class="px-4 py-2 bg-white text-neutral-800 rounded-lg border hover:bg-neutral-100">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-error-600 text-white rounded-lg hover:bg-error-700">
                            Tolak Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.plain-app>