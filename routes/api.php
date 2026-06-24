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
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| HEALTH & TEST ROUTES
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {

    // PUBLIC ROUTES
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

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

    // PROTECTED ROUTES
    Route::middleware(['auth:sanctum'])->group(function () {

        // AUTH
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::get('/me', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::post('/refresh-token', function (Request $request) {
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer'
            ]);
        });

        // USERS
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::post('/users/upload-photo', [UserController::class, 'uploadPhoto']);

        Route::post('/change-password', [AuthController::class, 'changePassword']);

        // DASHBOARD
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        // ADMIN STATISTICS
        Route::get('/admin/statistiques', [StatistiqueController::class, 'index']);

        // RESERVATIONS
        Route::apiResource('reservations', ReservationController::class);

        // CATEGORIES
        Route::apiResource('categories', CategoriController::class);

        // MESSAGES
        Route::get('/messages', [MessageController::class, 'index']);
        Route::post('/messages', [MessageController::class, 'store']);
        Route::get('/messages/{id}', [MessageController::class, 'show']);
        Route::put('/messages/{id}', [MessageController::class, 'update']);
        Route::delete('/messages/{id}', [MessageController::class, 'destroy']);
        Route::get('/messages/conversations', [MessageController::class, 'conversations']);
        Route::get('/messages/user/{userId}', [MessageController::class, 'messagesWithUser']);

        // AVIS
        Route::apiResource('avis', AvisController::class);
        Route::patch('/avis/{id}/signaler', [AvisController::class, 'signaler']);

        // NOTIFICATIONS
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications', [NotificationController::class, 'store']);
        Route::get('/notifications/{id}', [NotificationController::class, 'show']);
        Route::put('/notifications/{id}', [NotificationController::class, 'update']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

        // SERVICES
        Route::controller(ServiceController::class)->group(function () {
            Route::post('/creationServices', 'store');
            Route::get('/listeServices', 'index');
            Route::get('/detailServices/{service}', 'show');
            Route::put('/updateServices/{service}', 'update');
            Route::delete('/suppressionServices/{service}', 'destroy');
            Route::get('/recupererServices', 'myServices');
        });

        Route::apiResource('services', ServiceController::class);
        Route::get('/mes-services', [ServiceController::class, 'myServices']);

        // APPLICATIONS TEST
        Route::get('/applications', function () {
            return response()->json([
                'success' => true,
                'message' => 'Route applications OK',
                'user' => auth()->user(),
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
