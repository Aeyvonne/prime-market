<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Commande;
use App\Services\PaiementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();

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

    /**
     * Callback appelé par Wave ou OrangeMoney après un paiement
     */
    public function callback(Request $request)
    {
        try {
            $commandeId = $request->input('commande_id');
            $status     = $request->input('status'); // 'success' ou 'fail'

            if (!$commandeId) {
                return response()->json(['message' => 'Paramètre commande_id requis'], 400);
            }

            $commande = Commande::find($commandeId);
            if (!$commande) {
                return response()->json(['message' => 'Commande non trouvée'], 404);
            }

            $paiement = Paiement::where('commande_id', $commandeId)->first();
            if (!$paiement) {
                return response()->json(['message' => 'Paiement non trouvé'], 404);
            }

            if ($status === 'success') {
                $service = new PaiementService();

                if ($paiement->session_id && $paiement->methode_paiement === 'Wave') {
                    $verifie = $service->verifierWave($paiement->session_id);
                    if (!$verifie) {
                        return redirect($this->appUrl . '/paiement/echec?commande_id=' . $commandeId);
                    }
                }

                if ($paiement->session_id && $paiement->methode_paiement === 'OrangeMoney') {
                    $verifie = $service->verifierOrangeMoney($paiement->session_id);
                    if (!$verifie) {
                        return redirect($this->appUrl . '/paiement/echec?commande_id=' . $commandeId);
                    }
                }

                $paiement->update(['status' => 'Valider']);

                return redirect($this->appUrl . '/paiement/succes?commande_id=' . $commandeId);
            }

            $paiement->update(['status' => 'Echoué']);
            return redirect($this->appUrl . '/paiement/echec?commande_id=' . $commandeId);

        } catch (\Exception $e) {
            Log::error('Erreur callback paiement: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Confirmation d'un paiement simulé
     */
    public function simulerConfirmer(Request $request)
    {
        try {
            $request->validate([
                'commande_id' => 'required|exists:commandes,id',
                'methode'     => 'required|in:Wave,OrangeMoney',
            ]);

            $commande = Commande::findOrFail($request->commande_id);

            $paiement = Paiement::where('commande_id', $request->commande_id)->first();
            if (!$paiement) {
                return response()->json(['message' => 'Paiement non trouvé'], 404);
            }

            $paiement->update(['status' => 'Valider']);

            return response()->json([
                'message'  => 'Paiement simulé confirmé avec succès',
                'redirect' => '/paiement/succes?commande_id=' . $request->commande_id,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur simulation paiement: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la confirmation simulée'], 500);
        }
    }

    protected $appUrl;

    public function __construct()
    {
        $this->appUrl = config('app.url', 'http://127.0.0.1:8000');
    }
}
