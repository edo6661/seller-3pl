<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('guest.auth.login')
                ->with('error', 'Anda harus login terlebih dahulu.');
        }

        if (!auth()->user()->isAdmin()) {
            $redirectRoute = match(auth()->user()->role) {
                UserRole::SELLER => 'seller.dashboard',
                UserRole::ADMIN => 'admin.dashboard',
                default => 'home'
            };

            return redirect()->route($redirectRoute)
                ->with('error', 'Akses ditolak. Anda tidak memiliki hak akses admin.');
        }

        return $next($request);
    }
}
