<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            $commandes = Commande::with([
                'lignesCommande.produit',
                'paiement',
                'livraison.mission',
                'acheteur',
                'vendeur',
            ])
                ->where('acheteur_id', $request->user()->id)
                ->orderBy('date_commande', 'desc')
                ->get();

            return response()->json($commandes);
        } catch (\Exception $e) {
            Log::error('Erreur OrderController.index: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du chargement des commandes.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $commande = Commande::with([
                'lignesCommande.produit',
                'paiement',
                'livraison.mission',
                'acheteur',
                'vendeur',
            ])->findOrFail($id);

            return response()->json($commande);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Commande non trouvée.'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur OrderController.show: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du chargement de la commande.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'vendeur_id'     => 'required|exists:users,id',
                'items'          => 'required|array|min:1',
                'items.*.produit_id' => 'required|exists:produits,id',
                'items.*.quantite'   => 'required|integer|min:1',
                'mode_livraison' => 'nullable|string',
                'methode_paiement' => 'nullable|in:Wave,OrangeMoney',
            ]);

            return DB::transaction(function () use ($data, $request) {
                $total = 0;
                $lignes = [];

                foreach ($data['items'] as $item) {
                    $produit = Produit::lockForUpdate()->findOrFail($item['produit_id']);

                    if ($produit->quantite < $item['quantite']) {
                        throw new \Exception("Stock insuffisant pour le produit : {$produit->nom}");
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
                    'acheteur_id'   => $request->user()->id,
                    'vendeur_id'    => $data['vendeur_id'],
                    'montant_total' => $total,
                    'mode_livraison' => $data['mode_livraison'] ?? null,
                    'status'        => 'EnCours',
                    'date_commande' => now(),
                ]);

                $commande->lignesCommande()->createMany($lignes);

                if (!empty($data['methode_paiement'])) {
                    $commande->paiement()->create([
                        'methode_paiement' => $data['methode_paiement'],
                        'montant'          => $total,
                        'status'           => 'Valider',
                        'date_transaction' => now(),
                    ]);
                }

                return response()->json([
                    'message'  => 'Commande créée avec succès.',
                    'commande' => $commande->load('lignesCommande.produit', 'paiement'),
                ], 201);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur OrderController.store: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'status' => 'required|in:EnCours,Valider,Livrer,Annuler',
            ]);

            $commande = DB::transaction(function () use ($data, $id) {
                $commande = Commande::findOrFail($id);
                $commande->update($data);
                return $commande->fresh();
            });

            return response()->json([
                'message'  => 'Statut mis à jour avec succès.',
                'commande' => $commande,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Commande non trouvée.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur OrderController.updateStatus: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la mise à jour du statut.'], 500);
        }
    }
}
