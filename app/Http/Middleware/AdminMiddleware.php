<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class AdminMiddleware
{

    public function handle(
        Request $request,
        Closure $next
    ): Response
    {


        // Vérifier que l'utilisateur est connecté
        if (!Auth::check()) {

            return response()->json([
                'success'=>false,
                'message'=>'Utilisateur non authentifié'
            ],401);

        }



        // Vérifier le rôle admin
        if (Auth::user()->role !== 'admin') {


            return response()->json([
                'success'=>false,
                'message'=>'Accès refusé. Admin uniquement'
            ],403);

        }



        return $next($request);

    }

}