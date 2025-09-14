<?php
// app/Events/TopUpTransactionCreated.php
namespace App\Events;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TopUpTransactionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public WalletTransaction $transaction
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
            new Channel('admin-global'),
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
            'status' => $this->transaction->status->value,
            'created_at' => $this->transaction->created_at->toISOString(),
            'notification' => [
                'type' => 'topup_transaction_created',
                'title' => 'Permintaan Top Up Baru',
                'message' => "Top Up sebesar {$this->transaction->formatted_amount} dari {$this->user->name} membutuhkan perhatian.",
                'icon' => 'fas fa-wallet',
                'color' => 'info'
            ]
        ];
    }

    public function broadcastAs(): string
    {
        return 'wallet.topup.created';
    }
}
