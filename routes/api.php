<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CategoriController;

Route::apiResource('categories', CategoriController::class);