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

class WithdrawRequestStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public WithdrawRequest $withdrawRequest;
    public WalletTransaction $transaction;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(
        User $user, 
        WithdrawRequest $withdrawRequest, 
        WalletTransaction $transaction,
        string $oldStatus,
        string $newStatus
    ) {
        $this->user = $user;
        $this->withdrawRequest = $withdrawRequest;
        $this->transaction = $transaction;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusLabel = match($this->newStatus) {
            'processing' => 'Sedang Diproses',
            'completed' => 'Berhasil Diproses',
            'failed' => 'Gagal Diproses',
            'cancelled' => 'Dibatalkan',
            default => 'Status Berubah',
        };

        return new Envelope(
            subject: 'Update Penarikan Saldo - ' . $statusLabel . ' - ' . $this->withdrawRequest->withdrawal_code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.withdraw-request-status-changed',
            with: [
                'user' => $this->user,
                'withdrawRequest' => $this->withdrawRequest,
                'transaction' => $this->transaction,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
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
