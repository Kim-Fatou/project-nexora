<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsPremium
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isPremium()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Accès Premium requis.'], 403);
            }
            return redirect()->route('socialnet')->with('error', 'Tu dois être Premium (niveau 6 / 100 points ou abonné) pour accéder à cette fonctionnalité.');
        }

        return $next($request);
    }
}
