<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoriController extends Controller
{
    /**
     * Afficher toutes les catégories
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json($categories);
    }

    /**
     * Ajouter une catégorie
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required'
        ]);

        $categorie = Category::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'icone' => $request->icone,
            'parent_id' => $request->parent_id
        ]);

        return response()->json([
            'message' => 'Categorie ajoutée',
            'data' => $categorie
        ], 201);
    }

    /**
     * Afficher une catégorie
     */
    public function show(string $id)
    {
        $categorie = Category::findOrFail($id);

        return response()->json($categorie);
    }

    /**
     * Modifier une catégorie
     */
    public function update(Request $request, string $id)
    {
        $categorie = Category::findOrFail($id);

        $categorie->update($request->all());

        return response()->json([
            'message' => 'Categorie modifiée',
            'data' => $categorie
        ]);
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy(string $id)
    {
        $categorie = Category::findOrFail($id);

        $categorie->delete();

        return response()->json([
            'message' => 'Categorie supprimée'
        ]);
    }
}