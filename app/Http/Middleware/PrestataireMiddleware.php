<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrestataireMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user() && auth()->user()->role === 'prestataire') {
            return $next($request);
        }

        return response()->json([
            'message' => 'Accès refusé'
        ], 403);
    }
}