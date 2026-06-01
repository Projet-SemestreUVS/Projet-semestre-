<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // CORRECTION: Ajout d'un middleware pour protéger les routes
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('verified');
        
        // CORRECTION: Restreindre l'accès admin si nécessaire
        // $this->middleware('admin')->only(['destroy', 'update']);
    }

    public function index()
    {
        // CORRECTION: Pagination au lieu de all() pour les performances
        $users = User::latest()->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // CORRECTION: Charger les relations si nécessaire
            // $user->load(['services', 'reservationsDemandeur']);
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            // CORRECTION: Validation des données avant mise à jour
            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|string|max:255',
                'prenom' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'telephone' => 'nullable|string|max:20',
                'photo' => 'nullable|string',
                'localisation' => 'nullable|string|max:255'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // CORRECTION: Vérifier les permissions (admin ou l'utilisateur lui-même)
            if ($request->user()->id !== $user->id && $request->user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à modifier cet utilisateur'
                ], 403);
            }
            
            $user->update($request->only([
                'nom', 'prenom', 'email', 'telephone', 'photo', 'localisation'
            ]));
            
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur modifié avec succès',
                'data' => $user
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $user = User::findOrFail($id);
            
            // CORRECTION: Vérifier les permissions (admin uniquement)
            if ($request->user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à supprimer des utilisateurs'
                ], 403);
            }
            
            // CORRECTION: Empêcher la suppression de son propre compte
            if ($request->user()->id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas supprimer votre propre compte'
                ], 403);
            }
            
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
    }
}