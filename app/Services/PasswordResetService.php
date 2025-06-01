<?php

namespace App\Services;
use App\Events\PasswordResetRequested;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetService
{
    public function sendResetLink(string $email): bool
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email tidak terdaftar dalam sistem.'],
            ]);
        }

        // Delete existing tokens for this email
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        // Generate new token
        $token = Str::random(64);
        
        // Store token in database
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);

        // Dispatch event to send email
        event(new PasswordResetRequested($user, $token));

        return true;
    }

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

        // Check if token matches
        if (!Hash::check($token, $resetRecord->token)) {
            throw ValidationException::withMessages([
                'token' => ['Token reset password tidak valid.'],
            ]);
        }

        // Check if token is not expired (1 hour)
        $tokenAge = Carbon::parse($resetRecord->created_at)->diffInMinutes(Carbon::now());
        if ($tokenAge > 60) {
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();
                
            throw ValidationException::withMessages([
                'token' => ['Token reset password sudah kedaluwarsa.'],
            ]);
        }

        // Update user password
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User tidak ditemukan.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($password)
        ]);

        // Delete the token
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        return true;
    }
}
