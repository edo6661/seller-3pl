<x-layouts.plain-app>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header text-center mb-4">
                <h1 class="auth-title">
                    <i class="fas fa-key text-primary me-2"></i>
                    Lupa Password
                </h1>
                <p class="text-muted">
                    Masukkan email Anda untuk mendapatkan link reset password
                </p>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('guest.auth.forgot-password.submit') }}" method="POST" id="forgotPasswordForm">
                @csrf
                
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope text-muted"></i>
                        </span>
                        <input type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                placeholder="Masukkan email Anda"
                                required>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-login text-white w-100 mb-3" id="loginBtn">
                    <span class="btn-text">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Kirim Link Reset Password
                    </span>
                    <span class="spinner-border spinner-border-sm d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </span>
                </button>

                <div class="text-center">
                    <p class="text-muted mb-2">
                        <a href="{{ route('guest.auth.forgot-password') }}" class="text-decoration-none">
                            Lupa password?
                        </a>
                    </p>
                    <p class="text-muted">
                        Belum punya akun? 
                        <a href="{{ route('guest.auth.register') }}" class="text-decoration-none fw-semibold">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

</x-layouts.plain-app>