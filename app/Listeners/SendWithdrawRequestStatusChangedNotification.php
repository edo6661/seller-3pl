<?php

namespace App\Listeners;

use App\Events\WithdrawRequestStatusChanged;
use App\Mail\WithdrawRequestStatusChangedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
class SendWithdrawRequestStatusChangedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(WithdrawRequestStatusChanged $event): void
    {
        try {
            Mail::to($event->user->email)->send(
                new WithdrawRequestStatusChangedMail(
                    $event->user, 
                    $event->withdrawRequest, 
                    $event->transaction,
                    $event->oldStatus,
                    $event->newStatus
                )
            );
        } catch (\Exception $e) {
            Log::error("Error sending withdraw request status changed email", [
                'error' => $e->getMessage(),
                'user_id' => $event->user->id,
                'withdrawal_code' => $event->withdrawRequest->withdrawal_code,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}