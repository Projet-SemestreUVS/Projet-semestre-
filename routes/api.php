<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\AvisController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CategoriController;

// Route de test publique
Route::get('/test', function() {
    return response()->json(['message' => 'API fonctionne']);
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'API Laravel fonctionnelle'
    ]);
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (register / login / email verification)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {

    // PUBLIC
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // EMAIL VERIFICATION
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return response()->json([ 'success' => true, 'message' => 'Email vérifié avec succès' ]);
    })->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json([ 'success' => true, 'message' => 'Lien de vérification renvoyé avec succès' ]);
    })->middleware(['auth:sanctum', 'throttle:6,1']);

    // PROTECTED (requires auth + verified)
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Users CRUD (protected)
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::patch('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Token refresh
        Route::post('/refresh-token', function (Request $request) {
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('auth_token')->plainTextToken;
            return response()->json([ 'success' => true, 'token' => $token, 'token_type' => 'Bearer' ]);
        });

        // Protected resources
        Route::apiResource('reservations', ReservationController::class);

        Route::get('/messages', [MessageController::class, 'index']);
        Route::post('/messages', [MessageController::class, 'store']);
        Route::get('/messages/{id}', [MessageController::class, 'show']);
        Route::put('/messages/{id}', [MessageController::class, 'update']);
        Route::delete('/messages/{id}', [MessageController::class, 'destroy']);

        Route::apiResource('avis', AvisController::class);

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
            return response()->json([ 'message' => 'Route applications OK', 'user' => auth()->user() ]);
        });
    });
});

/*
|--------------------------------------------------------------------------
| PUBLIC USER ROUTES (KAY JOB)
|--------------------------------------------------------------------------
*/
Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'allUser');
    Route::get('/users/demandeurs', 'getDemandeurs');
    Route::get('/users/prestataires', 'getPrestataires');
    Route::get('/users/{id}', 'showUser');
    Route::get('/users/profile/{id}', 'profileUser');
    Route::get('/prestataires/search', 'searchPrestataires');
    Route::get('/demandeurs/search', 'searchDemandeurs');
});

// Routes protégées non-verbales (sanctum)
Route::controller(UserController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('/user/connected', 'connectedUser');
    Route::put('/users/{id}', 'updateUser');
    Route::delete('/users/{id}', 'destroyUser');
});

// Categories resource
Route::apiResource('categories', CategoriController::class);
