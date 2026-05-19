<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\Livraison;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

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
        $commandeController = new CommandeController();
        return $commandeController->store($request);
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
            Livraison::with('commande')
                ->whereIn('commande_id', $commandesIds)
                ->get()
        );
    }

    /**
     * Détails d'une livraison
     */
    public function showLivraison($id)
    {
        $livraison = Livraison::with(['commande.lignesCommande.produit', 'mission.transporteur.user'])
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
