<?php

namespace App\Http\Middleware\Api;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Anda harus login terlebih dahulu.'
            ], 401);
        }

        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Akses ditolak. Anda tidak memiliki hak akses admin.'
            ], 403);
        }

        return $next($request);
    }
}
