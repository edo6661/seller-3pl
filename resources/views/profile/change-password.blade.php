<x-layouts.plain-app>
    <x-slot:title>Ganti Password</x-slot:title>
    
    <div class="bg-gray-50 py-12">
        <div class="container mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Ganti Password</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Pastikan password baru Anda kuat dan aman untuk melindungi akun Anda.
                </p>
            </div>

            {{-- Notifikasi --}}
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

            {{-- Form Ganti Password --}}
            <div class="overflow-hidden rounded-lg bg-white shadow-md">
                <form method="POST" action="{{ route('profile.change-password') }}" class="space-y-6 p-6">
                    @csrf
                    
                    {{-- Password Saat Ini --}}
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">
                            Password Saat Ini
                        </label>
                        <div class="relative mt-1">
                            <input 
                                type="password" 
                                name="current_password" 
                                id="current_password"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('current_password') border-red-300 @enderror"
                                placeholder="Masukkan password saat ini"
                                required
                            >
                            <button 
                                type="button" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3"
                                onclick="togglePassword('current_password')"
                            >
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Baru --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password Baru
                        </label>
                        <div class="relative mt-1">
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password') border-red-300 @enderror"
                                placeholder="Masukkan password baru"
                                required
                                oninput="checkPasswordStrength(this.value)"
                            >
                            <button 
                                type="button" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3"
                                onclick="togglePassword('password')"
                            >
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        
                        {{-- Password Strength Indicator --}}
                        <div id="password-strength" class="mt-2 hidden">
                            <div class="flex items-center space-x-2">
                                <div class="h-2 flex-1 rounded-full bg-gray-200">
                                    <div id="strength-bar" class="h-2 rounded-full transition-all duration-300"></div>
                                </div>
                                <span id="strength-text" class="text-xs font-medium"></span>
                            </div>
                            <ul id="password-requirements" class="mt-2 space-y-1 text-xs text-gray-600">
                                <li id="req-length" class="flex items-center">
                                    <span class="mr-2">❌</span> Minimal 8 karakter
                                </li>
                                <li id="req-upper" class="flex items-center">
                                    <span class="mr-2">❌</span> Minimal 1 huruf besar
                                </li>
                                <li id="req-lower" class="flex items-center">
                                    <span class="mr-2">❌</span> Minimal 1 huruf kecil
                                </li>
                                <li id="req-number" class="flex items-center">
                                    <span class="mr-2">❌</span> Minimal 1 angka
                                </li>
                            </ul>
                        </div>
                        
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Konfirmasi Password Baru
                        </label>
                        <div class="relative mt-1">
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password_confirmation') border-red-300 @enderror"
                                placeholder="Konfirmasi password baru"
                                required
                            >
                            <button 
                                type="button" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3"
                                onclick="togglePassword('password_confirmation')"
                            >
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex items-center justify-between pt-6">
                        <a href="{{ route('profile.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            ← Kembali ke Profil
                        </a>
                        <div class="flex space-x-3">
                            <button 
                                type="button" 
                                onclick="window.history.back()" 
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Ganti Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('svg');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L12 12m-2.122-2.122L7.76 7.76m0 0L5.64 5.64m0 0L12 12m-6.36-6.36L12 12"></path>
                `;
            } else {
                field.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }

        // Check password strength
        function checkPasswordStrength(password) {
            const strengthDiv = document.getElementById('password-strength');
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            
            if (password.length === 0) {
                strengthDiv.classList.add('hidden');
                return;
            }
            
            strengthDiv.classList.remove('hidden');
            
            let score = 0;
            const requirements = {
                length: password.length >= 8,
                upper: /[A-Z]/.test(password),
                lower: /[a-z]/.test(password),
                number: /[0-9]/.test(password)
            };
            
            // Update requirement indicators
            Object.keys(requirements).forEach(req => {
                const element = document.getElementById(`req-${req}`);
                const icon = element.querySelector('span');
                if (requirements[req]) {
                    icon.textContent = '✅';
                    score++;
                } else {
                    icon.textContent = '❌';
                }
            });
            
            // Update strength bar
            const percentage = (score / 4) * 100;
            strengthBar.style.width = percentage + '%';
            
            if (score === 0) {
                strengthBar.className = 'h-2 rounded-full bg-red-500 transition-all duration-300';
                strengthText.textContent = 'Sangat Lemah';
                strengthText.className = 'text-xs font-medium text-red-600';
            } else if (score === 1) {
                strengthBar.className = 'h-2 rounded-full bg-red-400 transition-all duration-300';
                strengthText.textContent = 'Lemah';
                strengthText.className = 'text-xs font-medium text-red-500';
            } else if (score === 2) {
                strengthBar.className = 'h-2 rounded-full bg-yellow-400 transition-all duration-300';
                strengthText.textContent = 'Sedang';
                strengthText.className = 'text-xs font-medium text-yellow-600';
            } else if (score === 3) {
                strengthBar.className = 'h-2 rounded-full bg-blue-400 transition-all duration-300';
                strengthText.textContent = 'Kuat';
                strengthText.className = 'text-xs font-medium text-blue-600';
            } else {
                strengthBar.className = 'h-2 rounded-full bg-green-500 transition-all duration-300';
                strengthText.textContent = 'Sangat Kuat';
                strengthText.className = 'text-xs font-medium text-green-600';
            }
        }
    </script>
</x-layouts.plain-app>