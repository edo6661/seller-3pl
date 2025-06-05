<?php
namespace App\Mail;

use App\Models\User;
use App\Models\WithdrawRequest;
use App\Models\WalletTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawRequestCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public WithdrawRequest $withdrawRequest;
    public WalletTransaction $transaction;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, WithdrawRequest $withdrawRequest, WalletTransaction $transaction)
    {
        $this->user = $user;
        $this->withdrawRequest = $withdrawRequest;
        $this->transaction = $transaction;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Penarikan Saldo - ' . $this->withdrawRequest->withdrawal_code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.withdraw-request-created',
            with: [
                'user' => $this->user,
                'withdrawRequest' => $this->withdrawRequest,
                'transaction' => $this->transaction,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
