public function store(Request $request)
{
    $validated = $request->validate([
        'demandeur_id' => 'required|exists:users,id',
        'prestataire_id' => 'required|exists:users,id',
        'service_id' => 'required|exists:services,id',
        'date_debut' => 'required|date',
        'date_fin' => 'required|date|after_or_equal:date_debut',
        'commentaire' => 'nullable|string',
        'statut' => 'nullable|string'
    ]);

    $reservation = Reservation::create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Réservation créée avec succès',
        'data' => $reservation
    ], 201);
}