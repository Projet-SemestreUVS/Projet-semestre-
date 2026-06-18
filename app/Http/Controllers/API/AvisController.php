<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class AvisController extends Controller
{
    /**
     * Afficher la liste des avis
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->role === 'demandeur') {
                // Les avis donnés par le demandeur
                $avis = Avis::where('auteur_id', $user->id)
                    ->with(['reservation.service', 'cible'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            } 
            elseif ($user->role === 'prestataire') {
                // Les avis reçus par le prestataire
                $avis = Avis::where('cible_id', $user->id)
                    ->with(['reservation.service', 'auteur'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            } 
            else {
                // Admin voit tous les avis
                $avis = Avis::with(['reservation.service', 'auteur', 'cible'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $avis
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouvel avis
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reservation_id' => 'required|exists:reservations,id',
                'note' => 'required|integer|min:1|max:5',
                'commentaire' => 'required|string|min:3|max:1000',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Vérifier que la réservation existe et est terminée
            $reservation = Reservation::find($request->reservation_id);
            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation non trouvée'
                ], 404);
            }
            
            if ($reservation->statut !== 'terminee') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez laisser un avis que sur une réservation terminée'
                ], 422);
            }
            
            // Vérifier que l'utilisateur est le demandeur de la réservation
            if ($request->user()->id !== $reservation->demandeur_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à laisser un avis sur cette réservation'
                ], 403);
            }
            
            // Vérifier qu'un avis n'existe pas déjà
            $existingAvis = Avis::where('reservation_id', $request->reservation_id)->first();
            if ($existingAvis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Un avis a déjà été laissé pour cette réservation'
                ], 422);
            }
            
            $avis = Avis::create([
                'reservation_id' => $request->reservation_id,
                'auteur_id' => $request->user()->id,
                'cible_id' => $reservation->prestataire_id,
                'note' => $request->note,
                'commentaire' => $request->commentaire,
                'signale' => false,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Avis publié avec succès',
                'data' => $avis->load(['reservation.service', 'cible'])
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un avis spécifique
     */
    public function show($id)
    {
        try {
            $avis = Avis::with(['reservation.service', 'auteur', 'cible'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $avis
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Avis non trouvé'
            ], 404);
        }
    }

    /**
     * Mettre à jour un avis
     */
    public function update(Request $request, $id)
    {
        try {
            $avis = Avis::findOrFail($id);
            $user = $request->user();
            
            // Seul l'auteur peut modifier son avis
            if ($avis->auteur_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à modifier cet avis'
                ], 403);
            }
            
            $validator = Validator::make($request->all(), [
                'note' => 'sometimes|integer|min:1|max:5',
                'commentaire' => 'sometimes|string|min:3|max:1000',
                'signale' => 'sometimes|boolean',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $avis->update($request->only(['note', 'commentaire', 'signale']));
            
            return response()->json([
                'success' => true,
                'message' => 'Avis mis à jour',
                'data' => $avis
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un avis
     */
    public function destroy($id)
    {
        try {
            $avis = Avis::findOrFail($id);
            $avis->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Avis supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }
    
    /**
     * Signaler un avis
     */
    public function signaler($id)
    {
        try {
            $avis = Avis::findOrFail($id);
            $avis->update(['signale' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Avis signalé avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du signalement'
            ], 500);
        }
    }
}