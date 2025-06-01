<?php

namespace App\Providers;

use App\Events\PasswordResetRequested;
use App\Events\UserRegistered;
use App\Listeners\SendEmailVerificationNotification;
use App\Listeners\SendPasswordResetNotification;
use Illuminate\Support\ServiceProvider;
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            SendEmailVerificationNotification::class,
        ],
        PasswordResetRequested::class => [
            SendPasswordResetNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}