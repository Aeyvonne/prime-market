<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProducteurController;

/*
|--------------------------------------------------------------------------
| PRIME MARKET — API Routes
|--------------------------------------------------------------------------
|
| Toutes les routes sont préfixées par /api (défini dans bootstrap/app.php)
|
| Middleware disponibles :
|   auth:sanctum      → vérifie que le token est valide
|   role:X            → vérifie le rôle de l'utilisateur (CheckRole)
|
*/

/* -----------------------------------------------------------------------
 | Routes PUBLIQUES — pas de token requis
 | ----------------------------------------------------------------------- */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Catégories publiques (accessible à tous)
Route::get('/categories', [ProducteurController::class, 'categories']);
Route::get('/sous-categories', [ProducteurController::class, 'sousCategories']);
/* -----------------------------------------------------------------------
 | Routes PROTÉGÉES — token Sanctum requis
 | ----------------------------------------------------------------------- */
Route::middleware('auth:sanctum')->group(function () {

    // Auth communes à tous les rôles
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profil',  [AuthController::class, 'profil']);

    /* -------------------------------------------------------------------
     | PRODUCTEUR
     | Agriculteur / Éleveur / Pêcheur
     | ------------------------------------------------------------------- */
    Route::middleware('role:producteur')->prefix('producteur')->group(function () {
        Route::get('/produits',         [ProducteurController::class, 'index']);
        Route::post('/produits',        [ProducteurController::class, 'store']);
        Route::put('/produits/{id}',    [ProducteurController::class, 'update']);
        Route::delete('/produits/{id}', [ProducteurController::class, 'destroy']);
        Route::get('/commandes',        [ProducteurController::class, 'commandes']);
    });

    /* -------------------------------------------------------------------
     | DISTRIBUTEUR
     | Grossiste / Détaillant
     | ------------------------------------------------------------------- */
    Route::middleware('role:distributeur')->prefix('distributeur')->group(function () {
        // À compléter par le Membre 5
        // Route::get('/catalogue',       [DistributeurController::class, 'catalogue']);
        // Route::post('/commandes',      [DistributeurController::class, 'passerCommande']);
        // Route::get('/commandes',       [DistributeurController::class, 'mesCommandes']);
        // Route::get('/livraisons',      [DistributeurController::class, 'livraisons']);
    });

    /* -------------------------------------------------------------------
     | CONSOMMATEUR
     | ------------------------------------------------------------------- */
    Route::middleware('role:consommateur')->prefix('consommateur')->group(function () {
        // Route::get('/catalogue',       [ConsommateurController::class, 'catalogue']);
        // Route::post('/commandes',      [ConsommateurController::class, 'passerCommande']);
        // Route::post('/avis',           [ConsommateurController::class, 'laisserAvis']);
    });

    /* -------------------------------------------------------------------
     | TRANSPORTEUR
     | ------------------------------------------------------------------- */
    Route::middleware('role:transporteur')->prefix('transporteur')->group(function () {
        // Route::get('/missions',        [TransporteurController::class, 'missions']);
        // Route::post('/missions/{id}/accepter', [TransporteurController::class, 'accepter']);
        // Route::post('/missions/{id}/refuser',  [TransporteurController::class, 'refuser']);
        // Route::put('/livraisons/{id}/statut',  [TransporteurController::class, 'updateStatut']);
    });

    /* -------------------------------------------------------------------
     | ADMIN SECTORIEL — gestion de son secteur uniquement
     | ------------------------------------------------------------------- */
    Route::middleware('role:admin_sectoriel')->prefix('admin')->group(function () {
        // Route::get('/comptes',         [AdminController::class, 'index']);
        // Route::put('/comptes/{id}/activer',   [AdminController::class, 'activer']);
        // Route::put('/comptes/{id}/suspendre', [AdminController::class, 'suspendre']);
        // Route::delete('/comptes/{id}',        [AdminController::class, 'supprimer']);
        // Route::get('/transactions',    [AdminController::class, 'transactions']);
    });

    /* -------------------------------------------------------------------
     | SUPER ADMIN — accès global à tout
     | ------------------------------------------------------------------- */
    Route::middleware('role:super_administrateur')->prefix('super-admin')->group(function () {
        // Route::get('/categories',      [SuperAdminController::class, 'categories']);
        // Route::post('/categories',     [SuperAdminController::class, 'creerCategorie']);
        // Route::put('/categories/{id}', [SuperAdminController::class, 'modifierCategorie']);
        // Route::delete('/categories/{id}', [SuperAdminController::class, 'supprimerCategorie']);
    });

    /* -------------------------------------------------------------------
     | ROUTES COMMUNES
     | ------------------------------------------------------------------- */
    Route::middleware('role:producteur,distributeur,admin_sectoriel,super_administrateur')->prefix('commun')->group(function () {
        // Route::get('/paiements', [PaiementController::class, 'index']);
    });
});