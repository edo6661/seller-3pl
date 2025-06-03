<?php
namespace App\Listeners;

use App\Events\PaymentStatusChanged;
use App\Mail\PaymentNotificationMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentStatusChanged $event): void
    {
        try {
            $transaction = $event->transaction;
            $user = $transaction->wallet->user;
            
            if (!$user) {
                Log::error("User not found for transaction", [
                    'transaction_id' => $transaction->id,
                    'order_id' => $transaction->reference_id
                ]);
                return;
            }

            
            Mail::to($user->email)->send(new PaymentNotificationMail($user, $transaction));
            
            Log::info("Payment notification email sent", [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'transaction_id' => $transaction->id,
                'order_id' => $transaction->reference_id,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus
            ]);
        } catch (\Exception $e) {
            Log::error("Error sending payment notification email", [
                'error' => $e->getMessage(),
                'transaction_id' => $event->transaction->id ?? null,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}