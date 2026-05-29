<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Route de test - ajoutez ça en premier
Route::get('/test', function() {
    return response()->json(['message' => 'API fonctionne']);
});

/*
+ _______Routes Utilisateurs - KAY JOB_______
*/

Route::controller(UserController::class)->group(function () {
    // --- Routes publiques ---
    Route::get('/users', 'allUser');
    Route::get('/users/demandeurs', 'getDemandeurs');
    Route::get('/users/prestataires', 'getPrestataires');
    Route::get('/users/{id}', 'showUser');
    Route::get('/users/profile/{id}', 'profileUser');
    Route::get('/prestataires/search', 'searchPrestataires');
    Route::get('/demandeurs/search', 'searchDemandeurs');
});

// Routes protégées (celui qui l'authentification doit ajouter le middleware)
Route::controller(UserController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/user/connected', 'connectedUser');
    Route::put('/users/{id}', 'updateUser');
    Route::delete('/users/{id}', 'destroyUser');
});