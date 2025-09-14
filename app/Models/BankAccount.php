<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BankAccount extends Model
{
    protected $fillable = [
        'bank_name',
        'account_number',
        'account_name',
        'qr_code_path',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function getQrCodeUrlAttribute(): ?string
    {
        if ($this->qr_code_path && Storage::disk('r2')->exists($this->qr_code_path)) {
            return Storage::disk('r2')->temporaryUrl($this->qr_code_path, now()->addMinutes(15));
        }
        return null;
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function getFormattedAccountNumberAttribute(): string
    {
        // Format account number untuk tampilan (bisa disesuaikan per bank)
        $number = $this->account_number;
        $length = strlen($number);
        
        if ($length > 8) {
            return substr($number, 0, 4) . str_repeat('*', $length - 8) . substr($number, -4);
        }
        
        return $number;
    }

    public function hasQrCodeAttribute(): bool
    {
        return !empty($this->qr_code_url);
    }
}
