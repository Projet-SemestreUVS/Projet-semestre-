
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::middleware('auth:sanctum')->group(function () {
    
    // Notifications


Route::get('/notifications', [NotificationController::class, 'index']);        // Liste toutes les notifications
Route::post('/notifications', [NotificationController::class, 'store']);       // Crée une nouvelle notification
Route::get('/notifications/{id}', [NotificationController::class, 'show']);    // Affiche une notification précise
Route::put('/notifications/{id}', [NotificationController::class, 'update']);  // Met à jour une notification
Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']); // Supprime une notification

    
    // Route test
    Route::get('/applications', function () {
        return response()->json([
            'message' => 'Route applications OK',
            'user' => auth()->user(),
        ]);
     });//
});//