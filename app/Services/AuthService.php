<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Events\PasswordResetRequested;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(array $credentials, bool $remember = false): bool
    {
        $user = $this->getUserByEmail($credentials['email']);
        
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        if ($user->isEmailVerified() === false) {
            $emailVerificationService = app(EmailVerificationService::class);
            $emailVerificationService->resendVerificationEmail($user->email);
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

    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    public function getUserById(int $id): ?User
    {
        return User::find($id);
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['email'] = strtolower($data['email']);
        
        return User::create($data);
    }

    public function updateUser(int $id, array $data): User
    {
        $user = User::findOrFail($id);
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        if (isset($data['email'])) {
            $data['email'] = strtolower($data['email']);
        }
        
        $user->update($data);
        return $user;
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = User::findOrFail($userId);
        
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password saat ini tidak valid.'],
            ]);
        }
        
        $user->update(['password' => Hash::make($newPassword)]);
        return true;
    }

    public function checkUserRole(string $role): bool
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return false;
        }
        
        return $user->role->value === $role;
    }

    public function redirectAfterLogin(): string
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return route('guest.auth.login');
        }
        
        if ($user->isAdmin()) {
            return route('admin.buyer-ratings.index');
        }
        
        if ($user->isSeller()) {
            return route('guest.home');
        }
        
        return route('guest.home');
    }

    public function getUserStats(int $userId): array
    {
        $user = User::findOrFail($userId);
        
        $stats = [
            'total_products' => $user->products()->count(),
            'active_products' => $user->products()->where('is_active', true)->count(),
            'total_pickup_requests' => $user->pickupRequests()->count(),
            'pending_pickup_requests' => $user->pickupRequests()->where('status', 'pending')->count(),
        ];
        
        if ($user->wallet) {
            $stats['wallet_balance'] = $user->wallet->balance;
        }
        
        return $stats;
    }

    public function deleteUser(int $id): bool
    {
        $user = User::find($id);
        
        if ($user) {
            if ($user->products()->exists() || $user->pickupRequests()->exists()) {
                $user->update(['is_active' => false]);
                return true;
            }
            
            return $user->delete();
        }
        
        return false;
    }

    public function searchUsers(string $search): \Illuminate\Database\Eloquent\Collection
    {
        return User::where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        })
        ->orderBy('name')
        ->get();
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