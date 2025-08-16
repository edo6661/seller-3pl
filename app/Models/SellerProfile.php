<?php

namespace App\Models;


use App\Enums\SellerVerificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'address',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'ktp_image_path',         
        'passbook_image_path',    
        'verification_status',    
        'verification_notes',     

        'is_profile_complete'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_profile_complete' => 'boolean',
        'verification_status' => SellerVerificationStatus::class, 

    ];

    /**
     * Get the user that owns the seller profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full address attribute.
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->province} {$this->postal_code}";
    }

    /**
     * Check if the profile has coordinates.
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get the business name or fallback to user name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->business_name ?: ($this->user ? $this->user->name : 'Tidak ada nama');
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();
        
        static::saving(function ($profile) {
            // Auto-set is_profile_complete based on required fields
            $profile->is_profile_complete = !empty($profile->address) 
                && !empty($profile->city) 
                && !empty($profile->province) 
                && !empty($profile->postal_code);
        });
    }

    /**
     * Scope untuk profile yang lengkap
     */
    public function scopeComplete($query)
    {
        return $query->where('is_profile_complete', true);
    }

    /**
     * Scope untuk profile yang belum lengkap
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_profile_complete', false);
    }

    /**
     * Scope untuk profile yang memiliki koordinat
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }
     public function isVerified(): bool
    {
        return $this->verification_status === SellerVerificationStatus::VERIFIED;
    }

    /**
     * Accessor untuk mendapatkan URL gambar KTP.
     */
    public function getKtpImageUrlAttribute(): ?string
    {
        if ($this->ktp_image_path && Storage::disk('r2')->exists($this->ktp_image_path)) {
            return Storage::disk('r2')->temporaryUrl($this->ktp_image_path, now()->addMinutes(15));
        }
        return null;
    }

    /**
     * Accessor untuk mendapatkan URL gambar buku tabungan.
     */
    public function getPassbookImageUrlAttribute(): ?string
    {
        if ($this->passbook_image_path && Storage::disk('r2')->exists($this->passbook_image_path)) {
            return Storage::disk('r2')->temporaryUrl($this->passbook_image_path, now()->addMinutes(15));
        }
        return null;
    }
}