<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Service;
use Illuminate\Http\Request;
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
            
            if ($user->role === 'demandeur') {
                $reservations = Reservation::where('demandeur_id', $user->id)
                    ->with(['service', 'prestataire'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            } 
            elseif ($user->role === 'prestataire') {
                $reservations = Reservation::where('prestataire_id', $user->id)
                    ->with(['service', 'demandeur'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            } 
            else {
                $reservations = Reservation::with(['service', 'demandeur', 'prestataire'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $reservations
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une nouvelle réservation
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'service_id' => 'required|exists:services,id',
                'demandeur_id' => 'required|exists:users,id',
                'prestataire_id' => 'required|exists:users,id',
                'date_debut' => 'required|date|after:now',
                'commentaire' => 'nullable|string|max:1000',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Vérifier que l'utilisateur connecté est bien le demandeur
            if ($request->user()->id != $request->demandeur_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }
            
            // Vérifier que le service appartient bien au prestataire
            $service = Service::find($request->service_id);
            if ($service->prestataire_id != $request->prestataire_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le service ne correspond pas au prestataire sélectionné'
                ], 422);
            }
            
            $reservation = Reservation::create([
                'service_id' => $request->service_id,
                'demandeur_id' => $request->demandeur_id,
                'prestataire_id' => $request->prestataire_id,
                'date_debut' => $request->date_debut,
                'statut' => 'en_attente',
                'commentaire' => $request->commentaire,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Réservation créée avec succès',
                'data' => $reservation->load(['service', 'prestataire'])
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
            $reservation = Reservation::with(['service', 'demandeur', 'prestataire'])->findOrFail($id);
            
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
            $reservation = Reservation::findOrFail($id);
            $user = $request->user();
            
            $validator = Validator::make($request->all(), [
                'statut' => 'sometimes|in:en_attente,confirmee,terminee,annulee',
                'date_debut' => 'sometimes|date|after:now',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Vérifier les permissions
            if ($user->role === 'demandeur' && $reservation->demandeur_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à modifier cette réservation'
                ], 403);
            }
            
            if ($user->role === 'prestataire' && $reservation->prestataire_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à modifier cette réservation'
                ], 403);
            }
            
            $reservation->update($request->only(['statut', 'date_debut']));
            
            return response()->json([
                'success' => true,
                'message' => 'Réservation mise à jour',
                'data' => $reservation
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
            $reservation = Reservation::findOrFail($id);
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