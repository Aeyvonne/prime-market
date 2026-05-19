<?php
namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Categorie;
use App\Models\SousCategorie;
use Illuminate\Http\Request;

class ProducteurController extends Controller
{
    // Liste des produits du producteur connecté
    public function index(Request $request)
    {
        $produits = Produit::with('sousCategorie')
            ->where('producteur_id', $request->user()->id)
            ->get();
        return response()->json($produits);
    }

    // Ajouter un produit
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'              => 'required|string|max:255',
            'description'      => 'nullable|string',
            'prix'             => 'required|numeric|min:0',
            'quantite'         => 'required|integer|min:0',
            'sous_categorie_id'=> 'required|exists:sous_categories,id',
        ]);

        $data['producteur_id'] = $request->user()->id;
        $data['status'] = 'actif';
        $produit = Produit::create($data);

        return response()->json($produit, 201);
    }

    // Modifier un produit
    public function update(Request $request, $id)
    {
        $produit = Produit::where('id', $id)
            ->where('producteur_id', $request->user()->id)
            ->firstOrFail();

        $produit->update($request->all());
        return response()->json($produit);
    }

    // Supprimer un produit
    public function destroy(Request $request, $id)
    {
        $produit = Produit::where('id', $id)
            ->where('producteur_id', $request->user()->id)
            ->firstOrFail();

        $produit->delete();
        return response()->json(['message' => 'Produit supprimé']);
    }

    // Commandes reçues
    public function commandes(Request $request)
    {
        return response()->json([]);
    }

    // Liste des catégories
    public function categories()
    {
        return response()->json(Categorie::all());
    }

    // Liste des sous-catégories
    public function sousCategories()
    {
        return response()->json(SousCategorie::all());
    }
}