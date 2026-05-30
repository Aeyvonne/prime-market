<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProduitController extends Controller
{
    /**
     * Liste de tous les produits disponibles (Catalogue public)
     */
    public function index()
    {
        try {
            $produits = Produit::with(['sousCategorie.categorie', 'producteur.user', 'distributeur.user'])
                ->where('status', 'Disponible')
                ->get();
            return response()->json($produits, 200);
        } catch (\Exception $e) {
            Log::error("Erreur index produits: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des produits'], 500);
        }
    }

    /**
     * Liste des produits appartenant à l'utilisateur connecté (Producteur/Distributeur)
     */
    public function mesProduits(Request $request)
    {
        try {
            $user = $request->user();
            $query = Produit::with('sousCategorie');
            
            if ($user && $user->role === 'distributeur') {
                $query->where('proprietaire_type', 'distributeur')
                      ->where('proprietaire_id', $user->id);
            } else {
                $query->where('producteur_id', $user->id);
            }
            
            $produits = $query->get();
            return response()->json($produits, 200);
        } catch (\Exception $e) {
            Log::error("Erreur mesProduits: " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération de vos produits'], 500);
        }
    }

    /**
     * Détails d'un produit
     */
    public function show($id)
    {
        try {
            $produit = Produit::with(['sousCategorie.categorie', 'producteur.user', 'distributeur.user'])->findOrFail($id);
            return response()->json($produit, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Produit non trouvé'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Ajouter un produit
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nom'               => 'required|string|max:255',
                'description'       => 'nullable|string',
                'prix'              => 'required|numeric|min:0',
                'quantite'          => 'required|integer|min:0',
                'sous_categorie_id' => 'required|exists:sous_categories,id',
                'photo'             => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            // Gestion de la photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('products', 'public');
                $data['photo'] = basename($path);
            }

            $data['status'] = 'Disponible';
            
            if ($request->user()->role === 'distributeur') {
                $data['producteur_id'] = null;
                $data['proprietaire_type'] = 'distributeur';
                $data['proprietaire_id'] = $request->user()->id;
            } else {
                $data['producteur_id'] = $request->user()->id;
            }
            
            $produit = Produit::create($data);

            return response()->json([
                'message' => 'Produit ajouté avec succès',
                'produit' => $produit
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Erreur store produit: " . $e->getMessage());
            return response()->json(['message' => "Erreur lors de l'ajout du produit"], 500);
        }
    }

    /**
     * Modifier un produit
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            $query = Produit::query();
            
            if ($user && $user->role === 'distributeur') {
                $query->where('proprietaire_type', 'distributeur')
                      ->where('proprietaire_id', $user->id);
            } else {
                $query->where('producteur_id', $user->id);
            }
            
            $produit = $query->findOrFail($id);
            
            $data = $request->validate([
                'nom'               => 'sometimes|string|max:255',
                'description'       => 'sometimes|nullable|string',
                'prix'              => 'sometimes|numeric|min:0',
                'quantite'          => 'sometimes|integer|min:0',
                'sous_categorie_id' => 'sometimes|exists:sous_categories,id',
                'photo'             => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048',
                'status'            => 'sometimes|in:Disponible,Indisponible',
            ]);

            // Gestion de la photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('products', 'public');
                $data['photo'] = basename($path);
            }

            $produit->update($data);

            return response()->json([
                'message' => 'Produit mis à jour avec succès',
                'produit' => $produit
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Produit non trouvé ou non autorisé'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la modification'], 500);
        }
    }

    /**
     * Supprimer un produit
     */
    public function destroy($id)
    {
        try {
            $user = request()->user();
            $query = Produit::query();
            
            if ($user && $user->role === 'distributeur') {
                $query->where('proprietaire_type', 'distributeur')
                      ->where('proprietaire_id', $user->id);
            } else if ($user) {
                $query->where('producteur_id', $user->id);
            }
            
            $produit = $query->findOrFail($id);
            $produit->delete();
            return response()->json(['message' => 'Produit supprimé avec succès'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Produit non trouvé ou non autorisé'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression'], 500);
        }
    }
}
