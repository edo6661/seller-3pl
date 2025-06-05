<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Midtrans\Config;

class MidtransServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
    }

    /**
     */
    public function boot(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key'); 
        Config::$isProduction = config('midtrans.is_production'); 
        Config::$is3ds = config('midtrans.is_3ds');
        Config::$isSanitized = config('midtrans.is_sanitized');
    }
}
