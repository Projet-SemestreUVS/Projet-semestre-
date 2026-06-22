<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Afficher toutes les réservations.
     */
    public function index()
    {
        return response()->json(Reservation::all());
    }

    /**
     * Formulaire de création (inutile pour une API).
     */
    public function create()
    {
        //
    }

    /**
     * Enregistrer une nouvelle réservation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'demandeur_id' => 'required|integer',
            'prestataire_id' => 'required|integer',
            'service_id' => 'required|integer',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date',
            'commentaire' => 'nullable|string',
        ]);

        $reservation = Reservation::create([
            'demandeur_id' => $request->demandeur_id,
            'prestataire_id' => $request->prestataire_id,
            'service_id' => $request->service_id,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => 'en_attente',
            'commentaire' => $request->commentaire,
        ]);

        return response()->json([
            'message' => 'Réservation créée avec succès',
            'data' => $reservation
        ], 201);
    }

    /**
     * Afficher une réservation.
     */
    public function show(Reservation $reservation)
    {
        return response()->json($reservation);
    }

    /**
     * Formulaire d'édition (inutile pour une API).
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Mettre à jour une réservation.
     */
    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'date_debut' => 'sometimes|date',
            'date_fin' => 'sometimes|date',
            'statut' => 'sometimes|string',
            'commentaire' => 'nullable|string',
        ]);

        $reservation->update($request->all());

        return response()->json([
            'message' => 'Réservation mise à jour avec succès',
            'data' => $reservation
        ]);
    }

    /**
     * Supprimer une réservation.
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return response()->json([
            'message' => 'Réservation supprimée avec succès'
        ]);
    }
}
