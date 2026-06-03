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

// Routes de test middleware
Route::middleware(['admin'])->get('/test-admin', function () {
    return response()->json([
        'success' => true,
        'message' => 'Bienvenue Boss ! Le middleware Admin fonctionne.'
    ]);
});

Route::middleware(['prestataire'])->get('/test-prestataire', function () {
    return response()->json([
        'success' => true,
        'message' => 'Lii ya baax ! Le middleware Prestataire fonctionne.'
    ]);
});

Route::middleware(['demandeur'])->get('/test-demandeur', function () {
    return response()->json([
        'success' => true,
        'message' => 'Parfait ! Le middleware Demandeur fonctionne.'
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

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
            'message' => 'Lien de vérification renvoyé avec succès'
        ]);
    })->middleware(['auth:sanctum', 'throttle:6,1']);

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::patch('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        Route::post('/refresh-token', function (Request $request) {
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer'
            ]);
        });

        Route::apiResource('reservations', ReservationController::class);
        Route::get('/messages', [MessageController::class, 'index']);
        Route::post('/messages', [MessageController::class, 'store']);
        Route::get('/messages/{id}', [MessageController::class, 'show']);
        Route::put('/messages/{id}', [MessageController::class, 'update']);
        Route::delete('/messages/{id}', [MessageController::class, 'destroy']);

        Route::apiResource('avis', AvisController::class);
        Route::apiResource('categories', CategoriController::class);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications', [NotificationController::class, 'store']);
        Route::get('/notifications/{id}', [NotificationController::class, 'show']);
        Route::put('/notifications/{id}', [NotificationController::class, 'update']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

        Route::controller(ServiceController::class)->group(function () {
            Route::post('/creationServices', 'store');
            Route::get('/listeServices', 'index');
            Route::get('/detailServices/{service}', 'show');
            Route::put('/updateServices/{service}', 'update');
            Route::delete('/suppressionServices/{service}', 'destroy');
            Route::get('/recupererServices', 'myServices');
        });

        Route::get('/applications', function () {
            return response()->json([
                'message' => 'Route applications OK',
                'user' => auth()->user(),
            ]);
        });
    });
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'API Laravel fonctionnelle'
    ]);
});

Route::get('/test', function () {
    return response()->json([
        'message' => 'API fonctionne'
    ]);
});
