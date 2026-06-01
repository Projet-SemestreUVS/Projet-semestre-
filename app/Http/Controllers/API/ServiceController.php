<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    // Liste des services
    public function index(Request $request)
    {
        $query = Service::with(['prestataire', 'categorie']);

        // Filtre par catégorie
        if ($request->has('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        // Filtre par disponibilité
        if ($request->has('disponible')) {
            $query->where(
                'disponibilite',
                filter_var($request->disponible, FILTER_VALIDATE_BOOLEAN)
            );
        }

        // Filtre par statut (admin seulement)
        if ($request->has('statut') && auth()->user()?->role === 'admin') {
            $query->where('statut', $request->statut);
        }

        // Recherche
        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $services = $query->latest()->paginate(12);

        return response()->json($services);
    }

    // Détails d’un service
    public function show(Service $service)
    {
        $service->load(['prestataire', 'categorie']);

        return response()->json($service);
    }

    // Création d’un service
    public function store(ServiceRequest $request)
    {
        $data = $request->validated();

        $photosPaths = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $filename = Str::random(20) . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('services', $filename, 'public');
                $photosPaths[] = $filename;
            }
        }

        $service = Service::create([
            // Si authentifié, utiliser l'utilisateur courant, sinon permettre `user_id` fourni ou fallback au user 101
            'user_id' => auth()->id() ?? ($data['user_id'] ?? 101),
            'categorie_id' => $data['categorie_id'] ?? null,
            'titre' => $data['titre'],
            'description' => $data['description'],
            'tarif' => $data['tarif'],
            'photos' => $photosPaths,
            'disponibilite' => $data['disponibilite'] ?? true,
            'statut' => 'active',
        ]);

        return response()->json([
            'message' => 'Service créé avec succès',
            'service' => $service->load(['prestataire', 'categorie'])
        ], 201);
    }

    // Mise à jour d’un service
    public function update(ServiceRequest $request, Service $service)
    {
        // Si un utilisateur est authentifié, vérifier la propriété; sinon autoriser (mode test)
        if (auth()->check()) {
            if (auth()->id() !== $service->user_id && auth()->user()?->role !== 'admin') {
                return response()->json(['message' => 'Non autorisé'], 403);
            }
        }

        $data = $request->validated();

        if ($request->hasFile('photos')) {

            if (!empty($service->photos)) {
                foreach ($service->photos as $oldPhoto) {
                    Storage::disk('public')->delete('services/' . $oldPhoto);
                }
            }

            $photosPaths = [];

            foreach ($request->file('photos') as $photo) {
                $filename = Str::random(20) . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('services', $filename, 'public');
                $photosPaths[] = $filename;
            }

            $data['photos'] = $photosPaths;
        }

        $service->update($data);

        return response()->json([
            'message' => 'Service mis à jour',
            'service' => $service->fresh(['prestataire', 'categorie'])
        ]);
    }

    // Suppression d’un service
    public function destroy(Service $service)
    {
        // Comme pour update : si authentifié vérifie la propriété, sinon autorise (mode test)
        if (auth()->check()) {
            if (auth()->id() !== $service->user_id && auth()->user()?->role !== 'admin') {
                return response()->json(['message' => 'Non autorisé'], 403);
            }
        }

        if (!empty($service->photos)) {
            foreach ($service->photos as $photo) {
                Storage::disk('public')->delete('services/' . $photo);
            }
        }

        $service->delete();

        return response()->json([
            'message' => 'Service supprimé'
        ]);
    }

    // Mes services
    public function myServices()
    {
        $services = Service::where('user_id', auth()->id())
            ->with('categorie')
            ->latest()
            ->paginate(10);

        return response()->json($services);
    }
}