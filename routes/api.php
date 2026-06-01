<?php

use Illuminate\Support\Facades\Route;

// 1. Route de test pour l'Admin
Route::middleware(['admin'])->get('/test-admin', function () {
    return response()->json([
        'success' => true,
        'message' => 'Bienvenue Boss ! Le middleware Admin fonctionne.'
    ]);
});

// 2. Route de test pour le Prestataire
Route::middleware(['prestataire'])->get('/test-prestataire', function () {
    return response()->json([
        'success' => true,
        'message' => 'Lii ya baax ! Le middleware Prestataire fonctionne.'
    ]);
});

// 3. Route de test pour le Demandeur
Route::middleware(['demandeur'])->get('/test-demandeur', function () {
    return response()->json([
        'success' => true,
        'message' => 'Parfait ! Le middleware Demandeur fonctionne.'
    ]);
});