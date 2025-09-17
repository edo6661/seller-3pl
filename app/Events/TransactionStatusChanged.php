<?php
namespace App\Events;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class TransactionStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(
        public User $user,
        public WalletTransaction $transaction,
        public string $oldStatus,
        public string $newStatus,
        public string $action 
    ) {}
    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->user->id)
        ];
    }
    public function broadcastWith(): array
    {
        return [
            'id' => $this->transaction->id,
            'reference_id' => $this->transaction->reference_id,
            'user_name' => $this->user->name,
            'user_id' => $this->user->id,
            'amount' => $this->transaction->amount,
            'type' => $this->transaction->type->value,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'action' => $this->action,
            'updated_at' => $this->transaction->updated_at->toISOString(),
            'notification' => $this->getNotificationData()
        ];
    }
    public function broadcastAs(): string
    {
        return 'wallet.transaction.status_changed';
    }
    private function getNotificationData(): array
    {
        $isTopUp = $this->transaction->type->value === 'topup';
        $transactionType = $isTopUp ? 'Top Up' : 'Penarikan';
        return match($this->action) {
            'approve' => [
                'type' => 'wallet', 
                'title' => "{$transactionType} Disetujui",
                'message' => $isTopUp 
                    ? "Top Up sebesar {$this->transaction->formatted_amount} telah berhasil disetujui. Saldo Anda telah ditambahkan."
                    : "Penarikan sebesar {$this->transaction->formatted_amount} telah disetujui dan akan segera diproses.",
                'icon' => 'fas fa-check-circle',
                'color' => 'success'
            ],
            'reject' => [
                'type' => 'wallet', 
                'title' => "{$transactionType} Ditolak",
                'message' => $isTopUp
                    ? "Top Up sebesar {$this->transaction->formatted_amount} ditolak. Silakan periksa detail untuk informasi lebih lanjut."
                    : "Penarikan sebesar {$this->transaction->formatted_amount} ditolak. Saldo telah dikembalikan ke akun Anda.",
                'icon' => 'fas fa-times-circle',
                'color' => 'danger'
            ],
            'process' => [
                'type' => 'wallet', 
                'title' => "{$transactionType} Sedang Diproses",
                'message' => $isTopUp
                    ? "Top Up sebesar {$this->transaction->formatted_amount} sedang diproses oleh admin."
                    : "Penarikan sebesar {$this->transaction->formatted_amount} sedang diproses dan akan segera ditransfer.",
                'icon' => 'fas fa-clock',
                'color' => 'info'
            ],
            'cancel' => [
                'type' => 'wallet', 
                'title' => "{$transactionType} Dibatalkan",
                'message' => $isTopUp
                    ? "Top Up sebesar {$this->transaction->formatted_amount} telah dibatalkan."
                    : "Penarikan sebesar {$this->transaction->formatted_amount} telah dibatalkan. Saldo dikembalikan ke akun Anda.",
                'icon' => 'fas fa-ban',
                'color' => 'secondary'
            ],
            default => [
                'type' => 'wallet', 
                'title' => 'Status Transaksi Diperbarui',
                'message' => "Status transaksi {$transactionType} telah diperbarui.",
                'icon' => 'fas fa-info-circle',
                'color' => 'info'
            ]
        };
    }
}