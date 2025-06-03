<?php

namespace App\Providers;

use App\Events\PasswordResetRequested;
use App\Events\PaymentStatusChanged;
use App\Events\UserRegistered;
use App\Listeners\SendEmailVerificationNotification;
use App\Listeners\SendPasswordResetNotification;
use App\Listeners\SendPaymentNotification;
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
        PaymentStatusChanged::class => [
            SendPaymentNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}