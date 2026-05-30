<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\Producteur;
use App\Models\Distributeur;
use App\Models\Consommateur;
use App\Models\Transporteur;
use App\Models\SuperAdministrateur;
use App\Models\Paiement;
use App\Models\Commande;
use App\Models\AdminSectorielle;
use App\Models\DocumentProducteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    /* ==========================================
     * GESTION DES CATÉGORIES
     * ========================================== */

    public function indexCategories()
    {
        try {
            $categories = Categorie::with('sousCategories')->get();
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors du chargement des catégories'], 500);
        }
    }

    public function storeCategorie(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|unique:categories,nom|max:255',
            'description' => 'nullable|string',
            'sous_categories' => 'nullable|array',
            'sous_categories.*' => 'required|string|max:255',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $categorie = Categorie::create($request->only(['nom', 'description']));

                if ($request->has('sous_categories')) {
                    foreach ($request->input('sous_categories') as $subName) {
                        if (!empty($subName)) {
                            $categorie->sousCategories()->create([
                                'nom' => $subName,
                                'description' => ''
                            ]);
                        }
                    }
                }

                return response()->json([
                    'message' => 'Catégorie créée avec succès',
                    'categorie' => Categorie::with('sousCategories')->find($categorie->id)
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error("Erreur creation categorie: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la création'], 500);
        }
    }

    public function updateCategorie(Request $request, $id)
    {
        $categorie = Categorie::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|unique:categories,nom,' . $id . '|max:255',
            'description' => 'nullable|string',
            'sous_categories' => 'nullable|array',
            'sous_categories.*' => 'required|string|max:255',
        ]);

        try {
            return DB::transaction(function () use ($request, $categorie) {
                $categorie->update($request->only(['nom', 'description']));

                if ($request->has('sous_categories')) {
                    $newNames = array_filter(array_map('trim', $request->input('sous_categories')));
                    $existing = $categorie->sousCategories()->pluck('nom', 'id')->toArray();

                    // Supprimer celles qui ne sont plus dans la liste
                    foreach ($existing as $subId => $subName) {
                        if (!in_array($subName, $newNames)) {
                            $sub = $categorie->sousCategories()->find($subId);
                            if ($sub) {
                                $sub->delete();
                            }
                        }
                    }

                    // Ajouter les nouvelles
                    foreach ($newNames as $subName) {
                        if (!in_array($subName, $existing)) {
                            $categorie->sousCategories()->create([
                                'nom' => $subName,
                                'description' => ''
                            ]);
                        }
                    }
                }

                return response()->json([
                    'message' => 'Catégorie mise à jour avec succès',
                    'categorie' => Categorie::with('sousCategories')->find($categorie->id)
                ], 200);
            });
        } catch (\Exception $e) {
            Log::error("Erreur modification categorie: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la modification'], 500);
        }
    }

    public function destroyCategorie($id)
    {
        try {
            $categorie = Categorie::withCount('sousCategories')->findOrFail($id);

            DB::transaction(function () use ($categorie) {
                $sousCategorieIds = $categorie->sousCategories()->pluck('id');

                if ($sousCategorieIds->isNotEmpty()) {
                    Produit::whereIn('sous_categorie_id', $sousCategorieIds)
                        ->update(['sous_categorie_id' => null]);

                    $categorie->sousCategories()->delete();
                }

                $categorie->delete();
            });

            return response()->json([
                'message' => "Catégorie « {$categorie->nom} » et ses {$categorie->sous_categories_count} sous-catégories supprimées avec succès",
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression de la catégorie $id: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la suppression de la catégorie'], 500);
        }
    }

    /* ==========================================
     * GESTION DES COMPTES (USERS)
     * ========================================== */

    public function indexComptes()
    {
        try {
            $users = User::orderBy('created_at', 'desc')->get();
            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors du chargement des comptes'], 500);
        }
    }

    public function storeCompte(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'telephone' => 'required|string|unique:users,telephone',
            'role' => [
                'required',
                Rule::in(['producteur', 'distributeur', 'consommateur', 'transporteur', 'admin_sectoriel', 'super_administrateur'])
            ],
            'statut' => [
                'required',
                Rule::in(['actif', 'en_attente', 'suspendu'])
            ],
            'adresse' => 'nullable|string',
            // Profile spécifique
            'secteur_travail' => 'required_if:role,producteur|nullable|in:Agriculture,Elevage,Peche',
            'type_distributeur' => 'required_if:role,distributeur|nullable|in:Grossiste,Detaillant',
            'type_vehicule' => 'required_if:role,transporteur|nullable|in:Camion,Voiture,Moto',
            'zone_intervention' => 'nullable|string',
            'secteur' => 'required_if:role,admin_sectoriel|nullable|in:Agriculture,Elevage,Peche',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $user = User::create([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'telephone' => $request->telephone,
                    'role' => $request->role,
                    'statut' => $request->statut,
                    'adresse' => $request->adresse,
                ]);

                // Profils associés
                switch ($request->role) {
                    case 'producteur':
                        Producteur::create([
                            'id' => $user->id,
                            'secteur_travail' => $request->secteur_travail,
                            'adresse' => $request->adresse,
                        ]);
                        break;
                    case 'distributeur':
                        Distributeur::create([
                            'id' => $user->id,
                            'type_distributeur' => $request->type_distributeur,
                            'adresse' => $request->adresse,
                            'note' => 0,
                        ]);
                        break;
                    case 'consommateur':
                        Consommateur::create([
                            'id' => $user->id,
                            'adresse' => $request->adresse,
                        ]);
                        break;
                    case 'transporteur':
                        Transporteur::create([
                            'id' => $user->id,
                            'type_vehicule' => $request->type_vehicule,
                            'zone_intervention' => $request->zone_intervention,
                            'disponibilite' => true,
                        ]);
                        break;
                    case 'super_administrateur':
                        SuperAdministrateur::create([
                            'id' => $user->id,
                        ]);
                        break;
                    case 'admin_sectoriel':
                        AdminSectorielle::create([
                            'id' => $user->id,
                            'secteur' => $request->secteur,
                        ]);
                        break;
                }

                return response()->json([
                    'message' => 'Compte créé avec succès',
                    'user' => $user
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error("Erreur creation compte super-admin: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la création du compte'], 500);
        }
    }

    public function updateCompte(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'telephone' => 'sometimes|required|string|unique:users,telephone,' . $id,
            'statut' => 'sometimes|required|in:actif,en_attente,suspendu',
            'adresse' => 'nullable|string',
        ]);

        try {
            $data = $request->only(['nom', 'prenom', 'email', 'telephone', 'statut', 'adresse']);
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            return response()->json([
                'message' => 'Compte mis à jour avec succès',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la modification'], 500);
        }
    }

    public function destroyCompte($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Sécurité : Ne pas pouvoir supprimer son propre compte connecté
            if (auth()->id() === $user->id) {
                return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte admin'], 400);
            }

            $user->delete();
            return response()->json(['message' => 'Compte supprimé avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }

    /* ==========================================
     * STATISTIQUES GLOBALES
     * ========================================== */

    public function statistiques()
    {
        try {
            $total_comptes = User::count();
            $total_producteurs = User::where('role', 'producteur')->count();
            $total_distributeurs = User::where('role', 'distributeur')->count();
            $total_consommateurs = User::where('role', 'consommateur')->count();
            $total_transporteurs = User::where('role', 'transporteur')->count();

            $total_produits = Produit::count();
            $total_commandes = Commande::count();
            $total_transactions = Paiement::count();
            
            // Revenu global (somme des paiements validés)
            $revenu_global = Paiement::where('status', 'Valider')->sum('montant');

            return response()->json([
                'total_comptes' => $total_comptes,
                'total_producteurs' => $total_producteurs,
                'total_distributeurs' => $total_distributeurs,
                'total_consommateurs' => $total_consommateurs,
                'total_transporteurs' => $total_transporteurs,
                'total_produits' => $total_produits,
                'total_commandes' => $total_commandes,
                'total_transactions' => $total_transactions,
                'revenu_global' => $revenu_global,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des statistiques'], 500);
        }
    }

    /* ==========================================
     * TRANSACTIONS GLOBALES
     * ========================================== */

    public function transactions()
    {
        try {
            $transactions = Paiement::with(['commande.acheteur', 'commande.vendeur'])
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json($transactions, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des transactions'], 500);
        }
    }

    /* ==========================================
     * VÉRIFICATION DES DOCUMENTS PRODUCTEURS
     * ========================================== */

    public function comptesEnAttenteCount()
    {
        try {
            $count = User::where('role', 'producteur')
                ->where('statut', 'en_attente')
                ->count();
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    public function comptesEnAttente()
    {
        try {
            $users = User::with('producteur.documents')
                ->where('role', 'producteur')
                ->where('statut', 'en_attente')
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

            return response()->json($users, 200);
        } catch (\Exception $e) {
            Log::error("Erreur comptes en attente: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des comptes en attente.'], 500);
        }
    }

    public function indexDocuments($producteurId)
    {
        try {
            $user = User::with('producteur')->where('role', 'producteur')->findOrFail($producteurId);

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
            Log::error("Erreur index documents super-admin: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des documents.'], 500);
        }
    }

    public function verifyDocument($id)
    {
        try {
            $document = DocumentProducteur::with('producteur.user')->findOrFail($id);

            $document->update(['statut_verification' => 'verifie', 'motif_rejet' => null]);

            return response()->json([
                'message' => 'Document vérifié avec succès.',
                'document' => $document->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur vérification document {$id} super-admin: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la vérification du document.'], 500);
        }
    }

    public function rejectDocument(Request $request, $id)
    {
        $request->validate([
            'motif_rejet' => 'required|string|min:3|max:500',
        ]);

        try {
            $document = DocumentProducteur::with('producteur.user')->findOrFail($id);

            $document->update([
                'statut_verification' => 'rejete',
                'motif_rejet' => $request->motif_rejet,
            ]);

            return response()->json([
                'message' => 'Document rejeté.',
                'document' => $document->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur rejet document {$id} super-admin: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du rejet du document.'], 500);
        }
    }
}
