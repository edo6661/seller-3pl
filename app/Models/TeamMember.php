<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TeamMember extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'seller_id',
        'user_id', 
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'permissions',
        'is_active',
        'invited_at',
        'accepted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
        'is_active' => 'boolean',
        'invited_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->is_active) {
            return false;
        }
        return in_array($permission, $this->permissions ?? []);
    }

    public static function getAvailablePermissions(): array
    {
        return [
            'products.view' => 'Lihat Produk',
            'products.create' => 'Tambah Produk', 
            'products.edit' => 'Edit Produk',
            'products.delete' => 'Hapus Produk',
            'wallet.view' => 'Lihat Wallet',
            'wallet.transaction' => 'Transaksi Wallet',
            'pickup.view' => 'Lihat Pickup Request',
            'pickup.create' => 'Buat Pickup Request',
            'pickup.manage' => 'Kelola Pickup Request',
            'addresses.view' => 'Lihat Alamat',
            'addresses.create' => 'Tambah Alamat',
            'addresses.edit' => 'Edit Alamat',
            'addresses.delete' => 'Hapus Alamat',
            'profile.view' => 'Lihat Profile Toko',
            'profile.edit' => 'Edit Profile Toko',
            'team.view' => 'Lihat Anggota Tim',
            'team.create' => 'Undang Anggota Tim',
            'team.edit' => 'Edit Anggota Tim',
            'team.delete' => 'Hapus Anggota Tim',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopePendingInvitation($query)
    {
        return $query->whereNull('accepted_at')->whereNotNull('invited_at');
    }
}