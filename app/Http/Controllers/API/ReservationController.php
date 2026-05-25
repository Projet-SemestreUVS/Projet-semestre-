<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return response()->json(
            Reservation::all()
        );
    }

    public function store(Request $request)
    {
        $reservation = Reservation::create([
            'service_id' => $request->service_id,
            'demandeur_id' => $request->demandeur_id,
            'prestataire_id' => $request->prestataire_id,
            'date_debut' => $request->date_debut,
            'commentaire' => $request->commentaire,
            'statut' => 'en_attente'
        ]);

        return response()->json([
            'message' => 'Réservation créée avec succès',
            'data' => $reservation
        ]);
    }

    public function show(Reservation $reservation)
    {
        return response()->json($reservation);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $reservation->update([
            'statut' => $request->statut
        ]);

        return response()->json([
            'message' => 'Statut mis à jour',
            'data' => $reservation
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return response()->json([
            'message' => 'Réservation supprimée'
        ]);
    }
}