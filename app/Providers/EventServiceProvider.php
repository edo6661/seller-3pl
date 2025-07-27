<?php
namespace App\Providers;

use App\Events\MessageSent;
use App\Events\PasswordResetRequested;
use App\Events\PaymentStatusChanged;
use App\Events\UserRegistered;
use App\Events\WithdrawRequestCreated;
use App\Events\WithdrawRequestStatusChanged;
use App\Listeners\MessageSentListener;
use App\Listeners\SendEmailVerificationNotification;
use App\Listeners\SendPasswordResetNotification;
use App\Listeners\SendPaymentNotification;
use App\Listeners\SendWithdrawRequestCreatedNotification;
use App\Listeners\SendWithdrawRequestStatusChangedNotification;
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
        WithdrawRequestCreated::class => [
            SendWithdrawRequestCreatedNotification::class,
        ],
        WithdrawRequestStatusChanged::class => [
            SendWithdrawRequestStatusChangedNotification::class,
        ],
        MessageSent::class => [
            MessageSentListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
