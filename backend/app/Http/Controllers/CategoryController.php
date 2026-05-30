<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Categorie::with('sousCategories')->orderBy('nom')->get();
            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Erreur CategoryController.index: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du chargement des catégories.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $categorie = Categorie::with('sousCategories')->findOrFail($id);
            return response()->json($categorie);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Catégorie non trouvée.'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur CategoryController.show: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du chargement de la catégorie.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nom'         => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $categorie = DB::transaction(function () use ($data) {
                return Categorie::create($data);
            });

            return response()->json([
                'message'   => 'Catégorie créée avec succès.',
                'categorie' => $categorie,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur CategoryController.store: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la création de la catégorie.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'nom'         => 'sometimes|string|max:255',
                'description' => 'nullable|string',
            ]);

            $categorie = DB::transaction(function () use ($data, $id) {
                $categorie = Categorie::findOrFail($id);
                $categorie->update($data);
                return $categorie->fresh();
            });

            return response()->json([
                'message'   => 'Catégorie mise à jour avec succès.',
                'categorie' => $categorie,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Catégorie non trouvée.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur CategoryController.update: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la mise à jour.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $categorie = Categorie::withCount('sousCategories')->findOrFail($id);
                if ($categorie->sous_categories_count > 0) {
                    throw new \Exception('Impossible de supprimer une catégorie qui contient des sous-catégories.');
                }
                $categorie->delete();
            });

            return response()->json(['message' => 'Catégorie supprimée avec succès.']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Catégorie non trouvée.'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur CategoryController.destroy: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
