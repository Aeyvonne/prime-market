<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Commande;
use App\Models\Livraison;
use App\Models\Paiement;
use App\Models\Evaluation;
use App\Models\LigneCommande;
use App\Models\User;
use App\Services\PaiementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConsommateurController extends Controller
{
    /* -------------------------------------------------------------------
     | CATALOGUE
     | ------------------------------------------------------------------- */

    public function catalogue(Request $request)
    {
        try {
            $query = Produit::with(['sousCategorie.categorie', 'producteur.user'])
                ->where('status', 'Disponible');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('categorie_id')) {
                $query->whereHas('sousCategorie', function ($q) use ($request) {
                    $q->where('categorie_id', $request->categorie_id);
                });
            }

            $produits = $query->orderBy('created_at', 'desc')->get();

            return response()->json($produits, 200);
        } catch (\Exception $e) {
            Log::error("Erreur catalogue: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du chargement du catalogue'], 500);
        }
    }

    public function showProduit($id)
    {
        try {
            $produit = Produit::with([
                'sousCategorie.categorie',
                'producteur.user',
                'producteur' => function ($q) {
                    $q->with('user:id,nom,prenom,telephone');
                },
            ])->findOrFail($id);

            $data = [
                'id' => $produit->id,
                'nom' => $produit->nom,
                'description' => $produit->description,
                'prix' => $produit->prix,
                'photo' => $produit->photo,
                'quantite' => $produit->quantite,
                'status' => $produit->status,
                'categorie' => $produit->sousCategorie->categorie->nom ?? null,
                'sous_categorie' => $produit->sousCategorie->nom ?? null,
                'producteur' => $produit->producteur ? [
                    'nom' => $produit->producteur->user->nom ?? 'Inconnu',
                    'prenom' => $produit->producteur->user->prenom ?? '',
                    'telephone' => $produit->producteur->user->telephone ?? '',
                    'adresse' => $produit->producteur->adresse ?? 'Non renseignée',
                    'secteur_travail' => $produit->producteur->secteur_travail ?? 'Non spécifié',
                ] : null,
            ];

            return response()->json($data, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        } catch (\Exception $e) {
            Log::error("Erreur showProduit: " . $e->getMessage());
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }

    /* -------------------------------------------------------------------
     | COMMANDES
     | ------------------------------------------------------------------- */

    public function indexCommandes(Request $request)
    {
        try {
            $commandes = Commande::with(['lignesCommande.produit', 'vendeur', 'paiement', 'livraison'])
                ->where('acheteur_id', $request->user()->id)
                ->orderBy('date_commande', 'desc')
                ->get();

            return response()->json($commandes, 200);
        } catch (\Exception $e) {
            Log::error("Erreur indexCommandes: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des commandes'], 500);
        }
    }

    public function storeCommande(Request $request)
    {
        try {
            $data = $request->validate([
                'vendeur_id'         => 'nullable|exists:users,id',
                'items'              => 'required|array|min:1',
                'items.*.produit_id' => 'required|exists:produits,id',
                'items.*.quantite'   => 'required|integer|min:1',
                'mode_livraison'     => 'nullable|string',
                'adresse_livraison'  => 'nullable|string',
            ]);

            return DB::transaction(function () use ($data, $request) {
                $total = 0;
                $lignes = [];
                $vendeurId = $data['vendeur_id'] ?? null;

                foreach ($data['items'] as $item) {
                    $produit = Produit::lockForUpdate()->findOrFail($item['produit_id']);

                    if ($produit->quantite < $item['quantite']) {
                        return response()->json([
                            'message' => "Stock insuffisant pour le produit : {$produit->nom}",
                        ], 400);
                    }

                    // Auto-détecter le vendeur depuis le premier produit
                    if ($vendeurId === null) {
                        if ($produit->producteur_id) {
                            $vendeurId = $produit->producteur_id;
                        } elseif ($produit->proprietaire_type === 'distributeur' && $produit->proprietaire_id) {
                            $vendeurId = $produit->proprietaire_id;
                        }

                        if ($vendeurId === null) {
                            return response()->json(['message' => "Impossible de déterminer le vendeur pour {$produit->nom}."], 400);
                        }
                    }

                    $subtotal = $produit->prix * $item['quantite'];
                    $total += $subtotal;

                    $lignes[] = [
                        'produit_id'    => $produit->id,
                        'quantite'      => $item['quantite'],
                        'prix_unitaire' => $produit->prix,
                        'prix_total'    => $subtotal,
                    ];

                    $produit->decrement('quantite', $item['quantite']);
                }

                $commande = Commande::create([
                    'acheteur_id'       => $request->user()->id,
                    'vendeur_id'        => $vendeurId,
                    'montant_total'     => $total,
                    'mode_livraison'    => $data['mode_livraison'] ?? 'Standard',
                    'adresse_livraison' => $data['adresse_livraison'] ?? $request->user()->adresse,
                    'status'            => 'EnCours',
                    'date_commande'     => now(),
                ]);

                $commande->lignesCommande()->createMany($lignes);

                return response()->json([
                    'message'  => 'Commande créée avec succès',
                    'commande' => $commande->load('lignesCommande.produit'),
                ], 201);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Erreur storeCommande: " . $e->getMessage());
            return response()->json(['message' => "Erreur lors de la création de la commande"], 500);
        }
    }

    public function showCommande($id)
    {
        try {
            $commande = Commande::with([
                'lignesCommande.produit.sousCategorie',
                'vendeur:id,nom,prenom',
                'acheteur:id,nom,prenom',
                'paiement',
                'livraison.mission.transporteur.user:id,nom,prenom',
            ])->findOrFail($id);

            if ($commande->acheteur_id !== request()->user()->id) {
                return response()->json(['message' => 'Accès non autorisé à cette commande'], 403);
            }

            return response()->json($commande, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        } catch (\Exception $e) {
            Log::error("Erreur showCommande: " . $e->getMessage());
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }

    public function annulerCommande($id)
    {
        try {
            $commande = Commande::where('acheteur_id', request()->user()->id)->findOrFail($id);

            if ($commande->status !== 'EnCours') {
                return response()->json(['message' => 'Cette commande ne peut plus être annulée'], 400);
            }

            DB::transaction(function () use ($commande) {
                foreach ($commande->lignesCommande as $ligne) {
                    Produit::where('id', $ligne->produit_id)->increment('quantite', $ligne->quantite);
                }

                $commande->update(['status' => 'Annuler']);

                if ($commande->livraison) {
                    $commande->livraison->update(['status' => 'Annuler']);
                }
            });

            return response()->json(['message' => 'Commande annulée avec succès'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        } catch (\Exception $e) {
            Log::error("Erreur annulerCommande: " . $e->getMessage());
            return response()->json(['message' => "Erreur lors de l'annulation"], 500);
        }
    }

    public function confirmerReception($id)
    {
        try {
            $commande = Commande::where('acheteur_id', request()->user()->id)->findOrFail($id);

            if ($commande->status !== 'Valider') {
                return response()->json(['message' => 'Seules les commandes validées peuvent être confirmées'], 400);
            }

            if (!$commande->livraison) {
                return response()->json(['message' => 'Aucune livraison associée à cette commande'], 400);
            }

            $commande->livraison->update([
                'status'                => 'Valider',
                'date_livraison_reelle' => now(),
            ]);

            $commande->update(['status' => 'Livrer']);

            return response()->json(['message' => 'Réception confirmée avec succès'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        } catch (\Exception $e) {
            Log::error("Erreur confirmerReception: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la confirmation'], 500);
        }
    }

    /* -------------------------------------------------------------------
     | PAIEMENTS
     | ------------------------------------------------------------------- */

    public function indexPaiements(Request $request)
    {
        try {
            $paiements = Paiement::with('commande')
                ->whereHas('commande', function ($query) use ($request) {
                    $query->where('acheteur_id', $request->user()->id);
                })
                ->orderBy('date_transaction', 'desc')
                ->get();

            return response()->json($paiements, 200);
        } catch (\Exception $e) {
            Log::error("Erreur indexPaiements: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des paiements'], 500);
        }
    }

    public function storePaiement(Request $request)
    {
        try {
            $data = $request->validate([
                'commande_id'      => 'required|exists:commandes,id',
                'methode_paiement' => 'required|in:Wave,OrangeMoney',
            ]);

            $commande = Commande::where('acheteur_id', $request->user()->id)->findOrFail($data['commande_id']);

            if ($commande->paiement) {
                return response()->json(['message' => 'Cette commande a déjà été payée'], 400);
            }

            if (!in_array($commande->status, ['EnCours', 'Valider'])) {
                return response()->json(['message' => 'Cette commande ne peut pas être payée'], 400);
            }

            // Créer le paiement en attente
            $paiement = Paiement::create([
                'commande_id'      => $data['commande_id'],
                'methode_paiement' => $data['methode_paiement'],
                'montant'          => $commande->montant_total,
                'status'           => 'EnCours',
                'date_transaction' => now(),
            ]);

            // Appeler le service de paiement
            $service = new PaiementService();

            $result = $data['methode_paiement'] === 'Wave'
                ? $service->initierWave($commande->montant_total, $commande->id)
                : $service->initierOrangeMoney($commande->montant_total, $commande->id);

            if (!$result['success']) {
                $paiement->update(['status' => 'Echoué']);
                return response()->json([
                    'message' => 'Erreur lors de l\'initialisation du paiement',
                ], 500);
            }

            // Sauvegarder la session_id
            if (!empty($result['session_id'])) {
                $paiement->update(['session_id' => $result['session_id']]);
            }

            return response()->json([
                'message'      => 'Paiement initié',
                'redirect_url' => $result['redirect_url'],
                'paiement'     => $paiement->fresh()->load('commande'),
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        } catch (\Exception $e) {
            Log::error("Erreur storePaiement: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du paiement'], 500);
        }
    }

    /* -------------------------------------------------------------------
     | LIVRAISONS
     | ------------------------------------------------------------------- */

    public function indexLivraisons(Request $request)
    {
        try {
            $livraisons = Livraison::with(['commande.lignesCommande.produit.sousCategorie.categorie', 'mission.transporteur.user:id,nom,prenom'])
                ->whereHas('commande', function ($query) use ($request) {
                    $query->where('acheteur_id', $request->user()->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($livraisons, 200);
        } catch (\Exception $e) {
            Log::error("Erreur indexLivraisons: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des livraisons'], 500);
        }
    }

    public function showLivraison($id)
    {
        try {
            $livraison = Livraison::with([
                'commande.lignesCommande.produit.sousCategorie.categorie',
                'commande.vendeur:id,nom,prenom',
                'mission.transporteur.user:id,nom,prenom',
            ])->findOrFail($id);

            if ($livraison->commande->acheteur_id !== request()->user()->id) {
                return response()->json(['message' => 'Accès non autorisé à cette livraison'], 403);
            }

            return response()->json($livraison, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Livraison non trouvée'], 404);
        } catch (\Exception $e) {
            Log::error("Erreur showLivraison: " . $e->getMessage());
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }

    /* -------------------------------------------------------------------
     | ÉVALUATIONS
     | ------------------------------------------------------------------- */

    public function indexEvaluations(Request $request)
    {
        try {
            $evaluations = Evaluation::with([
                'evalue:id,nom,prenom',
                'evaluateur:id,nom,prenom',
                'commande:id,montant_total,date_commande',
            ])
                ->where('evaluateur_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($evaluations, 200);
        } catch (\Exception $e) {
            Log::error("Erreur indexEvaluations: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des évaluations'], 500);
        }
    }

    public function storeEvaluation(Request $request)
    {
        try {
            $data = $request->validate([
                'evalue_id'   => 'required|exists:users,id',
                'commande_id' => 'required|exists:commandes,id',
                'note'        => 'required|integer|min:1|max:5',
                'commentaire' => 'nullable|string|max:1000',
            ]);

            User::whereHas('distributeur')->findOrFail($data['evalue_id']);

            // Vérifier que la commande appartient bien au consommateur
            $commande = Commande::where('id', $data['commande_id'])
                ->where('acheteur_id', $request->user()->id)
                ->first();

            if (!$commande) {
                return response()->json(['message' => 'Commande introuvable'], 404);
            }

            $existing = Evaluation::where('evaluateur_id', $request->user()->id)
                ->where('evalue_id', $data['evalue_id'])
                ->where('commande_id', $data['commande_id'])
                ->first();

            if ($existing) {
                return response()->json(['message' => 'Vous avez déjà évalué ce distributeur pour cette commande'], 409);
            }

            $evaluation = Evaluation::create([
                'note'          => $data['note'],
                'commentaire'   => $data['commentaire'] ?? null,
                'date'          => now(),
                'evaluateur_id' => $request->user()->id,
                'evalue_id'     => $data['evalue_id'],
                'commande_id'   => $data['commande_id'],
            ]);

            return response()->json([
                'message'    => 'Évaluation enregistrée avec succès',
                'evaluation' => $evaluation->load(['evaluateur:id,nom,prenom', 'evalue:id,nom,prenom', 'commande:id,montant_total,date_commande']),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Distributeur non trouvé'], 404);
        } catch (\Exception $e) {
            Log::error("Erreur storeEvaluation: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de l\'enregistrement de l\'évaluation'], 500);
        }
    }

    public function commandesEvaluables(Request $request)
    {
        try {
            $commandes = Commande::with([
                'lignesCommande.produit:id,nom',
                'vendeur:id,nom,prenom',
            ])
                ->where('acheteur_id', $request->user()->id)
                ->whereIn('status', ['Livrer'])
                ->orderBy('date_commande', 'desc')
                ->get();

            $evaluations = Evaluation::where('evaluateur_id', $request->user()->id)
                ->whereIn('commande_id', $commandes->pluck('id'))
                ->get()
                ->keyBy('commande_id');

            $result = $commandes->map(function ($commande) use ($evaluations) {
                $ev = $evaluations->get($commande->id);
                return [
                    'id'                => $commande->id,
                    'montant_total'     => $commande->montant_total,
                    'date_commande'     => $commande->date_commande,
                    'vendeur_id'        => $commande->vendeur_id,
                    'vendeur_nom'       => $commande->vendeur?->nom ?? 'Inconnu',
                    'vendeur_prenom'    => $commande->vendeur?->prenom ?? '',
                    'produits'          => $commande->lignesCommande->map(fn($l) => [
                        'nom' => $l->produit?->nom ?? 'Produit',
                    ]),
                    'evaluation'        => $ev ? [
                        'id'    => $ev->id,
                        'note'  => $ev->note,
                        'commentaire' => $ev->commentaire,
                    ] : null,
                ];
            });

            return response()->json($result, 200);
        } catch (\Exception $e) {
            Log::error("Erreur commandesEvaluables: " . $e->getMessage());
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }
}
