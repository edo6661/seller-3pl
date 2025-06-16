<?php

namespace App\Models;

use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_id',
        'status',
        'snap_token',
        'snap_url',
        'payment_type'
    ];

    protected $casts = [
        'type' => WalletTransactionType::class,
        'status' => WalletTransactionStatus::class,
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        $sign = $this->type->increasesBalance() ? '+' : '-';
        return $sign . 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedBalanceBeforeAttribute(): string
    {
        return 'Rp ' . number_format($this->balance_before, 0, ',', '.');
    }

    public function getFormattedBalanceAfterAttribute(): string
    {
        return 'Rp ' . number_format($this->balance_after, 0, ',', '.');
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->status === WalletTransactionStatus::PENDING;
    }

    public function isSuccess(): bool
    {
        return $this->status === WalletTransactionStatus::SUCCESS;
    }

    public function isFailed(): bool
    {
        return $this->status === WalletTransactionStatus::FAILED;
    }

    public function isCancelled(): bool
    {
        return $this->status === WalletTransactionStatus::CANCELLED;
    }
    public function isTopup(): bool
    {
        return $this->type === WalletTransactionType::TOPUP;
    }
    public function isWithdrawal(): bool
    {
        return $this->type === WalletTransactionType::WITHDRAW;
    }

    public function canBeCancelled(): bool
    {
        return $this->isPending() && in_array($this->type, [
            WalletTransactionType::TOPUP, 
            WalletTransactionType::WITHDRAW
        ]);
    }
}