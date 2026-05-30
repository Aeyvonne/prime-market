<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\Livraison;
use App\Models\Paiement;
use App\Models\LigneCommande;
use App\Models\Mission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DistributeurController extends Controller
{
    /* -------------------------------------------------------------------
     | CATALOGUE (Achat auprès des producteurs)
     | ------------------------------------------------------------------- */

    /**
     * Consulter le catalogue des produits disponibles
     */
    public function catalogue()
    {
        return response()->json(Produit::with(['sousCategorie.categorie', 'producteur.user', 'distributeur.user'])
            ->where('status', 'Disponible')
            ->get());
    }

    /**
     * Détails d'un produit du catalogue
     */
    public function showCatalogueProduit($id)
    {
        $produit = Produit::with(['sousCategorie', 'producteur.user'])->findOrFail($id);
        return response()->json($produit);
    }

    /* -------------------------------------------------------------------
     | COMMANDES (Passées par le distributeur)
     | ------------------------------------------------------------------- */

    /**
     * Liste des commandes du distributeur
     */
    public function mesCommandes(Request $request)
    {
        return response()->json(
            Commande::with(['lignesCommande.produit', 'vendeur'])
                ->where('acheteur_id', $request->user()->id)
                ->orderBy('date_commande', 'desc')
                ->get()
        );
    }

    /**
     * Détails d'une commande
     */
    public function showCommande($id)
    {
        $commande = Commande::with(['lignesCommande.produit', 'vendeur', 'livraison', 'paiement'])
            ->findOrFail($id);
        return response()->json($commande);
    }

    /**
     * Passer une commande
     */
    public function passerCommande(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'vendeur_id'         => 'required|exists:users,id',
                'items'              => 'required|array',
                'items.*.produit_id' => 'required|exists:produits,id',
                'items.*.quantite'   => 'required|integer|min:1',
                'mode_livraison'     => 'nullable|string|in:Domicile,Retrait',
                'adresse_livraison'  => 'nullable|string',
                'methode_paiement'   => 'nullable|in:Wave,OrangeMoney',
            ]);

            if ($data['mode_livraison'] === 'Domicile' && empty($data['adresse_livraison'])) {
                return response()->json(['message' => "L'adresse de livraison est requise pour le mode Domicile."], 422);
            }

            $total = 0;
            $lignes = [];

            foreach ($data['items'] as $item) {
                $produit = Produit::lockForUpdate()->findOrFail($item['produit_id']);

                if ($produit->quantite < $item['quantite']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Stock insuffisant pour: {$produit->nom} (dispo: {$produit->quantite})",
                    ], 400);
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
                'vendeur_id'        => $data['vendeur_id'],
                'montant_total'     => $total,
                'mode_livraison'    => $data['mode_livraison'] ?? null,
                'adresse_livraison' => $data['adresse_livraison'] ?? null,
                'status'            => 'EnCours',
                'date_commande'     => now(),
            ]);

            $commande->lignesCommande()->createMany($lignes);

            // Créer le paiement si une méthode est fournie (la commande reste EnCours pour confirmation producteur)
            if (!empty($data['methode_paiement'])) {
                Paiement::create([
                    'commande_id'       => $commande->id,
                    'methode_paiement'  => $data['methode_paiement'],
                    'montant'           => $total,
                    'status'            => 'Valider',
                    'date_transaction'  => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Commande créée avec succès.',
                'commande' => $commande->load('lignesCommande.produit', 'paiement'),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur passerCommande: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Annuler une commande
     */
    public function annulerCommande($id)
    {
        $commandeController = new CommandeController();
        return $commandeController->annuler($id);
    }

    /* -------------------------------------------------------------------
     | VENTES (Commandes reçues en tant que vendeur)
     | ------------------------------------------------------------------- */

    public function ventes(Request $request)
    {
        try {
            $commandes = Commande::with([
                'acheteur',
                'lignesCommande.produit',
                'paiement',
                'livraison',
            ])
                ->where('vendeur_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($commandes);
        } catch (\Exception $e) {
            Log::error('Erreur ventes distributeur: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des ventes.'], 500);
        }
    }

    public function confirmerCommande(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $commande = Commande::with('lignesCommande.produit')
                ->where('vendeur_id', $request->user()->id)
                ->find($id);

            if (!$commande) {
                return response()->json(['message' => 'Commande non trouvée.'], 404);
            }

            if ($commande->status !== 'EnCours') {
                return response()->json(['message' => 'Seules les commandes en cours peuvent être confirmées.'], 400);
            }

            foreach ($commande->lignesCommande as $ligne) {
                $produit = $ligne->produit;
                if (!$produit) {
                    DB::rollBack();
                    return response()->json(['message' => "Produit #{$ligne->produit_id} introuvable."], 422);
                }
            }

            $commande->update(['status' => 'Valider']);

            $livraison = Livraison::create([
                'commande_id'           => $commande->id,
                'adresse_livraison'     => $commande->adresse_livraison ?? 'Adresse non spécifiée',
                'status'                => 'EnCours',
                'date_livraison_estimee' => now()->addDays(3),
            ]);

            $remuneration = $commande->montant_total * 0.1;

            Mission::create([
                'livraison_id'    => $livraison->id,
                'transporteur_id' => null,
                'status'          => 'EnCours',
                'remuneration'    => $remuneration,
            ]);

            DB::commit();

            $commande->load(['acheteur', 'lignesCommande.produit', 'paiement', 'livraison']);

            return response()->json([
                'message' => 'Commande confirmée avec succès. Livraison créée.',
                'commande' => $commande,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur confirmerCommande distributeur: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la confirmation de la commande.'], 500);
        }
    }

    public function refuserCommande(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $commande = Commande::with('lignesCommande.produit')
                ->where('vendeur_id', $request->user()->id)
                ->find($id);

            if (!$commande) {
                return response()->json(['message' => 'Commande non trouvée.'], 404);
            }

            if ($commande->status !== 'EnCours') {
                return response()->json(['message' => 'Seules les commandes en cours peuvent être refusées.'], 400);
            }

            // Restaurer le stock réservé
            foreach ($commande->lignesCommande as $ligne) {
                $produit = $ligne->produit;
                if ($produit) {
                    $produit->increment('quantite', $ligne->quantite);
                }
            }

            $data = ['status' => 'Annuler'];

            if ($request->filled('motif')) {
                $data['motif_annulation'] = $request->motif;
            }

            $commande->update($data);

            DB::commit();

            $commande->load(['acheteur', 'lignesCommande.produit', 'paiement', 'livraison']);

            return response()->json([
                'message' => 'Commande refusée.',
                'commande' => $commande,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur refuserCommande distributeur: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du refus de la commande.'], 500);
        }
    }

    /* -------------------------------------------------------------------
     | PRODUITS (Gestion du propre stock du distributeur pour revente)
     | ------------------------------------------------------------------- */

    /**
     * Liste des produits mis en vente par le distributeur
     */
    public function mesProduits(Request $request)
    {
        $produitController = new ProduitController();
        return $produitController->mesProduits($request);
    }

    /**
     * Ajouter un produit au catalogue de revente
     */
    public function storeProduit(Request $request)
    {
        $produitController = new ProduitController();
        return $produitController->store($request);
    }

    /**
     * Modifier un produit
     */
    public function updateProduit(Request $request, $id)
    {
        $produitController = new ProduitController();
        return $produitController->update($request, $id);
    }

    /**
     * Supprimer un produit
     */
    public function destroyProduit($id)
    {
        $produitController = new ProduitController();
        return $produitController->destroy($id);
    }

    /* -------------------------------------------------------------------
     | SUIVI ET PAIEMENTS
     | ------------------------------------------------------------------- */

    /**
     * Liste des livraisons liées aux achats du distributeur
     */
    public function livraisons(Request $request)
    {
        $commandesIds = Commande::where('acheteur_id', $request->user()->id)->pluck('id');
        
        return response()->json(
            Livraison::with(['commande.lignesCommande.produit.sousCategorie.categorie', 'mission'])
                ->whereIn('commande_id', $commandesIds)
                ->get()
        );
    }

    /**
     * Détails d'une livraison
     */
    public function showLivraison($id)
    {
        $livraison = Livraison::with(['commande.lignesCommande.produit.sousCategorie.categorie', 'mission.transporteur.user'])
            ->findOrFail($id);
        return response()->json($livraison);
    }

    /**
     * Liste des paiements effectués par le distributeur
     */
    public function paiements(Request $request)
    {
        $commandesIds = Commande::where('acheteur_id', $request->user()->id)->pluck('id');

        return response()->json(
            Paiement::with('commande')
                ->whereIn('commande_id', $commandesIds)
                ->get()
        );
    }
}
