<x-layouts.plain-app>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white rounded-lg shadow-md p-8">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                    <p class="text-gray-600">Silakan masuk ke akun Anda</p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6" id="errorAlert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <ul class="text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="ml-auto pl-3">
                                <button type="button" class="text-red-400 hover:text-red-600" onclick="closeAlert('errorAlert')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6" id="successAlert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button type="button" class="text-green-400 hover:text-green-600" onclick="closeAlert('successAlert')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6" id="sessionErrorAlert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button type="button" class="text-red-400 hover:text-red-600" onclick="closeAlert('sessionErrorAlert')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('guest.auth.login.submit') }}" method="POST" id="loginForm" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" 
                                   class="block w-full pl-10 pr-3 py-3 border @error('email') border-red-300 @else border-gray-300 @enderror rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Masukkan email Anda"
                                   required>
                        </div>
                        @error('email')
                            <div class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" 
                                   class="block w-full pl-10 pr-10 py-3 border @error('password') border-red-300 @else border-gray-300 @enderror rounded-md placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Masukkan password Anda"
                                   required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <div class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                               type="checkbox" 
                               id="remember" 
                               name="remember" 
                               value="1"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="ml-2 block text-sm text-gray-600" for="remember">
                            Ingat saya
                        </label>    
                    </div>

                    <button type="submit" 
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200" 
                            id="loginBtn">
                        <span class="btn-text flex items-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Masuk
                        </span>
                        <span class="spinner hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading...
                        </span>
                    </button>

                    <div class="text-center space-y-2">
                        <p class="text-sm">
                            <a href="{{ route('guest.auth.forgot-password') }}" class="text-blue-600 hover:text-blue-500 hover:underline">
                                Lupa password?
                            </a>
                        </p>
                        <p class="text-sm text-gray-600">
                            Belum punya akun? 
                            <a href="{{ route('guest.auth.register') }}" class="text-blue-600 hover:text-blue-500 font-semibold hover:underline">
                                Daftar sekarang
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Close Alert Function
        function closeAlert(alertId) {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.remove();
            }
        }

        // Form Submission with Loading State
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('loginBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const spinner = submitBtn.querySelector('.spinner');
            
            // Show loading state
            btnText.classList.add('hidden');
            spinner.classList.remove('hidden');
            submitBtn.disabled = true;
            
            // If form validation fails, restore button state after a short delay
            setTimeout(function() {
                if (document.querySelector('.border-red-300')) {
                    btnText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                    submitBtn.disabled = false;
                }
            }, 100);
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[id$="Alert"]');
            alerts.forEach(function(alert) {
                if (alert) {
                    alert.remove();
                }
            });
        }, 5000);

        // Focus first input on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
    </script>
</x-layouts.plain-app>