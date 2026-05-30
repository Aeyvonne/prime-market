<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Commande;
use App\Models\Livraison;
use App\Models\Mission;
use App\Models\LigneCommande;
use App\Models\Paiement;
use App\Models\Categorie;
use App\Models\SousCategorie;
use App\Models\DocumentProducteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProducteurController extends Controller
{
    public function index(Request $request)
    {
        $produits = Produit::with('sousCategorie')
            ->where('producteur_id', $request->user()->id)
            ->get();
        return response()->json($produits);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'               => 'required|string|max:255',
            'description'       => 'nullable|string',
            'prix'              => 'required|numeric|min:0',
            'quantite'          => 'required|integer|min:0',
            'sous_categorie_id' => 'required|exists:sous_categories,id',
        ]);

        $data['producteur_id'] = $request->user()->id;
        $data['status'] = 'actif';
        $produit = Produit::create($data);

        return response()->json($produit, 201);
    }

    public function update(Request $request, $id)
    {
        $produit = Produit::where('id', $id)
            ->where('producteur_id', $request->user()->id)
            ->firstOrFail();

        $produit->update($request->all());
        return response()->json($produit);
    }

    public function destroy(Request $request, $id)
    {
        $produit = Produit::where('id', $id)
            ->where('producteur_id', $request->user()->id)
            ->firstOrFail();

        $produit->delete();
        return response()->json(['message' => 'Produit supprimé']);
    }

    /* -------------------------------------------------------------------
     | COMMANDES REÇUES
     | ------------------------------------------------------------------- */

    public function commandes(Request $request)
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
            Log::error('Erreur commandes producteur: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des commandes.'], 500);
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

            // Vérifier que le stock est suffisant (le stock est déjà réservé à la création de la commande)
            foreach ($commande->lignesCommande as $ligne) {
                $produit = $ligne->produit;
                if (!$produit) {
                    DB::rollBack();
                    return response()->json(['message' => "Produit #{$ligne->produit_id} introuvable."], 422);
                }
            }

            // Mettre à jour le statut de la commande
            $commande->update(['status' => 'Valider']);

            // Créer la livraison automatiquement
            $livraison = Livraison::create([
                'commande_id'           => $commande->id,
                'adresse_livraison'     => $commande->adresse_livraison ?? 'Adresse non spécifiée',
                'status'                => 'EnCours',
                'date_livraison_estimee' => now()->addDays(3),
            ]);

            // Créer la mission automatiquement (disponible pour les transporteurs)
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
            Log::error('Erreur confirmerCommande: ' . $e->getMessage());
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
            Log::error('Erreur refuserCommande: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du refus de la commande.'], 500);
        }
    }

    /* -------------------------------------------------------------------
     | VENTES
     | ------------------------------------------------------------------- */

    public function ventes(Request $request)
    {
        try {
            $commandes = Commande::with([
                'acheteur:id,nom,prenom',
                'lignesCommande.produit:id,nom',
            ])
                ->where('vendeur_id', $request->user()->id)
                ->whereIn('status', ['Valider', 'Livrer'])
                ->orderBy('date_commande', 'desc')
                ->get();

            $totalRevenu = $commandes->sum('montant_total');
            $totalVentes = $commandes->count();

            $result = [
                'total_revenu' => $totalRevenu,
                'total_ventes' => $totalVentes,
                'commandes'    => $commandes->map(function ($cmd) {
                    return [
                        'id'           => $cmd->id,
                        'montant_total'=> $cmd->montant_total,
                        'date_commande'=> $cmd->date_commande,
                        'status'       => $cmd->status,
                        'acheteur'     => $cmd->acheteur ? [
                            'nom'    => $cmd->acheteur->nom,
                            'prenom' => $cmd->acheteur->prenom,
                        ] : null,
                        'produits'     => $cmd->lignesCommande->map(fn($l) => [
                            'nom'      => $l->produit?->nom ?? 'Produit',
                            'quantite' => $l->quantite,
                            'prix'     => $l->prix_unitaire,
                        ]),
                    ];
                }),
            ];

            return response()->json($result, 200);
        } catch (\Exception $e) {
            Log::error("Erreur ventes producteur: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des ventes.'], 500);
        }
    }

    /* -------------------------------------------------------------------
     | PAIEMENTS REÇUS
     | ------------------------------------------------------------------- */

    public function paiements(Request $request)
    {
        try {
            $commandesIds = Commande::where('vendeur_id', $request->user()->id)->pluck('id');

            $paiements = Paiement::with([
                'commande.acheteur:id,nom,prenom',
                'commande.lignesCommande.produit:id,nom',
            ])
                ->whereIn('commande_id', $commandesIds)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalRecu = $paiements->where('status', 'Valider')->sum('montant');

            return response()->json([
                'total_recu' => $totalRecu,
                'paiements'  => $paiements,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erreur paiements producteur: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des paiements.'], 500);
        }
    }

    public function categories()
    {
        return response()->json(Categorie::all());
    }

    public function sousCategories()
    {
        return response()->json(SousCategorie::all());
    }

    /* -------------------------------------------------------------------
     | DOCUMENTS JUSTIFICATIFS
     | ------------------------------------------------------------------- */

    public function indexDocuments(Request $request)
    {
        $documents = DocumentProducteur::where('producteur_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'documents' => $documents,
            'types' => DocumentProducteur::types(),
        ]);
    }

    public function uploadDocument(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:carte_identite,registre_commerce,certificat_agricole,attestation_terrain,photo_exploitation',
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $existing = DocumentProducteur::where('producteur_id', $request->user()->id)
            ->where('type', $data['type'])
            ->where('statut_verification', '!=', 'rejete')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Vous avez déjà un document de ce type en attente ou vérifié.',
            ], 422);
        }

        $path = $request->file('fichier')->store('documents', 'public');

        $document = DocumentProducteur::create([
            'producteur_id' => $request->user()->id,
            'type' => $data['type'],
            'fichier' => $path,
            'statut_verification' => 'en_attente',
        ]);

        return response()->json([
            'message' => 'Document uploadé avec succès. En attente de vérification.',
            'document' => $document,
        ], 201);
    }

    public function deleteDocument(Request $request, $id)
    {
        $document = DocumentProducteur::where('id', $id)
            ->where('producteur_id', $request->user()->id)
            ->firstOrFail();

        if ($document->statut_verification === 'verifie') {
            return response()->json([
                'message' => 'Impossible de supprimer un document déjà vérifié.',
            ], 422);
        }

        Storage::disk('public')->delete($document->fichier);
        $document->delete();

        return response()->json([
            'message' => 'Document supprimé avec succès.',
        ]);
    }
}
