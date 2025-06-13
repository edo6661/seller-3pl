<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;

class PasswordValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentPassword = $request->input('current_password');
        
        if ($currentPassword && !Hash::check($currentPassword, auth()->user()->password)) {
            return redirect()
                ->back()
                ->withInput($request->except('current_password', 'password', 'password_confirmation'))
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        return $next($request);
    }
}
