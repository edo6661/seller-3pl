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
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $user = $this->authService->handleProviderCallback($provider, $socialUser);
            Auth::login($user);
            return redirect()
                ->intended($this->authService->redirectAfterLogin())
                ->with('success', 'Selamat datang! Anda berhasil masuk dengan ' . ucfirst($provider) . '.'); 

        } catch (\Exception $e) {
            Log::error('Socialite callback error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('guest.auth.login')
                ->with('error', 'Terjadi kesalahan saat login dengan Google. Silakan coba lagi.');
        }
    }
}