<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AvisController;

/*
|--------------------------------------------------------------------------
| Web Routes (pages web classiques)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| AVIS (version web - si tu utilises Blade)
|--------------------------------------------------------------------------
*/

// Afficher le formulaire de création d'avis
Route::get('/avis/create', [AvisController::class, 'create']);

// (optionnel) enregistrer un avis depuis formulaire web
Route::post('/avis', [AvisController::class, 'store']);