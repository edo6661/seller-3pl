<?php

// Model: ManualTopUpRequest
namespace App\Models;

use App\Enums\ManualTopUpStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ManualTopUpRequest extends Model
{
    protected $fillable = [
        'user_id',
        'request_code',
        'amount',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'qr_code_url',
        'status',
        'payment_proof_path',
        'admin_notes',
        'requested_at',
        'approved_at',
        'rejected_at'
    ];

    protected $casts = [
        'status' => ManualTopUpStatus::class,
        'amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        if ($this->payment_proof_path && Storage::disk('r2')->exists($this->payment_proof_path)) {
            return Storage::disk('r2')->temporaryUrl($this->payment_proof_path, now()->addMinutes(15));
        }
        return null;
    }
}