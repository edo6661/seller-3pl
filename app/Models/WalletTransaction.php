<?php

namespace App\Models;

use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'admin_fee',
        'balance_before',
        'balance_after',
        'description',
        'reference_id',
        'status',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'qr_code_url',
        'payment_proof_path',
        'account_number',
        'account_name',
        'requested_at',
        'processed_at',
        'completed_at',
        'approved_at',
        'rejected_at',
        'admin_notes'
    ];

    protected $casts = [
        'type' => WalletTransactionType::class,
        'status' => WalletTransactionStatus::class,
        'amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
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

    public function getFormattedAdminFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->admin_fee, 0, ',', '.');
    }

    public function getNetAmountAttribute(): float
    {
        return $this->amount - $this->admin_fee;
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->net_amount, 0, ',', '.');
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

    public function getPaymentProofUrlAttribute(): ?string
    {
        if ($this->payment_proof_path && Storage::disk('r2')->exists($this->payment_proof_path)) {
            return Storage::disk('r2')->temporaryUrl($this->payment_proof_path, now()->addMinutes(15));
        }
        return null;
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

    public function isCompleted(): bool
    {
        return in_array($this->status, [WalletTransactionStatus::SUCCESS, WalletTransactionStatus::FAILED, WalletTransactionStatus::CANCELLED]);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', WalletTransactionStatus::PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', WalletTransactionStatus::SUCCESS);
    }

    public function scopeTopup($query)
    {
        return $query->where('type', WalletTransactionType::TOPUP);
    }

    public function scopeWithdraw($query)
    {
        return $query->where('type', WalletTransactionType::WITHDRAW);
    }

    public function scopeWaitingApproval($query)
    {
        return $query->where('type', WalletTransactionType::TOPUP)
                    ->where('status', 'pending')
                    ->whereNotNull('payment_proof_path');
    }

    public function scopeWaitingPayment($query)
    {
        return $query->where('type', WalletTransactionType::TOPUP)
                    ->where('status', 'pending')
                    ->whereNotNull('bank_name')
                    ->whereNull('payment_proof_path');
    }
}