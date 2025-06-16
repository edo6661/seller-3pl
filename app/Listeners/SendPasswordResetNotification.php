<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\PasswordResetRequested;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PasswordResetRequested $event): void
    {
        $user = $event->user;
        $token = $event->token;

        $resetUrl = route('guest.auth.reset-password', [
            'token' => $token,
            'email' => $user->email
        ]);
        Log::info('Sending password reset email', [
            'user_id' => $user->id,
            'email' => $user->email,
            'reset_url' => $resetUrl
        ]);

        Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl, $token));
    }
}
