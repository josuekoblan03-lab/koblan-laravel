<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Non authentifié.');
        }

        $hasAccess = false;
        if ($user->role === 'admin') {
            $hasAccess = true;
        } elseif ($user->role === 'prestataire' && in_array($role, ['client', 'prestataire'])) {
            $hasAccess = true;
        } elseif ($user->role === $role) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
