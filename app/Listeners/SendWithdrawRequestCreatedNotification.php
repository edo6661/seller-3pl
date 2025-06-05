<?php

namespace App\Listeners;

use App\Events\WithdrawRequestCreated;
use App\Events\WithdrawRequestStatusChanged;
use App\Mail\WithdrawRequestCreatedMail;
use App\Mail\WithdrawRequestStatusChangedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWithdrawRequestCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(WithdrawRequestCreated $event): void
    {
        try {
            Mail::to($event->user->email)->send(
                new WithdrawRequestCreatedMail($event->user, $event->withdrawRequest, $event->transaction)
            );
           
        } catch (\Exception $e) {
            Log::error("Error sending withdraw request created email", [
                'error' => $e->getMessage(),
                'user_id' => $event->user->id,
                'withdrawal_code' => $event->withdrawRequest->withdrawal_code,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
