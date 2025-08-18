<x-layouts.plain-app>
    <x-slot name="title">Aktivasi Akun Tim</x-slot>
    
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl w-full space-y-8">
            <div>
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Aktivasi Akun</h2>
                    <p class="text-gray-600">Buat password baru untuk akun tim Anda</p>
                </div>
            </div>
            
            <form action="{{ route('team.accept') }}" method="POST" class="mt-8 space-y-6">
                @csrf
                
                <input type="hidden" name="email" value="{{ $email }}">
                
                <div>
                    <label for="temp_password" class="block text-sm font-medium text-gray-700">Password Sementara</label>
                    <input type="password" name="temp_password" id="temp_password" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                           placeholder="Masukkan password dari email undangan"
                           required>
                    @error('temp_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" name="password" id="password" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                           placeholder="Buat password baru (min. 8 karakter)"
                           required>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                           placeholder="Ulangi password baru"
                           required>
                </div>
                
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-check-circle mr-2"></i>
                    Aktivasi Akun
                </button>
            </form>
        </div>
    </div>
</x-layouts.plain-app>