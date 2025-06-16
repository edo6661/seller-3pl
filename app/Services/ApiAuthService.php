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

        if (!$user->isEmailVerified()) {
            $this->emailVerificationService->resendVerificationEmail($user->email);
            throw ValidationException::withMessages([
                'email' => ['Akun Anda belum diverifikasi. Silakan periksa email Anda untuk verifikasi.'],
            ]);
        }

        
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
            
            $token = $user->tokens()->where('id', $tokenId)->first();
            if ($token) {
                $token->delete();
                return true;
            }
            return false;
        } else {
            
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
        
        $user->currentAccessToken()?->delete();
        
        
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
        
        
        
        if ($provider === 'google') {
            
            $userInfo = $this->verifyGoogleToken($accessToken);
            
            if (!$userInfo) {
                throw ValidationException::withMessages([
                    'token' => ['Token tidak valid.'],
                ]);
            }

            
            $user = $this->findOrCreateSocialUser($provider, $userInfo);
            
            
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

        
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        
        $token = Str::random(64);
        
        
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        
        event(new PasswordResetRequested($user, $token));

        return true;
    }

    /**
     * Reset password user menggunakan token
     */
    public function resetPassword(string $token, string $email, string $password): bool
    {
        
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'email' => ['Token reset password tidak valid.'],
            ]);
        }

        
        if (!Hash::check($token, $resetRecord->token)) {
            throw ValidationException::withMessages([
                'token' => ['Token reset password tidak valid.'],
            ]);
        }

        
        $tokenAge = Carbon::parse($resetRecord->created_at)->diffInMinutes(Carbon::now());
        if ($tokenAge > 60) {
            
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();
                
            throw ValidationException::withMessages([
                'token' => ['Token reset password sudah kedaluwarsa. Silakan ajukan reset password baru.'],
            ]);
        }

        
        $user = $this->getUserByEmail($email);
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User tidak ditemukan.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($password)
        ]);

        
        $user->tokens()->delete();

        
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

        
        if (!Hash::check($token, $resetRecord->token)) {
            return false;
        }

        
        $tokenAge = Carbon::parse($resetRecord->created_at)->diffInMinutes(Carbon::now());
        if ($tokenAge > 60) {
            
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
        
        
        
        try {
            $response = file_get_contents("https:
            $tokenInfo = json_decode($response, true);
            
            if (isset($tokenInfo['error'])) {
                return null;
            }
            
            
            $userResponse = file_get_contents("https:
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