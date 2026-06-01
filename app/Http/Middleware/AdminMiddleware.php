<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Vérifier si l'utilisateur est authentifié et si son rôle est 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // Accès autorisé, on passe à la suite
        }

        // 2. Sinon, interdire l'accès
        return response()->json([
            'success' => false,
            'message' => 'Accès refusé. Autorisation Administrateur requise.'
        ], 403); // Code HTTP 403 = Forbidden
    }
}