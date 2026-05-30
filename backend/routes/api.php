<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DistributeurController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\LivraisonController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ProducteurController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminSectorialController;
use App\Http\Controllers\ConsommateurController;
use App\Http\Controllers\TransporteurController;

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
| */

/* -----------------------------------------------------------------------
 | Routes PUBLIQUES — pas de token requis
 | ----------------------------------------------------------------------- */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Routes publiques pour les produits et catégories
Route::get('/produits', [ProduitController::class, 'index']);
Route::get('/produits/{id}', [ProduitController::class, 'show']);
Route::get('/categories', [CategorieController::class, 'index']);

// Callback paiement (appelé par Wave/OrangeMoney — pas de auth requis)
Route::get('/paiements/callback', [PaiementController::class, 'callback']);
Route::post('/paiements/simuler/confirmer', [PaiementController::class, 'simulerConfirmer']);


/* -----------------------------------------------------------------------
 | Routes PROTÉGÉES — token Sanctum requis
 | ----------------------------------------------------------------------- */
Route::middleware('auth:sanctum')->group(function () {

    // Auth communes à tous les rôles
    Route::get('/badges', [CommandeController::class, 'badges']);
    Route::post('/panier/checkout', [CommandeController::class, 'checkoutPanier']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profil',  [AuthController::class, 'profil']);
    Route::put('/profil',  [AuthController::class, 'updateProfil']);
    Route::put('/profil/password', [AuthController::class, 'updatePassword']);

    /* -------------------------------------------------------------------
     | PRODUCTEUR
     | Agriculteur / Éleveur / Pêcheur
     | ------------------------------------------------------------------- */
    // Routes producteur accessibles uniquement si le compte est actif
    Route::middleware(['role:producteur', 'statut:actif'])->prefix('producteur')->group(function () {
        Route::get('/produits',         [ProduitController::class, 'mesProduits']);
        Route::post('/produits',        [ProduitController::class, 'store']);
        Route::get('/produits/{id}',    [ProduitController::class, 'show']);
        Route::put('/produits/{id}',    [ProduitController::class, 'update']);
        Route::post('/produits/{id}',   [ProduitController::class, 'update']); // Pour supporter _method=PUT dans multipart/form-data
        Route::delete('/produits/{id}', [ProduitController::class, 'destroy']);
        Route::get('/commandes',                           [ProducteurController::class, 'commandes']);
        Route::put('/commandes/{id}/confirmer',            [ProducteurController::class, 'confirmerCommande']);
        Route::put('/commandes/{id}/refuser',              [ProducteurController::class, 'refuserCommande']);
        Route::get('/ventes',                              [ProducteurController::class, 'ventes']);
        Route::get('/paiements',                           [ProducteurController::class, 'paiements']);
    });

    // Documents justificatifs — accessibles même si le compte est en_attente
    Route::middleware('role:producteur')->prefix('producteur')->group(function () {
        Route::get('/documents',                           [ProducteurController::class, 'indexDocuments']);
        Route::post('/documents',                          [ProducteurController::class, 'uploadDocument']);
        Route::delete('/documents/{id}',                   [ProducteurController::class, 'deleteDocument']);
    });

    /* -------------------------------------------------------------------
     | DISTRIBUTEUR
     | Grossiste / Détaillant
     | ------------------------------------------------------------------- */
    Route::middleware('role:distributeur')->prefix('distributeur')->group(function () {
        // Catalogue (achat auprès des producteurs)
        Route::get('/catalogue',       [DistributeurController::class, 'catalogue']);
        Route::get('/catalogue/{id}',  [DistributeurController::class, 'showCatalogueProduit']);

        // Commandes passées par le distributeur
        Route::get('/commandes',       [DistributeurController::class, 'mesCommandes']);
        Route::post('/commandes',      [DistributeurController::class, 'passerCommande']);
        Route::get('/commandes/{id}',  [DistributeurController::class, 'showCommande']);
        Route::put('/commandes/{id}/annuler', [DistributeurController::class, 'annulerCommande']);

        // Gestion de ses propres produits (pour la revente)
        Route::get('/produits',        [DistributeurController::class, 'mesProduits']);
        Route::post('/produits',       [DistributeurController::class, 'storeProduit']);
        Route::put('/produits/{id}',   [DistributeurController::class, 'updateProduit']);
        Route::delete('/produits/{id}',[DistributeurController::class, 'destroyProduit']);

        // Ventes (commandes reçues en tant que vendeur)
        Route::get('/ventes',                     [DistributeurController::class, 'ventes']);
        Route::put('/ventes/{id}/confirmer',      [DistributeurController::class, 'confirmerCommande']);
        Route::put('/ventes/{id}/refuser',        [DistributeurController::class, 'refuserCommande']);

        // Suivi
        Route::get('/livraisons',      [DistributeurController::class, 'livraisons']);
        Route::get('/livraisons/{id}', [DistributeurController::class, 'showLivraison']);
        Route::get('/paiements',       [DistributeurController::class, 'paiements']);
    });

    /* -------------------------------------------------------------------
     | CONSOMMATEUR
     | ------------------------------------------------------------------- */
    Route::middleware('role:consommateur')->prefix('consommateur')->group(function () {
        // Catalogue
        Route::get('/catalogue',         [ConsommateurController::class, 'catalogue']);
        Route::get('/catalogue/{id}',    [ConsommateurController::class, 'showProduit']);

        // Commandes
        Route::get('/commandes',                    [ConsommateurController::class, 'indexCommandes']);
        Route::post('/commandes',                   [ConsommateurController::class, 'storeCommande']);
        Route::get('/commandes/{id}',               [ConsommateurController::class, 'showCommande']);
        Route::put('/commandes/{id}/annuler',       [ConsommateurController::class, 'annulerCommande']);
        Route::put('/commandes/{id}/confirmer-reception', [ConsommateurController::class, 'confirmerReception']);

        // Paiements
        Route::get('/paiements',         [ConsommateurController::class, 'indexPaiements']);
        Route::post('/paiements',        [ConsommateurController::class, 'storePaiement']);

        // Livraisons
        Route::get('/livraisons',        [ConsommateurController::class, 'indexLivraisons']);
        Route::get('/livraisons/{id}',   [ConsommateurController::class, 'showLivraison']);

        // Évaluations
        Route::get('/evaluations',                           [ConsommateurController::class, 'indexEvaluations']);
        Route::post('/evaluations',                          [ConsommateurController::class, 'storeEvaluation']);
        Route::get('/commandes-evaluables',                  [ConsommateurController::class, 'commandesEvaluables']);
    });

    /* -------------------------------------------------------------------
     | TRANSPORTEUR
     | ------------------------------------------------------------------- */
    Route::middleware('role:transporteur')->prefix('transporteur')->group(function () {
        // Missions
        Route::get('/missions',                          [TransporteurController::class, 'indexMissions']);
        Route::get('/missions/{id}',                     [TransporteurController::class, 'showMission']);
        Route::put('/missions/{id}/accepter',            [TransporteurController::class, 'accepterMission']);
        Route::put('/missions/{id}/refuser',             [TransporteurController::class, 'refuserMission']);

        // Livraisons
        Route::get('/livraisons',                        [TransporteurController::class, 'indexLivraisons']);
        Route::get('/livraisons/{id}',                   [TransporteurController::class, 'showLivraison']);
        Route::put('/livraisons/{id}/statut',            [TransporteurController::class, 'updateStatutLivraison']);

        // Rémunérations
        Route::get('/remunerations',                     [TransporteurController::class, 'remunerations']);

        // Disponibilité
        Route::put('/disponibilite',                     [TransporteurController::class, 'updateDisponibilite']);
    });

    /* -------------------------------------------------------------------
     | ADMIN SECTORIEL — gestion de son secteur uniquement
     | ------------------------------------------------------------------- */
    Route::middleware('role:admin_sectoriel')->prefix('admin-sectoriel')->group(function () {
        // Transactions du secteur
        Route::get('/transactions', [AdminSectorialController::class, 'transactions']);

        // Gestion des comptes du secteur
        Route::get('/comptes', [AdminSectorialController::class, 'indexComptes']);
        Route::put('/comptes/{id}/activer', [AdminSectorialController::class, 'activerCompte']);
        Route::put('/comptes/{id}/suspendre', [AdminSectorialController::class, 'suspendreCompte']);
        Route::delete('/comptes/{id}', [AdminSectorialController::class, 'destroyCompte']);

        // Documents producteurs du secteur
        Route::get('/comptes/en-attente/count', [AdminSectorialController::class, 'comptesEnAttenteCount']);
        Route::get('/comptes/en-attente', [AdminSectorialController::class, 'comptesEnAttente']);
        Route::get('/comptes/{id}/documents', [AdminSectorialController::class, 'indexDocuments']);
        Route::put('/documents/{id}/verifier', [AdminSectorialController::class, 'verifyDocument']);
        Route::put('/documents/{id}/rejeter', [AdminSectorialController::class, 'rejectDocument']);

        // Gestion des sous-catégories
        Route::get('/sous-categories', [AdminSectorialController::class, 'indexSousCategories']);
        Route::post('/sous-categories', [AdminSectorialController::class, 'storeSousCategorie']);
        Route::put('/sous-categories/{id}', [AdminSectorialController::class, 'updateSousCategorie']);
        Route::delete('/sous-categories/{id}', [AdminSectorialController::class, 'destroySousCategorie']);
    });

    /* -------------------------------------------------------------------
     | SUPER ADMIN — accès global à tout
     | ------------------------------------------------------------------- */
    Route::middleware('role:super_administrateur')->prefix('super-admin')->group(function () {
        // Gestion des catégories
        Route::get('/categories', [SuperAdminController::class, 'indexCategories']);
        Route::post('/categories', [SuperAdminController::class, 'storeCategorie']);
        Route::put('/categories/{id}', [SuperAdminController::class, 'updateCategorie']);
        Route::delete('/categories/{id}', [SuperAdminController::class, 'destroyCategorie']);

        // Gestion de tous les comptes
        Route::get('/comptes', [SuperAdminController::class, 'indexComptes']);
        Route::post('/comptes', [SuperAdminController::class, 'storeCompte']);
        Route::put('/comptes/{id}', [SuperAdminController::class, 'updateCompte']);
        Route::delete('/comptes/{id}', [SuperAdminController::class, 'destroyCompte']);

        // Statistiques globales
        Route::get('/statistiques', [SuperAdminController::class, 'statistiques']);

        // Transactions globales
        Route::get('/transactions', [SuperAdminController::class, 'transactions']);

        // Vérification des documents producteurs
        Route::get('/comptes/en-attente/count', [SuperAdminController::class, 'comptesEnAttenteCount']);
        Route::get('/comptes/en-attente', [SuperAdminController::class, 'comptesEnAttente']);
        Route::get('/comptes/{id}/documents', [SuperAdminController::class, 'indexDocuments']);
        Route::put('/documents/{id}/verifier', [SuperAdminController::class, 'verifyDocument']);
        Route::put('/documents/{id}/rejeter', [SuperAdminController::class, 'rejectDocument']);
    });

    /* -------------------------------------------------------------------
     | ROUTES COMMUNES
     | ------------------------------------------------------------------- */
    Route::middleware('role:producteur,distributeur,admin_sectoriel,super_administrateur')->prefix('commun')->group(function () {
        // Route::get('/paiements', [PaiementController::class, 'index']);
    });
});