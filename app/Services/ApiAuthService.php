<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Events\PasswordResetRequested;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class ApiAuthService
{
    protected EmailVerificationService $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * Login untuk API - menggunakan token-based authentication
     */
    public function login(array $credentials, string $deviceName = 'api-device'): array
    {
        $user = $this->getUserByEmail($credentials['email']);
        
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        // Buat token untuk API authentication
        $token = $user->createToken($deviceName)->plainTextToken;
        
        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    /**
     * Logout untuk API - hapus token yang sedang digunakan
     */
    public function logout(User $user, ?string $tokenId = null): bool
    {
        if ($tokenId) {
            // Logout token tertentu
            $token = $user->tokens()->where('id', $tokenId)->first();
            if ($token) {
                $token->delete();
                return true;
            }
            return false;
        } else {
            // Logout current token (dari request)
            $user->currentAccessToken()?->delete();
            return true;
        }
    }

    /**
     * Logout dari semua device
     */
    public function logoutAll(User $user): int
    {
        return $user->tokens()->delete();
    }

    /**
     * Refresh token - buat token baru dan hapus yang lama
     */
    public function refreshToken(User $user, string $deviceName = 'api-device'): array
    {
        // Hapus token lama
        $user->currentAccessToken()?->delete();
        
        // Buat token baru
        $token = $user->createToken($deviceName)->plainTextToken;
        
        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    /**
     * Get user berdasarkan email
     */
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get user berdasarkan token
     */
    public function getUserByToken(string $token): ?User
    {
        $accessToken = PersonalAccessToken::findToken($token);
        return $accessToken?->tokenable;
    }

    /**
     * Get redirect path untuk API (untuk informasi saja)
     */
    /**
     * Handle social login untuk API
     */
    public function handleProviderCallback(string $provider, object $socialUser, string $deviceName = 'api-device'): array
    {
        $user = DB::transaction(function () use ($provider, $socialUser) {
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
            
            SocialAccount::updateOrCreate(
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

        // Buat token untuk user
        $token = $user->createToken($deviceName)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    /**
     * Register user baru untuk API
     */
    public function createUserWithVerification(array $data, string $deviceName = 'api-device'): array
    {
        $data['password'] = Hash::make($data['password']);
        $data['email'] = strtolower($data['email']);
        
        $user = User::create($data);
        
        event(new UserRegistered(user: $user));

        // Buat token untuk user yang baru register
        $token = $user->createToken($deviceName)->plainTextToken;
        
        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Registrasi berhasil. Silakan verifikasi email Anda.'
        ];
    }

    /**
     * Social login dengan token (untuk mobile apps)
     */
    public function loginWithSocialToken(string $provider, string $accessToken, string $deviceName = 'api-device'): array
    {
        // Implementasi tergantung provider (Google, Facebook, etc.)
        // Contoh untuk Google:
        
        if ($provider === 'google') {
            // Verifikasi token dengan Google API
            $userInfo = $this->verifyGoogleToken($accessToken);
            
            if (!$userInfo) {
                throw ValidationException::withMessages([
                    'token' => ['Token tidak valid.'],
                ]);
            }

            // Cari atau buat user
            $user = $this->findOrCreateSocialUser($provider, $userInfo);
            
            // Buat token
            $token = $user->createToken($deviceName)->plainTextToken;
            
            return [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ];
        }

        throw ValidationException::withMessages([
            'provider' => ['Provider tidak didukung.'],
        ]);
    }

    /**
     * Get semua token user
     */
    public function getUserTokens(User $user): array
    {
        return $user->tokens()->get()->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
            ];
        })->toArray();
    }

    /**
     * Revoke token tertentu
     */
    public function revokeToken(User $user, string $tokenId): bool
    {
        $token = $user->tokens()->where('id', $tokenId)->first();
        
        if ($token) {
            $token->delete();
            return true;
        }
        
        return false;
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

        // Hapus semua token user setelah reset password (untuk keamanan)
        $user->tokens()->delete();

        // Hapus token reset password
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

    /**
     * Verifikasi Google token (contoh implementasi)
     */
    private function verifyGoogleToken(string $token): ?array
    {
        // Implementasi verifikasi Google token
        // Gunakan Google Client Library atau HTTP request ke Google API
        
        try {
            $response = file_get_contents("https://www.googleapis.com/oauth2/v3/tokeninfo?access_token=" . $token);
            $tokenInfo = json_decode($response, true);
            
            if (isset($tokenInfo['error'])) {
                return null;
            }
            
            // Get user info
            $userResponse = file_get_contents("https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $token);
            $userInfo = json_decode($userResponse, true);
            
            return $userInfo;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Cari atau buat user dari social login
     */
    private function findOrCreateSocialUser(string $provider, array $userInfo): User
    {
        return DB::transaction(function () use ($provider, $userInfo) {
            $socialAccount = SocialAccount::where('provider', $provider)
                ->where('provider_id', $userInfo['id'])
                ->first();
            
            if ($socialAccount && $socialAccount->user) {
                return $socialAccount->user;
            }
            
            $user = $this->getUserByEmail($userInfo['email']);
            
            if (!$user) {
                $user = User::create([
                    'name' => $userInfo['name'],
                    'email' => $userInfo['email'],
                    'avatar' => $userInfo['picture'] ?? null,
                    'password' => null,
                    'email_verified_at' => now(),
                    'role' => 'seller',
                ]);
            }
            
            SocialAccount::updateOrCreate(
                [
                    'provider' => $provider,
                    'provider_id' => $userInfo['id'],
                ],
                [
                    'user_id' => $user->id, 
                ]
            );
            
            return $user;
        });
    }
}