<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
     * Proses lupa password
     */
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
            // Logic untuk kirim email reset password
            // Implementasi sesuai kebutuhan aplikasi
            
            return back()
                ->with('success', 'Link reset password telah dikirim ke email Anda.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat mengirim email reset password.')
                ->withInput();
        }
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

    /**
     * Proses reset password
     */
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
            // Logic untuk reset password
            // Implementasi sesuai kebutuhan aplikasi
            
            return redirect()
                ->route('guest.auth.login')
                ->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat reset password.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Verifikasi email
     */
    public function verifyEmail(Request $request): RedirectResponse
    {
        try {
            $user = $this->authService->getUserById($request->route('id'));
            
            if (!$user) {
                return redirect()
                    ->route('guest.auth.login')
                    ->with('error', 'User tidak ditemukan.');
            }

            // Logic untuk verifikasi email
            // Implementasi sesuai kebutuhan aplikasi
            
            return redirect()
                ->route('guest.auth.login')
                ->with('success', 'Email berhasil diverifikasi. Silakan login.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('guest.auth.login')
                ->with('error', 'Terjadi kesalahan saat verifikasi email.');
        }
    }

    /**
     * Kirim ulang email verifikasi
     */
    public function resendVerification(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            // Logic untuk kirim ulang email verifikasi
            // Implementasi sesuai kebutuhan aplikasi
            
            return back()
                ->with('success', 'Email verifikasi telah dikirim ulang.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan saat mengirim email verifikasi.');
        }
    }
}