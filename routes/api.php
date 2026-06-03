<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\AvisController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\CategoriController;

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
            'success' => true,
            'message' => 'Email vérifié avec succès'
        ]);

    })->middleware(['auth:sanctum', 'signed'])
      ->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
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

        // Routes users complètes
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);     
        Route::patch('/users/{id}', [UserController::class, 'update']);   
        Route::delete('/users/{id}', [UserController::class, 'destroy']); 
        
        // Route pour rafraîchir le token
        Route::post('/refresh-token', function (Request $request) {
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('auth_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer'
            ]);
        });
      
        // Routes de réservations protégées
        Route::apiResource('reservations', ReservationController::class);
        
         // Routes de catégorie protégées
        Route::apiResource('categories', CategoriController::class);

        // Routes de messages protégées
        Route::get('/messages', [MessageController::class, 'index']);
        Route::post('/messages', [MessageController::class, 'store']);
        Route::get('/messages/{id}', [MessageController::class, 'show']);
        Route::put('/messages/{id}', [MessageController::class, 'update']);
        Route::delete('/messages/{id}', [MessageController::class, 'destroy']);

        // Routes des avis protégées
        Route::apiResource('avis', AvisController::class);

        // Routes des notifications protégées
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications', [NotificationController::class, 'store']);
        Route::get('/notifications/{id}', [NotificationController::class, 'show']);
        Route::put('/notifications/{id}', [NotificationController::class, 'update']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

        // Routes des services protégées
        Route::controller(ServiceController::class)->group(function () {
            Route::post('/creationServices', 'store');
            Route::get('/listeServices', 'index');
            Route::get('/detailServices/{service}', 'show');
            Route::put('/updateServices/{service}', 'update');
            Route::delete('/suppressionServices/{service}', 'destroy');
            Route::get('/recupererServices', 'myServices');
        });

        // Route test
        Route::get('/applications', function () {
            return response()->json([
                'message' => 'Route applications OK',
                'user' => auth()->user(),
            ]);
        });
    });
});

// Route de test pour vérifier que l'API fonctionne
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'API Laravel fonctionnelle'
    ]);
});

// Route test API
Route::get('/test', function () {
    return response()->json([
        'message' => 'API fonctionne'
    ]);
});


