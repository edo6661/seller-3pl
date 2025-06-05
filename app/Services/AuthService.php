<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Events\PasswordResetRequested;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected EmailVerificationService $emailVerificationService;
    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }
    public function login(array $credentials, bool $remember = false): bool
    {
        $user = $this->getUserByEmail($credentials['email']);
        
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        if ($user->isEmailVerified() === false) {

            $this->emailVerificationService->resendVerificationEmail($user->email);
            throw ValidationException::withMessages([
                'email' => ['Akun Anda belum diverifikasi. Silakan periksa email Anda untuk verifikasi.'],
            ]);
        }

        Auth::login($user, $remember);
        
        return true;
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }

    public function redirectAfterLogin(): string
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return route('guest.auth.login');
        }
        
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        }
        
        if ($user->isSeller()) {
            return route('seller.dashboard');
        }
        
        return route('guest.home');
    }


    public function handleProviderCallback(string $provider, object $socialUser): User
    {
        return DB::transaction(function () use ($provider, $socialUser) {
            $socialAccount = SocialAccount::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();
            
            if ($socialAccount && $socialAccount->user) {
                $socialAccount->update([
                    'provider_token' => $socialUser->token,
                    'provider_refresh_token' => $socialUser->refreshToken,
                ]);
                
                return $socialAccount->user;
            }
            
            $user = $this->getUserByEmail($socialUser->getEmail());
            
            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    'password' => null,
                    'email_verified_at' => now(),
                    'role' => 'seller',
                ]);
            }
            
            $socialAccount = SocialAccount::updateOrCreate(
                [
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ],
                [
                    'user_id' => $user->id, 
                    'provider_token' => $socialUser->token,
                    'provider_refresh_token' => $socialUser->refreshToken,
                ]
            );
            
            return $user;
        });
    }

    public function createUserWithVerification(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['email'] = strtolower($data['email']);
        
        $user = User::create($data);
        
        event(new UserRegistered(user: $user));
        
        return $user;
    }

    /**
     * Kirim link reset password ke email user
     */
    public function sendPasswordResetLink(string $email): bool
    {
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email tidak terdaftar dalam sistem.'],
            ]);
        }

        // Hapus token reset password yang sudah ada untuk email ini
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        // Generate token baru
        $token = Str::random(64);
        
        // Simpan token ke database
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        // Dispatch event untuk mengirim email
        event(new PasswordResetRequested($user, $token));

        return true;
    }

    /**
     * Reset password user menggunakan token
     */
    public function resetPassword(string $token, string $email, string $password): bool
    {
        // Cari record reset password
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'email' => ['Token reset password tidak valid.'],
            ]);
        }

        // Verifikasi token
        if (!Hash::check($token, $resetRecord->token)) {
            throw ValidationException::withMessages([
                'token' => ['Token reset password tidak valid.'],
            ]);
        }

        // Cek apakah token sudah kedaluwarsa (60 menit)
        $tokenAge = Carbon::parse($resetRecord->created_at)->diffInMinutes(Carbon::now());
        if ($tokenAge > 60) {
            // Hapus token yang kedaluwarsa
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();
                
            throw ValidationException::withMessages([
                'token' => ['Token reset password sudah kedaluwarsa. Silakan ajukan reset password baru.'],
            ]);
        }

        // Update password user
        $user = $this->getUserByEmail($email);
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User tidak ditemukan.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($password)
        ]);

        // Hapus token setelah berhasil reset password
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        return true;
    }

    /**
     * Verifikasi apakah token reset password masih valid
     */
    public function isValidResetToken(string $token, string $email): bool
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return false;
        }

        // Verifikasi token
        if (!Hash::check($token, $resetRecord->token)) {
            return false;
        }

        // Cek apakah token masih berlaku (60 menit)
        $tokenAge = Carbon::parse($resetRecord->created_at)->diffInMinutes(Carbon::now());
        if ($tokenAge > 60) {
            // Hapus token yang kedaluwarsa
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();
            return false;
        }

        return true;
    }
}