<?php

namespace App\Services;
use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class EmailVerificationService
{
    public function verifyEmail(int $userId, string $hash): bool
    {
        $user = User::find($userId);
        
        if (!$user) {
            throw ValidationException::withMessages([
                'user' => ['User tidak ditemukan.'],
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Email sudah diverifikasi sebelumnya.'],
            ]);
        }

        if (!hash_equals($hash, sha1($user->email))) {
            throw ValidationException::withMessages([
                'hash' => ['Link verifikasi tidak valid.'],
            ]);
        }

        // Verifikasi email
        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();
        
        return true;
    }

    public function resendVerificationEmail(string $email): bool
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email tidak terdaftar dalam sistem.'],
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Email sudah diverifikasi.'],
            ]);
        }

        event(new UserRegistered($user));

        return true;
    }
}