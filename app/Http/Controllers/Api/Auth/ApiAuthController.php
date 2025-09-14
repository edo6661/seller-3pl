<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;
use App\Services\ApiAuthService;
use App\Services\EmailVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    protected ApiAuthService $authService;
    protected EmailVerificationService $emailVerificationService;

    public function __construct(ApiAuthService $authService, EmailVerificationService $emailVerificationService)
    {
        $this->authService = $authService;
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * Login user dan return token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only(['email', 'password']);
            $deviceName = $request->input('device_name', 'api-device');
            
            $result = $this->authService->login($credentials, $deviceName);
            
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                    'token_type' => $result['token_type'],
                ]
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register user baru
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $userData = $request->validated();
            $deviceName = $request->input('device_name', 'api-device');
            
            // Gunakan service method yang mengembalikan array dengan token
            $result = $this->authService->createUserWithVerification($userData, $deviceName);
            
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi.',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                    'token_type' => $result['token_type']
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat registrasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user dan revoke current token
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $this->authService->logout($user);
            
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout dari semua device (revoke all tokens)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $deletedTokens = $this->authService->logoutAll($user);
            
            return response()->json([
                'success' => true,
                'message' => 'Logout dari semua device berhasil',
                'data' => [
                    'deleted_tokens_count' => $deletedTokens
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout dari semua device',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user data
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => new UserResource($user)
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $deviceName = $request->input('device_name', 'api-device');
            
            // Gunakan service method untuk refresh token
            $result = $this->authService->refreshToken($user, $deviceName);
            
            return response()->json([
                'success' => true,
                'message' => 'Token berhasil di-refresh',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                    'token_type' => $result['token_type']
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat refresh token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all user tokens
     */
    public function getTokens(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $tokens = $this->authService->getUserTokens($user);
            
            // Tambahkan informasi current token
            $currentTokenId = $request->user()->currentAccessToken()->id;
            $tokensWithCurrent = array_map(function ($token) use ($currentTokenId) {
                $token['is_current'] = $token['id'] === $currentTokenId;
                return $token;
            }, $tokens);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'tokens' => $tokensWithCurrent
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revoke specific token
     */
    public function revokeToken(Request $request, string $tokenId): JsonResponse
    {
        try {
            $user = $request->user();
            $success = $this->authService->revokeToken($user, $tokenId);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Token berhasil dihapus'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout specific token
     */
    public function logoutToken(Request $request, string $tokenId): JsonResponse
    {
        try {
            $user = $request->user();
            $success = $this->authService->logout($user, $tokenId);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Logout dari token tersebut berhasil'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forgot password - kirim email reset
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar dalam sistem.'
        ]);

        try {
            $this->authService->sendPasswordResetLink($request->email);
            
            return response()->json([
                'success' => true,
                'message' => 'Link reset password telah dikirim ke email Anda.'
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim email reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'token.required' => 'Token reset password diperlukan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        try {
            $this->authService->resetPassword(
                $request->token,
                $request->email,
                $request->password
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset.'
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify reset password token
     */
    public function verifyResetToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email'
        ]);

        try {
            $isValid = $this->authService->isValidResetToken($request->token, $request->email);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'is_valid' => $isValid
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat verifikasi token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Social login dengan provider callback
     */
    public function socialCallback(Request $request, string $provider): JsonResponse
    {
        try {
            // Implementasi ini memerlukan social user object dari provider
            // Biasanya didapat dari Socialite atau library social login lainnya
            
            return response()->json([
                'success' => false,
                'message' => 'Method ini memerlukan implementasi social login provider'
            ], 501);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat social login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Social login dengan token (untuk mobile apps)
     */
    public function socialToken(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'required|string|in:google,facebook',
            'access_token' => 'required|string',
            'device_name' => 'sometimes|string'
        ]);

        try {
            $deviceName = $request->input('device_name', 'api-device');
            
            $result = $this->authService->loginWithSocialToken(
                $request->provider,
                $request->access_token,
                $deviceName
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Social login berhasil',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                    'token_type' => $result['token_type'],
                ]
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat social login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $this->emailVerificationService->verifyEmail(
                $request->route('id'),
                $request->route('hash')
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Email berhasil diverifikasi.'
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat verifikasi email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            $this->emailVerificationService->resendVerificationEmail($request->email);
            
            return response()->json([
                'success' => true,
                'message' => 'Email verifikasi telah dikirim ulang.'
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim email verifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}