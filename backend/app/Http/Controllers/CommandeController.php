<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommandeController extends Controller
{
    /**
     * Liste des commandes de l'utilisateur
     */
    public function index(Request $request)
    {
        try {
            $commandes = Commande::with(['lignesCommande.produit', 'vendeur', 'acheteur'])
                 ->where('acheteur_id', $request->user()->id)
                 ->orWhere('vendeur_id', $request->user()->id)
                 ->orderBy('date_commande', 'desc')
                 ->get();
            return response()->json($commandes, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des commandes'], 500);
        }
    }

    /**
     * Détails d'une commande
     */
    public function show($id)
    {
        try {
            $commande = Commande::with(['lignesCommande.produit', 'vendeur', 'acheteur', 'livraison', 'paiement'])
                ->findOrFail($id);
            return response()->json($commande, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }
    }

    /**
     * Créer une commande
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'vendeur_id'         => 'required|exists:users,id',
                'items'              => 'required|array',
                'items.*.produit_id' => 'required|exists:produits,id',
                'items.*.quantite'   => 'required|integer|min:1',
                'mode_livraison'     => 'nullable|string',
                'adresse_livraison'  => 'nullable|string',
            ]);

            return DB::transaction(function () use ($data, $request) {
                $total = 0;
                $lignes = [];

                foreach ($data['items'] as $item) {
                    $produit = Produit::lockForUpdate()->findOrFail($item['produit_id']);
                    
                    if ($produit->quantite < $item['quantite']) {
                        throw new \Exception("Stock insuffisant pour le produit: " . $produit->nom);
                    }

                    $subtotal = $produit->prix * $item['quantite'];
                    $total += $subtotal;
                    
                    $lignes[] = [
                        'produit_id'    => $produit->id,
                        'quantite'      => $item['quantite'],
                        'prix_unitaire' => $produit->prix,
                        'prix_total'    => $subtotal,
                    ];

                    // Mise à jour du stock
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

                return response()->json([
                    'message' => 'Commande créée avec succès',
                    'commande' => $commande->load('lignesCommande.produit')
                ], 201);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Erreur store commande: " . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Annuler une commande
     */
    public function annuler($id)
    {
        try {
            $commande = Commande::findOrFail($id);

            if ($commande->status !== 'EnCours') {
                return response()->json(['message' => 'Cette commande ne peut plus être annulée'], 400);
            }

            DB::transaction(function () use ($commande) {
                // Restaurer les stocks
                foreach ($commande->lignesCommande as $ligne) {
                    Produit::where('id', $ligne->produit_id)->increment('quantite', $ligne->quantite);
                }

                $commande->update(['status' => 'Annuler']);
            });

            return response()->json(['message' => 'Commande annulée avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => "Erreur lors de l'annulation"], 500);
        }
    }
}
