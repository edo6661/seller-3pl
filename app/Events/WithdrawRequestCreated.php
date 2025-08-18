<?php

namespace App\Events;

use App\Models\User;
use App\Models\WithdrawRequest;
use App\Models\WalletTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WithdrawRequestCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public WithdrawRequest $withdrawRequest;
    public WalletTransaction $transaction;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, WithdrawRequest $withdrawRequest, WalletTransaction $transaction)
    {
        $this->user = $user;
        $this->withdrawRequest = $withdrawRequest;
        $this->transaction = $transaction;
    }
}

class WithdrawRequestStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public WithdrawRequest $withdrawRequest;
    public WalletTransaction $transaction;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(
        User $user, 
        // WithdrawRequest $withdrawRequest, 
        WalletTransaction $transaction,
        string $oldStatus,
        string $newStatus
    ) {
        $this->user = $user;
        // $this->withdrawRequest = $withdrawRequest;
        $this->transaction = $transaction;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}