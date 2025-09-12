<?php
namespace App\Providers;

use App\Events\MessageSent;
use App\Events\NewChatNotification;
use App\Events\PasswordResetRequested;
use App\Events\PaymentStatusChanged;
use App\Events\PickupRequestCreated;
use App\Events\PickupRequestStatusUpdated;
use App\Events\SupportTicketCreated;
use App\Events\SupportTicketReplied;
use App\Events\TopUpTransactionCreated;
use App\Events\TransactionStatusChanged;
use App\Events\UserRegistered;
use App\Events\UserStoppedTyping;
use App\Events\UserTyping;
use App\Events\WithdrawRequestCreated;
use App\Events\WithdrawRequestStatusChanged;
use App\Listeners\MessageSentListener;
use App\Listeners\NewChatNotificationListener;
use App\Listeners\PickupRequestCreatedListener;
use App\Listeners\PickupRequestStatusUpdatedListener;
use App\Listeners\SendEmailVerificationNotification;
use App\Listeners\SendPasswordResetNotification;
use App\Listeners\SendPaymentNotification;
use App\Listeners\SendWithdrawRequestCreatedNotification;
use App\Listeners\SendWithdrawRequestStatusChangedNotification;
use App\Listeners\SupportTicketCreatedListener;
use App\Listeners\SupportTicketRepliedListener;
use App\Listeners\TopUpTransactionCreatedListener;
use App\Listeners\TransactionStatusChangedListener;
use App\Listeners\WithdrawRequestCreatedListener;
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
        PickupRequestCreated::class => [
            PickupRequestCreatedListener::class,
        ],
        
        PickupRequestStatusUpdated::class => [
            PickupRequestStatusUpdatedListener::class,
        ],
        
        NewChatNotification::class => [
            NewChatNotificationListener::class,
        ],
        MessageSent::class => [
            MessageSentListener::class,
        ],
        SupportTicketCreated::class => [
            SupportTicketCreatedListener::class,
        ],
        SupportTicketReplied::class => [
            SupportTicketRepliedListener::class,
        ],
        TopUpTransactionCreated::class => [
            TopUpTransactionCreatedListener::class,
        ],
        WithdrawRequestCreated::class => [
            WithdrawRequestCreatedListener::class,
        ],
        TransactionStatusChanged::class => [
            TransactionStatusChangedListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
