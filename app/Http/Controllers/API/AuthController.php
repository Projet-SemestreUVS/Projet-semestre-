<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'nom' => 'required|string|max:255',

            'prenom' => 'required|string|max:255',

            'email' => 'required|email|unique:users,email',

            'password' => 'required|min:6|confirmed',

            'role' => 'required|in:admin,demandeur,prestataire',  // CORRECTION: ajouté 'admin'

            'telephone' => 'nullable|string|max:20',

            'photo' => 'nullable|string',

            'localisation' => 'nullable|string|max:255',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | CREATION UTILISATEUR
        |--------------------------------------------------------------------------
        */

        $user = User::create([

            'nom' => $request->nom,

            'prenom' => $request->prenom,

            'email' => $request->email,

            // CORRECTION: Ne pas mettre email_verified_at à now() si on veut une vraie vérification
            // 'email_verified_at' => now(),  // À COMMENTER pour que la vérification email fonctionne

            'password' => Hash::make($request->password),

            'role' => $request->role,

            'telephone' => $request->telephone,

            'photo' => $request->photo,

            'localisation' => $request->localisation,
        ]);
        
        // CORRECTION: Déclencher l'événement de vérification email
        event(new Registered($user));

        /*
        |--------------------------------------------------------------------------
        | TOKEN
        |--------------------------------------------------------------------------
        */

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([

            'success' => true,

            'message' => 'Inscription réussie. Veuillez vérifier votre email.', // CORRECTION: Message plus clair

            'token' => $token,

            'token_type' => 'Bearer',

            'user' => $user

        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'email' => 'required|email',

            'password' => 'required'

        ]);

        if ($validator->fails()) {

            return response()->json([

                'success' => false,
                
                'message' => 'Erreur de validation', // CORRECTION: Ajout du message

                'errors' => $validator->errors()

            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFICATION EMAIL
        |--------------------------------------------------------------------------
        */

        $user = User::where('email', $request->email)->first();

        if (!$user) {

            return response()->json([

                'success' => false,

                'message' => 'Email incorrect'

            ], 401);
        }
        
        // CORRECTION: Vérifier si l'email est vérifié (optionnel, décommentez si nécessaire)
        /*
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez vérifier votre email avant de vous connecter'
            ], 403);
        }
        */

        /*
        |--------------------------------------------------------------------------
        | VERIFICATION PASSWORD
        |--------------------------------------------------------------------------
        */

        if (!Hash::check($request->password, $user->password)) {

            return response()->json([

                'success' => false,

                'message' => 'Mot de passe incorrect'

            ], 401);
        }

        // CORRECTION: Supprimer les anciens tokens pour éviter les fuites (optionnel)
        $user->tokens()->delete();

        /*
        |--------------------------------------------------------------------------
        | CREATION TOKEN
        |--------------------------------------------------------------------------
        */

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([

            'success' => true,

            'message' => 'Connexion réussie',

            'token' => $token,

            'token_type' => 'Bearer',

            'user' => $user

        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | PROFIL UTILISATEUR CONNECTE
    |--------------------------------------------------------------------------
    */

    public function profile(Request $request)
    {

        return response()->json([

            'success' => true,

            'user' => $request->user()

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {

        // CORRECTION: Supprimer le token courant au lieu de currentAccessToken()
        $request->user()->currentAccessToken()->delete();

        return response()->json([

            'success' => true,

            'message' => 'Déconnexion réussie'

        ]);
    }
}