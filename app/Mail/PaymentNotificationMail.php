<?php
namespace App\Mail;

use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public WalletTransaction $transaction;
    public string $statusMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, WalletTransaction $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
        $this->statusMessage = $this->getStatusMessage();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update Status Pembayaran - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'seller.wallet.payment-notification',
            with: [
                'user' => $this->user,
                'transaction' => $this->transaction,
                'statusMessage' => $this->statusMessage,
                'dashboardUrl' => route('seller.wallet.index'),
                'transactionDetailUrl' => route('seller.wallet.transaction.detail', $this->transaction->id),
            ]
        );
    }

    /**
     * Get status message based on transaction status
     */
    private function getStatusMessage(): string
    {
        return match($this->transaction->status->value) {
            'success' => 'Pembayaran Anda telah berhasil diproses.',
            'failed' => 'Pembayaran Anda gagal diproses.',
            'cancelled' => 'Pembayaran Anda telah dibatalkan.',
            'pending' => 'Pembayaran Anda sedang diproses.',
            default => 'Status pembayaran Anda telah diperbarui.',
        };
    }
}