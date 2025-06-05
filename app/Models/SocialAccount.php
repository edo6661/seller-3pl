<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{

    protected $fillable = [
        'user_id', 'provider', 'provider_id', 
        'provider_token', 'provider_refresh_token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
