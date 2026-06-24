<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Oblige l'utilisateur à être connecté via Sanctum pour interagir avec l'API
        $this->middleware('auth:sanctum');
    }

    // Liste utilisateurs
    public function index()
    {
        $users = User::latest()->get();

        return response()->json([
            "success" => true,
            "data" => $users
        ]);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                "message" => "Utilisateur introuvable"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "data" => $user
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "nom" => "required",
                "prenom" => "required",
                "email" => "required|email|unique:users",
                "password" => "required|min:6",
                "role" => "required"
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                422
            );
        }

        $user = User::create([
            "nom" => $request->nom,
            "prenom" => $request->prenom,
            "email" => $request->email,
            "password" => Hash::make($request->password), // Sécurisation du mot de passe
            "telephone" => $request->telephone,
            "role" => $request->role,
            "localisation" => $request->localisation // Ajout de la localisation gérée par React
        ]);

        return response()->json([
            "success" => true,
            "data" => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                "message" => "Utilisateur introuvable"
            ], 404);
        }

        // Si un mot de passe est fourni lors de la mise à jour, on le hash
        $data = $request->all();
        if ($request->has('password') && !empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            "success" => true,
            "data" => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                "message" => "Utilisateur introuvable"
            ], 404);
        }

        $user->delete();

        return response()->json([
            "success" => true
        ]);
    }
}