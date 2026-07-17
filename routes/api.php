<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\CompetenceController;
use App\Http\Controllers\OffreController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->get('/admin-test', function () {
    return response()->json([
        'message' => 'Bienvenue Admin'
    ]);
});

Route::middleware(['auth:sanctum', 'role:candidate'])->get('/candidate-test', function () {
    return response()->json([
        'message' => 'Bienvenue Candidate'
    ]);
});

Route::middleware(['auth:sanctum', 'role:entreprise'])->get('/entreprise-test', function () {
    return response()->json([
        'message' => 'Bienvenue Entreprise'
    ]);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/entreprises', [EntrepriseController::class, 'index']);

    Route::get('/entreprises/{entreprise}', [EntrepriseController::class, 'show']);

    Route::middleware('role:entreprise,admin')->group(function () {

        Route::post('/entreprises', [EntrepriseController::class, 'store']);

        Route::put('/entreprises/{entreprise}', [EntrepriseController::class, 'update']);

        Route::delete('/entreprises/{entreprise}', [EntrepriseController::class, 'destroy']);

    });

});

Route::middleware(['auth:sanctum','role:admin'])->group(function () {

    Route::apiResource('competences', CompetenceController::class);

});

Route::middleware('auth:sanctum')->group(function () {

    // Public pour les utilisateurs connectés
    Route::get('/offres', [OffreController::class, 'index']);
    Route::get('/offres/{offre}', [OffreController::class, 'show']);

    // Entreprise ou Admin
    Route::middleware('role:entreprise,admin')->group(function () {

        Route::post('/offres', [OffreController::class, 'store']);
        Route::put('/offres/{offre}', [OffreController::class, 'update']);
        Route::delete('/offres/{offre}', [OffreController::class, 'destroy']);

    });

});