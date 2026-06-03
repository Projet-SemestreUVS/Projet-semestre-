<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Support\Facades\Auth;
=======
>>>>>>> origin/feature/abdoulaye
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
<<<<<<< HEAD
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
=======
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier si l'utilisateur a le rôle admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Vous n\'êtes pas administrateur'
            ], 403);
        }

        return $next($request);
    }
}
>>>>>>> origin/feature/abdoulaye
