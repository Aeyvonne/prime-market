<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Commande;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    /**
     * Liste des paiements liés à l'utilisateur
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            // On récupère les paiements dont l'utilisateur est soit acheteur soit vendeur de la commande
            $paiements = Paiement::with('commande')
                ->whereHas('commande', function($query) use ($user) {
                    $query->where('acheteur_id', $user->id)
                          ->orWhere('vendeur_id', $user->id);
                })
                ->orderBy('date_transaction', 'desc')
                ->get();

            return response()->json($paiements, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des paiements'], 500);
        }
    }
}
