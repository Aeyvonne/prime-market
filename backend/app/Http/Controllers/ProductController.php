<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $produits = Produit::with([
                'sousCategorie.categorie',
                'producteur.user',
                'distributeur.user',
            ])->orderBy('created_at', 'desc')->get();

            return response()->json($produits);
        } catch (\Exception $e) {
            Log::error('Erreur ProductController.index: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du chargement des produits.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $produit = Produit::with([
                'sousCategorie.categorie',
                'producteur.user',
                'distributeur.user',
            ])->findOrFail($id);

            return response()->json($produit);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Produit non trouvé.'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur ProductController.show: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du chargement du produit.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nom'              => 'required|string|max:255',
                'description'      => 'nullable|string',
                'prix'             => 'required|numeric|min:0',
                'quantite'         => 'required|integer|min:0',
                'poids'            => 'nullable|numeric|min:0',
                'photo'            => 'nullable|string',
                'status'           => 'nullable|in:Disponible,Indisponible',
                'sous_categorie_id' => 'required|exists:sous_categories,id',
                'producteur_id'    => 'nullable|exists:producteurs,id',
                'proprietaire_type' => 'nullable|string',
                'proprietaire_id'  => 'nullable|integer',
            ]);

            $produit = DB::transaction(function () use ($data) {
                return Produit::create($data);
            });

            return response()->json([
                'message' => 'Produit créé avec succès.',
                'produit' => $produit->load(['sousCategorie.categorie', 'producteur.user']),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur ProductController.store: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la création du produit.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'nom'              => 'sometimes|string|max:255',
                'description'      => 'nullable|string',
                'prix'             => 'sometimes|numeric|min:0',
                'quantite'         => 'sometimes|integer|min:0',
                'poids'            => 'nullable|numeric|min:0',
                'photo'            => 'nullable|string',
                'status'           => 'nullable|in:Disponible,Indisponible',
                'sous_categorie_id' => 'sometimes|exists:sous_categories,id',
            ]);

            $produit = DB::transaction(function () use ($data, $id) {
                $produit = Produit::findOrFail($id);
                $produit->update($data);
                return $produit->fresh(['sousCategorie.categorie', 'producteur.user']);
            });

            return response()->json([
                'message' => 'Produit mis à jour avec succès.',
                'produit' => $produit,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Produit non trouvé.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur ProductController.update: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la mise à jour du produit.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $produit = Produit::findOrFail($id);
                $produit->delete();
            });

            return response()->json(['message' => 'Produit supprimé avec succès.']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Produit non trouvé.'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur ProductController.destroy: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la suppression du produit.'], 500);
        }
    }
}
