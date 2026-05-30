<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Categorie;
use App\Models\SousCategorie;
use App\Models\Paiement;
use App\Models\AdminSectorielle;
use App\Models\DocumentProducteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminSectorialController extends Controller
{
    /**
     * Helper pour obtenir le secteur de l'administrateur connecté.
     */
    private function getSector()
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin_sectoriel' || !$user->adminSectorielle) {
            abort(403, 'Accès interdit. Rôle d\'administrateur sectoriel requis.');
        }
        return $user->adminSectorielle->secteur;
    }

    /* ==========================================
     * SUPERVISION DES TRANSACTIONS
     * ========================================== */

    /**
     * Liste des transactions du secteur.
     */
    public function transactions(Request $request)
    {
        try {
            $sector = $this->getSectorFromRequest($request);

            $paiements = Paiement::whereHas('commande.vendeur.producteur', function ($q) use ($sector) {
                $q->where('secteur_travail', $sector);
            })
            ->with([
                'commande.acheteur',
                'commande.vendeur',
                'commande.lignesCommande.produit.sousCategorie'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json([
                'secteur' => $sector,
                'transactions' => $paiements,
                'total' => $paiements->sum('montant')
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur transactions sectorielles: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des transactions.'], 500);
        }
    }

    /* ==========================================
     * GESTION DES COMPTES PRODUCTEURS
     * ========================================== */

    /**
     * Liste des producteurs du secteur.
     */
    public function indexComptes(Request $request)
    {
        try {
            $sector = $this->getSectorFromRequest($request);

            $comptes = User::with('producteur')
                ->where('role', 'producteur')
                ->whereHas('producteur', function ($query) use ($sector) {
                    $query->where('secteur_travail', $sector);
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    return [
                        'id'              => $user->id,
                        'nom'             => $user->nom,
                        'prenom'          => $user->prenom,
                        'email'           => $user->email,
                        'telephone'       => $user->telephone,
                        'statut'          => $user->statut,
                        'role'            => $user->role,
                        'adresse'         => $user->adresse,
                        'created_at'      => $user->created_at,
                        'secteur_travail' => $user->producteur?->secteur_travail,
                    ];
                });

            return response()->json([
                'secteur'  => $sector,
                'comptes'  => $comptes,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur comptes sectoriels: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des comptes.'], 500);
        }
    }

    private function getSectorFromRequest(Request $request): string
    {
        $user = $request->user();
        if (!$user || $user->role !== 'admin_sectoriel' || !$user->adminSectorielle) {
            abort(403, 'Accès interdit.');
        }
        return $user->adminSectorielle->secteur;
    }

    /**
     * Activer un producteur du secteur.
     */
    public function activerCompte($id)
    {
        try {
            $sector = $this->getSector();

            $user = User::where('role', 'producteur')
                ->whereHas('producteur', function ($query) use ($sector) {
                    $query->where('secteur_travail', $sector);
                })
                ->findOrFail($id);

            $user->update(['statut' => 'actif']);

            return response()->json([
                'message' => 'Compte activé avec succès.',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur activation compte {$id} sectoriel: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de l\'activation du compte.'], 500);
        }
    }

    /**
     * Suspendre un producteur du secteur.
     */
    public function suspendreCompte($id)
    {
        try {
            $sector = $this->getSector();

            $user = User::where('role', 'producteur')
                ->whereHas('producteur', function ($query) use ($sector) {
                    $query->where('secteur_travail', $sector);
                })
                ->findOrFail($id);

            $user->update(['statut' => 'suspendu']);

            return response()->json([
                'message' => 'Compte suspendu avec succès.',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur suspension compte {$id} sectoriel: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la suspension du compte.'], 500);
        }
    }

    /**
     * Supprimer un producteur du secteur.
     */
    public function destroyCompte($id)
    {
        try {
            $sector = $this->getSector();

            $user = User::where('role', 'producteur')
                ->whereHas('producteur', function ($query) use ($sector) {
                    $query->where('secteur_travail', $sector);
                })
                ->findOrFail($id);

            $user->delete();

            return response()->json([
                'message' => 'Compte producteur supprimé avec succès.'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur suppression compte {$id} sectoriel: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la suppression du compte.'], 500);
        }
    }

    /* ==========================================
     * GESTION DES SOUS-CATÉGORIES
     * ========================================== */

    /**
     * Liste des sous-catégories du secteur.
     */
    public function indexSousCategories()
    {
        try {
            $sector = $this->getSector();

            $categorie = Categorie::where('nom', $sector)->first();
            if (!$categorie) {
                return response()->json([], 200);
            }

            $sousCategories = SousCategorie::where('categorie_id', $categorie->id)
                ->with(['categorie'])
                ->withCount('produits')
                ->get();

            return response()->json($sousCategories, 200);
        } catch (\Exception $e) {
            Log::error("Erreur sous-catégories sectorielles: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du chargement des sous-catégories.'], 500);
        }
    }

    /**
     * Créer une sous-catégorie dans son secteur.
     */
    public function storeSousCategorie(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $sector = $this->getSector();

            // S'assurer que la catégorie principale du secteur existe
            $categorie = Categorie::firstOrCreate(
                ['nom' => $sector],
                ['description' => "Catégorie principale pour le secteur " . $sector]
            );

            // Vérifier les doublons de nom dans cette même catégorie
            $exists = SousCategorie::where('categorie_id', $categorie->id)
                ->where('nom', $request->nom)
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'Une sous-catégorie avec ce nom existe déjà dans votre secteur.'], 422);
            }

            $sousCategorie = SousCategorie::create([
                'nom' => $request->nom,
                'description' => $request->description ?? '',
                'categorie_id' => $categorie->id,
            ]);

            return response()->json([
                'message' => 'Sous-catégorie créée avec succès.',
                'sous_categorie' => SousCategorie::withCount('produits')->find($sousCategorie->id)
            ], 201);
        } catch (\Exception $e) {
            Log::error("Erreur création sous-catégorie sectorielle: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la création de la sous-catégorie.'], 500);
        }
    }

    /**
     * Mettre à jour une sous-catégorie de son secteur.
     */
    public function updateSousCategorie(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $sector = $this->getSector();

            $categorie = Categorie::where('nom', $sector)->first();
            if (!$categorie) {
                return response()->json(['message' => 'Catégorie de secteur introuvable.'], 404);
            }

            $sousCategorie = SousCategorie::where('categorie_id', $categorie->id)->findOrFail($id);

            // Vérifier les doublons de nom (hors celle en cours de modification)
            $exists = SousCategorie::where('categorie_id', $categorie->id)
                ->where('nom', $request->nom)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'Une autre sous-catégorie avec ce nom existe déjà.'], 422);
            }

            $sousCategorie->update([
                'nom' => $request->nom,
                'description' => $request->description ?? '',
            ]);

            return response()->json([
                'message' => 'Sous-catégorie mise à jour avec succès.',
                'sous_categorie' => SousCategorie::withCount('produits')->find($sousCategorie->id)
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur modification sous-catégorie {$id} sectorielle: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la modification.'], 500);
        }
    }

    /**
     * Supprimer une sous-catégorie de son secteur.
     */
    public function destroySousCategorie($id)
    {
        try {
            $sector = $this->getSector();

            $categorie = Categorie::where('nom', $sector)->first();
            if (!$categorie) {
                return response()->json(['message' => 'Catégorie de secteur introuvable.'], 404);
            }

            $sousCategorie = SousCategorie::where('categorie_id', $categorie->id)->findOrFail($id);

            // Sécurité : Ne pas pouvoir supprimer une sous-catégorie qui contient déjà des produits
            if ($sousCategorie->produits()->exists()) {
                return response()->json([
                    'message' => 'Impossible de supprimer cette sous-catégorie car elle contient des produits.'
                ], 400);
            }

            $sousCategorie->delete();

            return response()->json([
                'message' => 'Sous-catégorie supprimée avec succès.'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur suppression sous-catégorie {$id} sectorielle: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la suppression.'], 500);
        }
    }

    /* ==========================================
     * VÉRIFICATION DES DOCUMENTS PRODUCTEURS
     * ========================================== */

    public function comptesEnAttenteCount()
    {
        try {
            $sector = $this->getSector();
            $count = User::where('role', 'producteur')
                ->where('statut', 'en_attente')
                ->whereHas('producteur', function ($query) use ($sector) {
                    $query->where('secteur_travail', $sector);
                })
                ->count();
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    public function comptesEnAttente()
    {
        try {
            $sector = $this->getSector();

            $users = User::with('producteur.documents')
                ->where('role', 'producteur')
                ->where('statut', 'en_attente')
                ->whereHas('producteur', function ($query) use ($sector) {
                    $query->where('secteur_travail', $sector);
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    $totalDocs = $user->producteur?->documents->count() ?? 0;
                    $verifiedDocs = $user->producteur?->documents->where('statut_verification', 'verifie')->count() ?? 0;
                    $pendingDocs = $user->producteur?->documents->where('statut_verification', 'en_attente')->count() ?? 0;
                    $rejectedDocs = $user->producteur?->documents->where('statut_verification', 'rejete')->count() ?? 0;

                    return [
                        'id' => $user->id,
                        'nom' => $user->nom,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'telephone' => $user->telephone,
                        'statut' => $user->statut,
                        'created_at' => $user->created_at,
                        'secteur_travail' => $user->producteur?->secteur_travail,
                        'documents' => [
                            'total' => $totalDocs,
                            'verifies' => $verifiedDocs,
                            'en_attente' => $pendingDocs,
                            'rejetes' => $rejectedDocs,
                        ],
                    ];
                });

            return response()->json([
                'secteur' => $sector,
                'comptes' => $users,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur comptes en attente sectoriel: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des comptes en attente.'], 500);
        }
    }

    public function indexDocuments($producteurId)
    {
        try {
            $sector = $this->getSector();

            $user = User::where('role', 'producteur')
                ->whereHas('producteur', function ($query) use ($sector) {
                    $query->where('secteur_travail', $sector);
                })
                ->findOrFail($producteurId);

            $documents = DocumentProducteur::where('producteur_id', $producteurId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'documents' => $documents,
                'producteur' => [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'statut' => $user->statut,
                    'secteur_travail' => $user->producteur?->secteur_travail,
                ],
                'types' => DocumentProducteur::types(),
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur index documents sectoriel: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des documents.'], 500);
        }
    }

    public function verifyDocument($id)
    {
        try {
            $sector = $this->getSector();

            $document = DocumentProducteur::with('producteur.user')
                ->findOrFail($id);

            if ($document->producteur->user->role !== 'producteur') {
                return response()->json(['message' => 'Document invalide.'], 404);
            }

            if ($document->producteur->secteur_travail !== $sector) {
                return response()->json(['message' => 'Ce document ne fait pas partie de votre secteur.'], 403);
            }

            $document->update(['statut_verification' => 'verifie', 'motif_rejet' => null]);

            return response()->json([
                'message' => 'Document vérifié avec succès.',
                'document' => $document->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur vérification document {$id} sectoriel: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la vérification du document.'], 500);
        }
    }

    public function rejectDocument(Request $request, $id)
    {
        $request->validate([
            'motif_rejet' => 'required|string|min:3|max:500',
        ]);

        try {
            $sector = $this->getSector();

            $document = DocumentProducteur::with('producteur.user')
                ->findOrFail($id);

            if ($document->producteur->user->role !== 'producteur') {
                return response()->json(['message' => 'Document invalide.'], 404);
            }

            if ($document->producteur->secteur_travail !== $sector) {
                return response()->json(['message' => 'Ce document ne fait pas partie de votre secteur.'], 403);
            }

            $document->update([
                'statut_verification' => 'rejete',
                'motif_rejet' => $request->motif_rejet,
            ]);

            return response()->json([
                'message' => 'Document rejeté.',
                'document' => $document->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur rejet document {$id} sectoriel: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du rejet du document.'], 500);
        }
    }
}
