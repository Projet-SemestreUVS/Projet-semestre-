<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\AvisController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\CategoriController;
use App\Http\Controllers\API\StatistiqueController;

/*
|--------------------------------------------------------------------------
| ROUTES API PUBLIQUES (SANS AUTH)
|--------------------------------------------------------------------------
*/

// Routes publiques - DOIVENT être définies AVANT les routes protégées
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'status' => 'OK',
        'message' => 'API Laravel fonctionnelle'
    ]);
});

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API fonctionne'
    ]);
});

// Routes publiques pour les services et catégories
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::get('/categories', [CategoriController::class, 'index']);
Route::get('/categories/{id}', [CategoriController::class, 'show']);

/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {

    // Register & Login (public)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Email Verification (public avec middleware spécifique)
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return response()->json([
            'success' => true,
            'message' => 'Email vérifié avec succès'
        ]);
    })->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json([
            'success' => true,
            'message' => 'Lien de vérification envoyé'
        ]);
    })->middleware(['auth:sanctum', 'throttle:6,1']);

    // Routes protégées
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        // Auth
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::get('/me', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        
        Route::post('/refresh-token', function (Request $request) {
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('auth_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer'
            ]);
        });

        // Users
        Route::apiResource('users', UserController::class);
        Route::post('/users/upload-photo', [UserController::class, 'uploadPhoto']);
        Route::put('/users/{id}', [UserController::class, 'update']);

        // Services (protégés)
        Route::get('/mes-services', [ServiceController::class, 'myServices']);
        Route::apiResource('services', ServiceController::class);

        // Reservations
        Route::apiResource('reservations', ReservationController::class);

        // Avis
        Route::apiResource('avis', AvisController::class);
        Route::patch('/avis/{id}/signaler', [AvisController::class, 'signaler']);

        // Categories (CRUD complet pour admin)
        Route::apiResource('categories', CategoriController::class);

        // Messages
        Route::apiResource('messages', MessageController::class);
        Route::get('/messages/conversations', [MessageController::class, 'conversations']);
        Route::get('/messages/user/{userId}', [MessageController::class, 'messagesWithUser']);

        // Notifications
        Route::apiResource('notifications', NotificationController::class);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

        // Statistiques Admin
        Route::get('/admin/statistiques', [StatistiqueController::class, 'index']);

        // Test applications
        Route::get('/applications', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'Route applications OK',
                'user' => $request->user(),
            ]);
        });
    });
});

/*
|--------------------------------------------------------------------------
| TEST MIDDLEWARES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'admin'])->get('/test-admin', function () {
    return response()->json([
        'success' => true,
        'message' => 'Middleware Admin OK'
    ]);
});

Route::middleware(['auth:sanctum', 'prestataire'])->get('/test-prestataire', function () {
    return response()->json([
        'success' => true,
        'message' => 'Middleware Prestataire OK'
    ]);
});

Route::middleware(['auth:sanctum', 'demandeur'])->get('/test-demandeur', function () {
    return response()->json([
        'success' => true,
        'message' => 'Middleware Demandeur OK'
    ]);
});