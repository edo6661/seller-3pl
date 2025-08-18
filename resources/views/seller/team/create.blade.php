<x-layouts.plain-app>
    <x-slot name="title">Undang Anggota Tim</x-slot>
    
    <div class="bg-gray-50 py-12">
        <div class="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <form action="{{ route('seller.team.store') }}" method="POST" x-data="teamForm()">
                @csrf
                
                <div class="overflow-hidden rounded-lg bg-white shadow-md">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Undang Anggota Tim</h3>
                        <p class="mt-1 max-w-5xl text-sm text-gray-500">
                            Undang orang untuk bergabung dan mengelola toko bersama Anda.
                        </p>
                    </div>
                    
                    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            
                            {{-- Nama --}}
                            <div class="sm:col-span-6">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" id="name" 
                                       value="{{ old('name') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                       required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            {{-- Email --}}
                            <div class="sm:col-span-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" 
                                       value="{{ old('email') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                       required>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            {{-- Phone --}}
                            <div class="sm:col-span-2">
                                <label for="phone" class="block text-sm font-medium text-gray-700">Telepon</label>
                                <input type="text" name="phone" id="phone" 
                                       value="{{ old('phone') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    {{-- Permissions Section --}}
                    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-4">Hak Akses</h4>
                        <p class="text-sm text-gray-600 mb-4">Pilih fitur yang dapat diakses oleh anggota tim ini:</p>
                        
                        @error('permissions')
                            <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <div class="space-y-4">
                            {{-- Products Permissions --}}
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h5 class="font-medium text-blue-900 mb-3 flex items-center">
                                    <i class="fas fa-box mr-2"></i>
                                    Manajemen Produk
                                </h5>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach(['products.view', 'products.create', 'products.edit', 'products.delete'] as $permission)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission }}" 
                                                   {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">{{ $availablePermissions[$permission] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            {{-- Wallet Permissions --}}
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h5 class="font-medium text-green-900 mb-3 flex items-center">
                                    <i class="fas fa-wallet mr-2"></i>
                                    Manajemen Wallet
                                </h5>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach(['wallet.view', 'wallet.transaction'] as $permission)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission }}" 
                                                   {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">{{ $availablePermissions[$permission] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            {{-- Pickup Permissions --}}
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <h5 class="font-medium text-yellow-900 mb-3 flex items-center">
                                    <i class="fas fa-truck mr-2"></i>
                                    Pickup Request
                                </h5>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach(['pickup.view', 'pickup.create', 'pickup.manage'] as $permission)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission }}" 
                                                   {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">{{ $availablePermissions[$permission] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            {{-- Addresses & Profile Permissions --}}
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h5 class="font-medium text-purple-900 mb-3 flex items-center">
                                    <i class="fas fa-cog mr-2"></i>
                                    Alamat & Profil
                                </h5>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach(['addresses.view', 'addresses.manage', 'profile.view', 'profile.edit'] as $permission)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission }}" 
                                                   {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">{{ $availablePermissions[$permission] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        {{-- Quick Select Options --}}
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h6 class="font-medium text-gray-700 mb-3">Pilihan Cepat:</h6>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="selectAllPermissions()" 
                                        class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-md text-sm hover:bg-indigo-200 transition">
                                    Pilih Semua
                                </button>
                                <button type="button" @click="selectBasicPermissions()" 
                                        class="px-3 py-1 bg-green-100 text-green-700 rounded-md text-sm hover:bg-green-200 transition">
                                    Hak Akses Dasar
                                </button>
                                <button type="button" @click="selectAdvancedPermissions()" 
                                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded-md text-sm hover:bg-blue-200 transition">
                                    Hak Akses Lengkap
                                </button>
                                <button type="button" @click="clearAllPermissions()" 
                                        class="px-3 py-1 bg-red-100 text-red-700 rounded-md text-sm hover:bg-red-200 transition">
                                    Hapus Semua
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Action Buttons --}}
                <div class="mt-8 flex justify-end gap-x-3">
                    <a href="{{ route('seller.team.index') }}" 
                       class="rounded-md bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Kirim Undangan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function teamForm() {
            return {
                selectAllPermissions() {
                    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
                    checkboxes.forEach(checkbox => checkbox.checked = true);
                },
                
                selectBasicPermissions() {
                    this.clearAllPermissions();
                    const basicPermissions = ['products.view', 'pickup.view', 'addresses.view', 'profile.view'];
                    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
                    checkboxes.forEach(checkbox => {
                        if (basicPermissions.includes(checkbox.value)) {
                            checkbox.checked = true;
                        }
                    });
                },
                
                selectAdvancedPermissions() {
                    this.clearAllPermissions();
                    const advancedPermissions = [
                        'products.view', 'products.create', 'products.edit',
                        'wallet.view', 'pickup.view', 'pickup.create', 'pickup.manage',
                        'addresses.view', 'addresses.manage', 'profile.view'
                    ];
                    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
                    checkboxes.forEach(checkbox => {
                        if (advancedPermissions.includes(checkbox.value)) {
                            checkbox.checked = true;
                        }
                    });
                },
                
                clearAllPermissions() {
                    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
                    checkboxes.forEach(checkbox => checkbox.checked = false);
                }
            }
        }
    </script>
</x-layouts.plain-app>