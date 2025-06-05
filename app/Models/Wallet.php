<?php

namespace App\Models;

use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'pending_balance'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Helpers
    public function getAvailableBalanceAttribute(): float
    {
        return $this->balance - $this->pending_balance;
    }

    public function hasSufficientBalance(float $amount): bool
    {
        return $this->available_balance >= $amount;
    }

    public function addBalance(float $amount, string $description, WalletTransactionType $type = WalletTransactionType::TOPUP, ?string $referenceId = null): WalletTransaction
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);
        
        return $this->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->fresh()->balance,
            'description' => $description,
            'reference_id' => $referenceId,
            'status' => WalletTransactionStatus::SUCCESS
        ]);
    }

    public function deductBalance(float $amount, string $description, WalletTransactionType $type = WalletTransactionType::PAYMENT, ?string $referenceId = null): WalletTransaction
    {
        if (!$this->hasSufficientBalance($amount)) {
            throw new \Exception('Insufficient balance');
        }

        $balanceBefore = $this->balance;
        $this->decrement('balance', $amount);
        
        return $this->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->fresh()->balance,
            'description' => $description,
            'reference_id' => $referenceId,
            'status' => WalletTransactionStatus::SUCCESS
        ]);
    }

    /**
     * Format balance for display
     */
    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }

    /**
     * Format available balance for display
     */
    public function getFormattedAvailableBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->available_balance, 0, ',', '.');
    }
}