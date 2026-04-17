<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedProviderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->role === 'prestataire' && !$user->is_verified) {
            // Block access with an alert message
            return redirect()->route('prestataire.dashboard')->with('error', 'Votre compte est en attente de vérification. Vous ne pouvez pas publier de services ou interagir pour le moment.');
        }

        return $next($request);
    }
}
