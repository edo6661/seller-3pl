<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class TeamPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();
        if ($user->isTeamMember()) {
            $teamMember = $user->memberOf()->first();
            if (!$teamMember || !$teamMember->hasPermission($permission)) {
                return redirect()
                    ->back()
                    ->with('error', 'Anda tidak memiliki hak akses untuk halaman ini.');
            }
            return $next($request);
        }
        if ($user->isSeller()) {
            return $next($request);
        }
        return redirect()
            ->route('guest.auth.login')
            ->with('error', 'Akses tidak diizinkan.');
    }
}