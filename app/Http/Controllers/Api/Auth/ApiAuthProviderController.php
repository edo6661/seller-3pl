<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class ApiAuthProviderController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Redirect to provider untuk OAuth
     * Return URL redirect untuk mobile app
     */
    public function redirect(string $provider): JsonResponse
    {
        try {
            $redirectUrl = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'redirect_url' => $redirectUrl
                ]
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Socialite redirect error: ' . $e->getMessage(), ['exception' => $e]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat redirect ke ' . ucfirst($provider),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle callback dari provider OAuth
     */
    public function callback(Request $request, string $provider): JsonResponse
    {
        try {
            
            
            
            if ($request->has('access_token')) {
                
                $socialUser = Socialite::driver($provider)
                    ->stateless()
                    ->userFromToken($request->access_token);
            } else {
                
                $socialUser = Socialite::driver($provider)->stateless()->user();
            }
            
            $user = $this->authService->handleProviderCallback($provider, $socialUser);
            
            
            $token = $user->createToken('auth-token')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil dengan ' . ucfirst($provider),
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Socialite callback error: ' . $e->getMessage(), ['exception' => $e]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat login dengan ' . ucfirst($provider),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login dengan social token dari mobile app
     * Method khusus untuk mobile app yang sudah punya access token
     */
    public function loginWithToken(Request $request, string $provider): JsonResponse
    {
        $request->validate([
            'access_token' => 'required|string'
        ]);

        try {
            $socialUser = Socialite::driver($provider)
                ->stateless()
                ->userFromToken($request->access_token);
            
            $user = $this->authService->handleProviderCallback($provider, $socialUser);
            
            
            $token = $user->createToken('auth-token')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil dengan ' . ucfirst($provider),
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Social token login error: ' . $e->getMessage(), ['exception' => $e]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat login dengan ' . ucfirst($provider),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}