<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Service;
use App\Models\Reservation;
use App\Models\Avis;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Statistiques générales
     */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'users' => User::count(),
                'services' => Service::count(),
                'reservations' => Reservation::count(),
                'avis' => Avis::count(),
                'users_by_role' => [
                    'admin' => User::where('role', 'admin')->count(),
                    'demandeur' => User::where('role', 'demandeur')->count(),
                    'prestataire' => User::where('role', 'prestataire')->count(),
                ],
            ]
        ]);
    }

    /**
     * Liste tous les utilisateurs
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Filtrer par rôle
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Recherche par nom ou email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nom', 'like', "%$search%")
                  ->orWhere('prenom', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
        }

        $users = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function showUser($id)
    {
        $user = User::with([
            'services',
            'reservationsDemandeur',
            'reservationsPrestataire',
            'avisAuteur',
            'avisCible'
        ])->find($id);

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
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        $validated = $request->validate([
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'role' => 'nullable|in:admin,demandeur,prestataire',
            'telephone' => 'nullable|string|max:20',
            'photo' => 'nullable|string',
            'localisation' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur mis à jour',
            'data' => $user
        ]);
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer un administrateur'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé'
        ]);
    }

    /**
     * Liste tous les services
     */
    public function services(Request $request)
    {
        $query = Service::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('titre', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
        }

        $services = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Supprimer un service
     */
    public function deleteService($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service non trouvé'
            ], 404);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service supprimé'
        ]);
    }

    /**
     * Liste les réservations
     */
    public function reservations(Request $request)
    {
        $query = Reservation::query();

        if ($request->has('status')) {
            $query->where('statut', $request->status);
        }

        $reservations = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Afficher les détails d'une réservation
     */
    public function showReservation($id)
    {
        $reservation = Reservation::with(['demandeur', 'prestataire', 'service'])->find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $reservation
        ]);
    }

    /**
     * Liste les avis
     */
    public function avis(Request $request)
    {
        $query = Avis::query();

        if ($request->has('rating')) {
            $query->where('note', $request->rating);
        }

        $avis = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $avis
        ]);
    }

    /**
     * Supprimer un avis
     */
    public function deleteAvis($id)
    {
        $avis = Avis::find($id);

        if (!$avis) {
            return response()->json([
                'success' => false,
                'message' => 'Avis non trouvé'
            ], 404);
        }

        $avis->delete();

        return response()->json([
            'success' => true,
            'message' => 'Avis supprimé'
        ]);
    }
}