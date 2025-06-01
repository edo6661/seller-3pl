<x-layouts.plain-app>
    <h1>
        Form Reset Password
    </h1>
    <form action="{{ route('guest.auth.reset-password.submit') }}" method="POST" id="loginForm">
        @csrf
        <div class="mb-3">
            <input type="hidden" 
                id="email" 
                name="email"
                value="{{ $email }}"> 
            
            @error('email')
                <div class="invalid-feedback d-block">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
            <input type="hidden" 
                id="token" 
                name="token"
                value="{{ $token }}"/> 
            @error('token')
                <div class="invalid-feedback d-block">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
            <label for="password" class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-lock text-muted"></i>
                </span>
                <input type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        placeholder="Masukkan password Anda"
                        required/>
                <span class="input-group-text password-toggle" onclick="togglePassword()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </span>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-lock text-muted"></i>
                </span>
                <input type="password" 
                        class="form-control @error('password_confirmation') is-invalid @enderror" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder="Konfirmasi password Anda"
                        required>
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback d-block">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>
        @if(session('status'))
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-1"></i>
                {{ session('status') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-1"></i>
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-1"></i>
                {{ session('success') }}
            </div>
        @endif

       

        <button type="submit" class="btn btn-login text-white w-100 mb-3" id="loginBtn">
            <span class="btn-text">
                <i class="fas fa-sign-in-alt me-2"></i>
                Masuk
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
</x-layouts.plain-app>