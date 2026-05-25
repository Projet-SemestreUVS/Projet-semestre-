<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AvisController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/avis/create', [AvisController::class, 'create']);