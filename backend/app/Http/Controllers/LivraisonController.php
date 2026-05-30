<?php

namespace App\Http\Controllers;

use App\Models\Livraison;
use App\Models\Commande;
use Illuminate\Http\Request;

class LivraisonController extends Controller
{
    /**
     * Liste des livraisons liées à l'utilisateur
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            // On récupère les livraisons dont l'utilisateur est soit acheteur soit vendeur de la commande
            $livraisons = Livraison::with(['commande.acheteur', 'commande.vendeur'])
                ->whereHas('commande', function($query) use ($user) {
                    $query->where('acheteur_id', $user->id)
                          ->orWhere('vendeur_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($livraisons, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des livraisons'], 500);
        }
    }

    /**
     * Détails d'une livraison
     */
    public function show($id)
    {
        try {
            $livraison = Livraison::with(['commande.lignesCommande.produit', 'mission.transporteur.user'])
                ->findOrFail($id);
            return response()->json($livraison, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Livraison non trouvée'], 404);
        }
    }
}
