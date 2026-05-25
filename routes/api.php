<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;  // CORRECTION: 'API' au lieu de 'Api'
use App\Http\Controllers\API\UserController;  // CORRECTION: 'API' au lieu de 'Api'
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PUBLIC ROUTES
    |--------------------------------------------------------------------------
    */

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);

    /*
    |--------------------------------------------------------------------------
    | EMAIL VERIFICATION
    |--------------------------------------------------------------------------
    */

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {

        $request->fulfill();

        return response()->json([
            'success' => true,  // CORRECTION: Ajout du champ success
            'message' => 'Email vérifié avec succès'
        ]);

    })->middleware(['auth:sanctum', 'signed'])
      ->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,  // CORRECTION: Ajout du champ success
            'message' => 'Lien de vérification renvoyé avec succès'
        ]);

    })->middleware(['auth:sanctum', 'throttle:6,1']);

    /*
    |--------------------------------------------------------------------------
    | PROTECTED ROUTES
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {

        Route::get('/profile', [AuthController::class, 'profile']);

        Route::post('/logout', [AuthController::class, 'logout']);

        // CORRECTION: Routes CRUD complètes
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);     
        Route::patch('/users/{id}', [UserController::class, 'update']);   
        Route::delete('/users/{id}', [UserController::class, 'destroy']); 
        
        // CORRECTION: Route pour rafraîchir le token (optionnel)
        Route::post('/refresh-token', function (Request $request) {
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('auth_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer'
            ]);
        });
    });
});

// CORRECTION: Route de test pour vérifier que l'API fonctionne
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'API Laravel fonctionnelle'
    ]);
});