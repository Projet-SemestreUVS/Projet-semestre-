

<?php

use App\Http\Controllers\API\ServiceController;
use Illuminate\Support\Facades\Route;

// Test API
Route::get('/test', function () {
    return response()->json([
        'message' => 'API fonctionne'
    ]);
});

// Routes Services
Route::controller(ServiceController::class)->group(function () {

    // Créer un service
    Route::post('/creationServices', 'store');

    // Liste des services
    Route::get('/listeServices', 'index');

    // Détail d’un service (avec ID)
    Route::get('/detailServices/{service}', 'show');

    // Modifier un service (avec ID)
    Route::put('/updateServices/{service}', 'update');

    // Supprimer un service (avec ID)
    Route::delete('/suppressionServices/{service}', 'destroy');

    // Services de l’utilisateur connecté
    Route::get('/recupererServices', 'myServices');
});