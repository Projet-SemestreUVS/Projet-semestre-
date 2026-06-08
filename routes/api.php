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
| TEST API
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
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | REGISTER & LOGIN
    |--------------------------------------------------------------------------
    */

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);

    /*
    |--------------------------------------------------------------------------
    | EMAIL VERIFICATION
    |--------------------------------------------------------------------------
    */

    Route::get('/email/verify/{id}/{hash}', function (
        EmailVerificationRequest $request
    ) {

        $request->fulfill();

        return response()->json([
            'success' => true,
            'message' => 'Email vérifié avec succès'
        ]);
    })
    ->middleware(['auth:sanctum', 'signed'])
    ->name('verification.verify');

    Route::post('/email/verification-notification', function (
        Request $request
    ) {

        $request
            ->user()
            ->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Lien de vérification envoyé'
        ]);
    })
    ->middleware([
        'auth:sanctum',
        'throttle:6,1'
    ]);

    /*
    |--------------------------------------------------------------------------
    | ROUTES PROTEGEES
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'auth:sanctum',
        'verified'
    ])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | AUTH
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/profile',
            [AuthController::class, 'profile']
        );

        Route::get(
            '/me',
            [AuthController::class, 'profile']
        );

        Route::post(
            '/logout',
            [AuthController::class, 'logout']
        );

        Route::post(
            '/refresh-token',
            function (Request $request) {

                $request
                    ->user()
                    ->tokens()
                    ->delete();

                $token = $request
                    ->user()
                    ->createToken('auth_token')
                    ->plainTextToken;

                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'users',
            UserController::class
        );

        /*
        |--------------------------------------------------------------------------
        | SERVICES
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'services',
            ServiceController::class
        );

        Route::get(
            '/mes-services',
            [ServiceController::class, 'myServices']
        );

        /*
        |--------------------------------------------------------------------------
        | RESERVATIONS
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'reservations',
            ReservationController::class
        );

        /*
        |--------------------------------------------------------------------------
        | AVIS
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'avis',
            AvisController::class
        );
         Route::patch('/avis/{id}/signaler', [AvisController::class, 'signaler']); // Route pour signaler

        /*
        |--------------------------------------------------------------------------
        | CATEGORIES
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'categories',
            CategoriController::class
        );

        /*
        |--------------------------------------------------------------------------
        | MESSAGES
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'messages',
            MessageController::class
        );
         Route::get('/messages/conversations', [MessageController::class, 'conversations']);
        Route::get('/messages/user/{userId}', [MessageController::class, 'messagesWithUser']);

        /*
        |--------------------------------------------------------------------------
        | NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'notifications',
            NotificationController::class
        );
         Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES ADMIN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/admin/statistiques',
            [StatistiqueController::class, 'index']
        );

          // Upload photo de profil
            Route::post('/users/upload-photo', [UserController::class, 'uploadPhoto']);
            
            // Changer le mot de passe
            Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
            
            // Mettre à jour le profil
            Route::put('/users/{id}', [UserController::class, 'update']);

        /*
        |--------------------------------------------------------------------------
        | APPLICATIONS TEST
        |--------------------------------------------------------------------------
        */

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

Route::middleware([
    'auth:sanctum',
    'admin'
])->get('/test-admin', function () {

    return response()->json([
        'success' => true,
        'message' => 'Middleware Admin OK'
    ]);
});

Route::middleware([
    'auth:sanctum',
    'prestataire'
])->get('/test-prestataire', function () {

    return response()->json([
        'success' => true,
        'message' => 'Middleware Prestataire OK'
    ]);
});

Route::middleware([
    'auth:sanctum',
    'demandeur'
])->get('/test-demandeur', function () {

    return response()->json([
        'success' => true,
        'message' => 'Middleware Demandeur OK'
    ]);
});