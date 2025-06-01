<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Services\AuthService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthProviderController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }
    public function callback(Request $request, string $provider)
    {
        
        if (!$request->has('code') || $request->has('error')) {
            return redirect()->route('guest.auth.login')
                ->with('error', 'Login dengan Google gagal atau dibatalkan.');
        }

        try {
            
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $user = $this->authService->handleProviderCallback($provider, $socialUser);
            
            Auth::login($user);
            
            return $this->authService->redirectAfterLogin();

        } catch (\Exception $e) {
            Log::error('Socialite callback error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('guest.auth.login')
                ->with('error', 'Terjadi kesalahan saat login dengan Google. Silakan coba lagi.');
        }
    }
}