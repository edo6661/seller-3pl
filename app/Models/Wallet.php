<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    /** @use HasFactory<\Database\Factories\WalletFactory> */
    use HasFactory;
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Helpers
    public function getAvailableBalanceAttribute()
    {
        return $this->balance - $this->pending_balance;
    }

    public function hasSufficientBalance($amount)
    {
        return $this->available_balance >= $amount;
    }

    public function addBalance($amount, $description, $type = 'topup', $referenceId = null)
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
            'status' => 'success'
        ]);
    }

    public function deductBalance($amount, $description, $type = 'payment', $referenceId = null)
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
            'status' => 'success'
        ]);
    }

}
