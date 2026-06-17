<?php
// app/Http/Controllers/API/ReservationController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    /**
     * Afficher la liste des réservations
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            // Admin voit toutes les réservations
            if ($user->role === 'admin') {
                $reservations = Reservation::with(['service', 'demandeur', 'prestataire'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            } 
            // Prestataire voit ses réservations
            elseif ($user->role === 'prestataire') {
                $reservations = Reservation::with(['service', 'demandeur'])
                    ->where('prestataire_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } 
            // Demandeur voit ses réservations
            else {
                $reservations = Reservation::with(['service', 'prestataire'])
                    ->where('demandeur_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $reservations,
                'message' => 'Réservations récupérées avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une réservation
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'service_id' => 'required|exists:services,id',
                'date_debut' => 'required|date|after:now',
                'commentaire' => 'nullable|string',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $service = Service::findOrFail($request->service_id);
            
            $reservation = Reservation::create([
                'service_id' => $request->service_id,
                'demandeur_id' => $request->user()->id,
                'prestataire_id' => $service->prestataire_id,
                'date_debut' => $request->date_debut,
                'statut' => 'en_attente',
                'commentaire' => $request->commentaire,
                'prix_total' => $service->prix,
            ]);
            
            $reservation->load(['service', 'demandeur', 'prestataire']);
            
            return response()->json([
                'success' => true,
                'data' => $reservation,
                'message' => 'Réservation créée avec succès'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher une réservation spécifique
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            $reservation = Reservation::with(['service', 'demandeur', 'prestataire'])->findOrFail($id);
            
            // Vérifier les permissions
            if ($user->role !== 'admin' && 
                $reservation->demandeur_id !== $user->id && 
                $reservation->prestataire_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }
            
            return response()->json([
                'success' => true,
                'data' => $reservation
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée'
            ], 404);
        }
    }

    /**
     * Mettre à jour une réservation
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $reservation = Reservation::findOrFail($id);
            
            // Vérifier les permissions
            if ($user->role !== 'admin' && $reservation->prestataire_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas le droit de modifier cette réservation'
                ], 403);
            }
            
            $validator = Validator::make($request->all(), [
                'statut' => 'sometimes|in:en_attente,confirmee,terminee,annulee',
                'commentaire' => 'nullable|string',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $reservation->update($request->only(['statut', 'commentaire']));
            $reservation->load(['service', 'demandeur', 'prestataire']);
            
            return response()->json([
                'success' => true,
                'data' => $reservation,
                'message' => 'Réservation mise à jour avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une réservation
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $reservation = Reservation::findOrFail($id);
            
            // Seul l'admin peut supprimer
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Seul un administrateur peut supprimer une réservation'
                ], 403);
            }
            
            $reservation->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Réservation supprimée avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }
}