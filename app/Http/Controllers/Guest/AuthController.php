<?php

namespace App\Http\Controllers\Guest;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Services\EmailVerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected AuthService $authService;
    protected EmailVerificationService $emailVerificationService;

    public function __construct(AuthService $authService, EmailVerificationService $emailVerificationService)
    {
        $this->authService = $authService;
        $this->emailVerificationService = $emailVerificationService;
    }

    public function login(): View
    {
        return view('guest.auth.login');
    }

    public function loginSubmit(LoginRequest $request): RedirectResponse
    {
        try {
            $credentials = $request->validated();
            $credentials = $request->only(['email', 'password']);
            $remember = $request->boolean('remember');
            
            $this->authService->login($credentials, $remember);
            
            $request->session()->regenerate();
            
            return redirect()
                ->intended($this->authService->redirectAfterLogin())
                ->with('success', 'Selamat datang! Anda berhasil masuk.');
                
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput($request->except('password'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat proses login. Silakan coba lagi.')
                ->withInput($request->except('password'));
        }
    }

    public function register(): View
    {
        return view('guest.auth.register');
    }

    public function registerSubmit(RegisterRequest $request): RedirectResponse
    {
        try {
            $userData = $request->validated();
            $user = $this->authService->createUserWithVerification($userData);
            
            return redirect()
                ->route('guest.auth.login')
                ->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        try {
            $this->authService->logout();
            
            return redirect()
                ->route('guest.home')
                ->with('success', 'Anda berhasil keluar dari sistem.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat logout.');
        }
    }

    public function forgotPassword(): View
    {
        return view('guest.auth.forgot-password');
    }

    public function forgotPasswordSubmit(Request $request): RedirectResponse
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
            
            return back()
                ->with('success', 'Link reset password telah dikirim ke email Anda. Silakan periksa kotak masuk dan folder spam.');
                
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat mengirim email reset password. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function resetPassword(Request $request)
    {
        $token = $request->route('token');
        $email = $request->email;

        // Validasi token dan email
        if (!$token || !$email) {
            return redirect()
                ->route('guest.auth.forgot-password')
                ->with('error', 'Link reset password tidak valid.');
        }

        // Verifikasi apakah token masih valid
        if (!$this->authService->isValidResetToken($token, $email)) {
            return redirect()
                ->route('guest.auth.forgot-password')
                ->with('error', 'Link reset password sudah kedaluwarsa atau tidak valid. Silakan ajukan reset password baru.');
        }

        return view('guest.auth.form-reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    public function resetPasswordSubmit(Request $request): RedirectResponse
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
            
            return redirect()
                ->route('guest.auth.login')
                ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
                
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput($request->except('password', 'password_confirmation'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat reset password. Silakan coba lagi.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    // PERBAIKAN UTAMA: Menambahkan pesan sukses setelah verifikasi email
    public function verifyEmail(Request $request): RedirectResponse
    {
        try {
            $userId = $request->route('id');
            $hash = $request->route('hash');
            
            $this->emailVerificationService->verifyEmail($userId, $hash);
            
            // Ambil user untuk pesan personal
            $user = User::find($userId);
            $userName = $user ? $user->name : 'Pengguna';
            
            return redirect()
                ->route('guest.auth.login')
                ->with('success', "Selamat {$userName}! Email Anda berhasil diverifikasi. Sekarang Anda dapat masuk ke akun Anda.");
                
        } catch (ValidationException $e) {
            // Ambil pesan error yang spesifik
            $errorMessage = collect($e->errors())->flatten()->first();
            
            return redirect()
                ->route('guest.auth.login')
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            return redirect()
                ->route('guest.auth.login')
                ->with('error', 'Terjadi kesalahan saat verifikasi email. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar dalam sistem.'
        ]);

        try {
            $this->emailVerificationService->resendVerificationEmail($request->email);
            
            // Check if it's AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email verifikasi telah dikirim ulang ke ' . $request->email
                ]);
            }
            
            return back()
                ->with('success', 'Email verifikasi telah dikirim ulang ke ' . $request->email . '. Silakan periksa kotak masuk dan folder spam Anda.');
                
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first()
                ], 422);
            }
            
            return back()->withErrors($e->errors());
            
        }catch (\Exception $e) {
                return back()
                    ->with('error', 'Terjadi kesalahan saat mengirim email verifikasi. Silakan coba lagi.');
            }
        }
    
}