<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemandeurMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sécurité : On vérifie si l'utilisateur n'est pas connecté ou s'il n'est pas demandeur
        if (!auth()->check() || auth()->user()->role !== 'demandeur') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Réservé aux demandeurs de services.'
            ], 403);
        } 

        return $next($request);
    }
}