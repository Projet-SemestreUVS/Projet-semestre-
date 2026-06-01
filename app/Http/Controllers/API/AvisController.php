<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    // Liste des avis
    public function index()
    {
        return Avis::all();
    }

    // Ajouter un avis
    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required',
            'auteur_id' => 'required',
            'cible_id' => 'required',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string',
            'signale' => 'nullable|boolean'
        ]);

        $avis = Avis::create($request->all());

        return response()->json($avis, 201);
    }

    // Voir un avis
    public function show($id)
    {
        return Avis::findOrFail($id);
    }

    // Modifier un avis
    public function update(Request $request, $id)
    {
        $avis = Avis::findOrFail($id);

        $request->validate([
            'note' => 'sometimes|integer|min:1|max:5',
            'commentaire' => 'sometimes|string',
            'signale' => 'sometimes|boolean'
        ]);

        $avis->update($request->all());

        return response()->json($avis);
    }

    // Supprimer un avis
    public function destroy($id)
    {
        Avis::destroy($id);

        return response()->json([
            'message' => 'Avis supprimé avec succès'
        ]);
    }
}