<?php
namespace App\Events;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WithdrawRequestCreated implements ShouldBroadcast
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
            'bank_name' => $this->transaction->bank_name,
            'account_number' => $this->transaction->account_number,
            'type' => $this->transaction->type->value,
            'status' => $this->transaction->status->value,
            'created_at' => $this->transaction->created_at->toISOString(),
            'notification' => [
                'type' => 'withdraw_request_created',
                'title' => 'Permintaan Penarikan Baru',
                'message' => "Permintaan penarikan sebesar {$this->transaction->formatted_amount} dari {$this->user->name} membutuhkan perhatian.",
                'icon' => 'fas fa-money-bill-wave',
                'color' => 'warning'
            ]
        ];
    }

    public function broadcastAs(): string
    {
        return 'wallet.withdraw.created';
    }
}
