<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Affichage de l'utilisateur connecté
     * Retourne les informations de l'utilisateur qui est actuellement connecté.
     */
    public function connectedUser(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'password' => $user->password,
            'role' => $user->role,
            'telephone' => $user->telephone,
            'photo' => $user->photo,
            'localisation' => $user->localisation,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    /**
     * Récupération de tous les utilisateurs
     * Retourne la liste COMPLÈTE de TOUS les utilisateurs (demandeurs + prestataires).
     */
    public function allUser()
    {
        $users = User::all();
        return response()->json([
            'success'=>true,
            'data'=>$users
        ]);
    }

    /**
     * Récupération des demandeurs uniquement
     * Retourne uniquement les utilisateurs qui ont le rôle demandeur.
     */
    public function getDemandeurs()
    {
        $demandeurs = User::where('role', 'demandeur')->get();
        
        return response()->json([
            'success' => true,
            'data' => $demandeurs
        ]);
    }

    /**
     * Récupération des prestataires uniquement
     * Retourne uniquement les utilisateurs qui ont le rôle prestataire.
     */
    public function getPrestataires()
    {
        $prestataires = User::where('role', 'prestataire')->get();
        
        return response()->json([
            'success' => true,
            'data' => $prestataires
        ]);
    }

    /**
     * Afficher un utilisateur spécifique
     * Affiche les détails d'UN SEUL utilisateur spécifique par son ID.
     */
    public function showUser(string $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Mettre à jour un utilisateur
     * Modifie les informations d'un utilisateur existant.
     */
    public function updateUser(Request $request, string $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
        
        // Validation des données
        $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'telephone' => 'nullable|string|max:20',
            'photo' => 'nullable|string|max:255',
            'localisation' => 'nullable|string|max:255'
        ]);
        
        // Mise à jour des champs
        if ($request->has('nom')) {
            $user->nom = $request->nom;
        }
        if ($request->has('prenom')) {
            $user->prenom = $request->prenom;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('telephone')) {
            $user->telephone = $request->telephone;
        }
        if ($request->has('photo')) {
            $user->photo = $request->photo;
        }
        if ($request->has('localisation')) {
            $user->localisation = $request->localisation;
        }
        
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => $user
        ]);
    }

    /**
     * Supprimer un utilisateur
     * Supprime définitivement un utilisateur de la base de données.
     */
    public function destroyUser(string $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }

    /**
     * Rechercher des prestataires par localisation
     * Recherche des prestataires par localisation ou par nom.
     */
    public function searchPrestataires(Request $request)
    {
        $query = User::where('role', 'prestataire');
        
        if ($request->has('localisation')) {
            $query->where('localisation', 'like', '%' . $request->localisation . '%');
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', '%' . $search . '%')
                  ->orWhere('prenom', 'like', '%' . $search . '%');
            });
        }
        
        $prestataires = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $prestataires
        ]);
    }

    /**
     * Rechercher des demandeurs par localisation
     * Recherche des demandeurs par localisation ou par nom.
     */
    public function searchDemandeurs(Request $request)
    {
        $query = User::where('role', 'demandeur');
        
        if ($request->has('localisation')) {
            $query->where('localisation', 'like', '%' . $request->localisation . '%');
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', '%' . $search . '%')
                  ->orWhere('prenom', 'like', '%' . $search . '%');
            });
        }
        
        $demandeurs = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $demandeurs
        ]);
    }

    /**
     * Profil complet d'un utilisateur avec ses statistiques
     * Retourne un profil COMPLET avec des statistiques (services, avis, notes).
     */
    public function profileUser(string $id)
    {
        $user = User::with(['services', 'avis'])->find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
        
        $statistiques = [];
        
        if ($user->role === 'prestataire') {
            $statistiques = [
                'total_services' => $user->services->count(),
                'total_avis' => $user->avis->count(),
                'note_moyenne' => $user->avis->avg('note') ?? 0
            ];
        } else {
            $statistiques = [
                'total_reservations' => $user->reservations()->count()
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $user,
            'statistiques' => $statistiques
        ]);
    }
}