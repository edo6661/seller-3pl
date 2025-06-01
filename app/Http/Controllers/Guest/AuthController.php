<?php

namespace App\Http\Controllers\Guest;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Services\EmailVerificationService;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Tampilkan halaman login
     */
    public function login(): View
    {
        return view('guest.auth.login');
    }

    /**
     * Proses login
     */
    public function loginSubmit(LoginRequest $request): RedirectResponse
    {
        try {
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

    /**
     * Tampilkan halaman register
     */
    public function register(): View
    {
        return view('guest.auth.register');
    }

    /**
     * Proses logout
     */
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

    /**
     * Tampilkan halaman lupa password
     */
    public function forgotPassword(): View
    {
        return view('guest.auth.forgot-password');
    }

    /**
     * Tampilkan halaman reset password
     */
    public function resetPassword(Request $request): View
    {
        return view('guest.auth.reset-password', [
            'token' => $request->route('token'),
            'email' => $request->email
        ]);
    }


    public function createUserWithVerification(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['email'] = strtolower($data['email']);
        
        $user = User::create($data);
        
        // Dispatch event to send verification email
        event(new UserRegistered($user));
        
        return $user;
    }

# 7. Update AuthController.php - Replace existing methods with these implementations:

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
            $passwordResetService = app(PasswordResetService::class);
            $passwordResetService->sendResetLink($request->email);
            
            return back()
                ->with('success', 'Link reset password telah dikirim ke email Anda.');
                
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat mengirim email reset password.')
                ->withInput();
        }
    }

    public function resetPasswordSubmit(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'token.required' => 'Token reset password diperlukan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        try {
            $passwordResetService = app(PasswordResetService::class);
            $passwordResetService->resetPassword(
                $request->token,
                $request->email,
                $request->password
            );
            
            return redirect()
                ->route('guest.auth.login')
                ->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
                
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput($request->except('password', 'password_confirmation'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat reset password.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    public function verifyEmail(Request $request): RedirectResponse
    {
        try {
            $emailVerificationService = app(EmailVerificationService::class);
            $emailVerificationService->verifyEmail(
                $request->route('id'),
                $request->route('hash')
            );
            
            return redirect()
                ->route('guest.auth.login')
                ->with('success', 'Email berhasil diverifikasi. Silakan login.');
                
        } catch (ValidationException $e) {
            return redirect()
                ->route('guest.auth.login')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->route('guest.auth.login')
                ->with('error', 'Terjadi kesalahan saat verifikasi email.');
        }
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            $emailVerificationService = app(EmailVerificationService::class);
            $emailVerificationService->resendVerificationEmail($request->email);
            
            return back()
                ->with('success', 'Email verifikasi telah dikirim ulang.');
                
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat mengirim email verifikasi.');
        }
    }
}