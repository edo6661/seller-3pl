<x-layouts.plain-app>
    <x-slot:title>Profil Saya</x-slot:title>

    <div class="bg-gray-50 py-12">
        <div class="container mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            
            {{-- Notifikasi Sukses atau Error --}}
            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-100 p-4 text-sm text-green-700" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-lg bg-red-100 p-4 text-sm text-red-700" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-lg bg-white shadow-md">
                <div class="p-6 md:flex md:items-center md:justify-between">
                    <div class="flex-1 md:flex md:items-center">
                        {{-- Avatar Pengguna --}}
                        <div class="flex-shrink-0">
                            <img class="h-24 w-24 rounded-full object-cover" 
                                 src="{{ $profileData['user']->avatar ? asset('storage/' . $profileData['user']->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($profileData['user']->name) . '&color=7F9CF5&background=EBF4FF' }}" 
                                 alt="Avatar {{ $profileData['user']->name }}">
                        </div>
                        <div class="mt-4 md:mt-0 md:ml-6">
                            {{-- Nama dan Role --}}
                            <h1 class="text-2xl font-bold text-gray-900">{{ $profileData['user']->name }}</h1>
                            <p class="mt-1 text-sm font-medium text-gray-500">{{ $profileData['user']->getRoleLabelAttribute() }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex-shrink-0 md:mt-0 md:ml-4">
                        {{-- Tombol Edit Profil --}}
                        <a href="{{ route('profile.edit', ['id' => $profileData['user']->id]) }}" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Edit Profil
                        </a>
                    </div>
                </div>

                {{-- Progress Bar Kelengkapan Profil --}}
                <div class="border-t border-gray-200 px-6 py-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Kelengkapan Profil</h3>
                    <div class="mt-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600">
                                @if ($completionPercentage < 100)
                                    Lengkapi profil Anda untuk mengoptimalkan pengalaman.
                                @else
                                    Profil Anda sudah lengkap!
                                @endif
                            </p>
                            <span class="text-sm font-semibold text-indigo-600">{{ $completionPercentage }}%</span>
                        </div>
                        <div class="mt-2 w-full rounded-full bg-gray-200">
                            <div class="rounded-full bg-indigo-600 p-1 text-center text-xs font-medium leading-none text-indigo-100" style="width: {{ $completionPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Detail Informasi --}}
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        {{-- Informasi Akun Dasar --}}
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $profileData['user_fields']['name'] }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Alamat Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $profileData['user_fields']['email'] }}</dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Nomor Telepon</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $profileData['user_fields']['phone'] ?? '-' }}</dd>
                        </div>

                        {{-- Informasi Tambahan untuk Seller --}}
                        @if ($profileData['user']->isSeller())
                            <div class="py-3 sm:py-5 sm:px-6">
                                <h3 class="text-md font-semibold text-gray-800">Informasi Penjual</h3>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Nama Bisnis</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $profileData['seller_profile']->display_name ?? '-' }}</dd>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $profileData['seller_profile'] ? $profileData['seller_profile']->getFullAddressAttribute() : '-' }}</dd>
                            </div>
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Koordinat Peta</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                    @if ($profileData['seller_profile'] && $profileData['seller_profile']->hasCoordinates())
                                        Lat: {{ $profileData['seller_profile']->latitude }}, Long: {{ $profileData['seller_profile']->longitude }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-layouts.plain-app>