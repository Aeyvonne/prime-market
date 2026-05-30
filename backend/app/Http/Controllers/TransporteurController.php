<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\Livraison;
use App\Models\Commande;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransporteurController extends Controller
{
    private function getTransporteurId(Request $request)
    {
        return $request->user()->id;
    }

    /* -------------------------------------------------------------------
     | MISSIONS
     | ------------------------------------------------------------------- */

    public function indexMissions(Request $request)
    {
        try {
            $transporteurId = $this->getTransporteurId($request);

            $missions = Mission::with([
                    'livraison.commande.vendeur',
                    'livraison.commande.lignesCommande.produit.sousCategorie.categorie',
                ])
                ->where(function ($q) use ($transporteurId) {
                    $q->whereNull('transporteur_id')
                      ->orWhere('transporteur_id', $transporteurId);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($missions);
        } catch (\Exception $e) {
            Log::error('Erreur indexMissions: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des missions.'], 500);
        }
    }

    public function showMission(Request $request, $id)
    {
        try {
            $mission = Mission::with([
                'livraison.commande.acheteur',
                'livraison.commande.vendeur',
                'livraison.commande.lignesCommande.produit.sousCategorie',
                'transporteur.user',
            ])->find($id);

            if (!$mission) {
                return response()->json(['message' => 'Mission non trouvée.'], 404);
            }

            return response()->json($mission);
        } catch (\Exception $e) {
            Log::error('Erreur showMission: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération de la mission.'], 500);
        }
    }

    public function accepterMission(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transporteurId = $this->getTransporteurId($request);

            $request->validate([
                'date_livraison_estimee' => 'required|date',
            ]);

            $mission = Mission::find($id);

            if (!$mission) {
                return response()->json(['message' => 'Mission non trouvée.'], 404);
            }

            if ($mission->transporteur_id !== null) {
                return response()->json(['message' => 'Cette mission est déjà assignée à un transporteur.'], 400);
            }

            $mission->update([
                'transporteur_id' => $transporteurId,
                'status' => 'EnCours',
            ]);

            $mission->livraison()->update(['date_livraison_estimee' => $request->date_livraison_estimee]);

            DB::commit();

            return response()->json([
                'message' => 'Mission acceptée avec succès.',
                'mission' => $mission->fresh()->load('livraison.commande'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur accepterMission: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de l\'acceptation de la mission.'], 500);
        }
    }

    public function refuserMission(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transporteurId = $this->getTransporteurId($request);

            $mission = Mission::where('transporteur_id', $transporteurId)->find($id);

            if (!$mission) {
                return response()->json(['message' => 'Mission non trouvée ou non assignée à vous.'], 404);
            }

            $mission->update([
                'transporteur_id' => null,
                'status' => 'EnCours',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Mission libérée pour un autre transporteur.',
                'mission' => $mission->fresh()->load('livraison.commande'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur refuserMission: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du refus de la mission.'], 500);
        }
    }

    /* -------------------------------------------------------------------
     | LIVRAISONS
     | ------------------------------------------------------------------- */

    public function indexLivraisons(Request $request)
    {
        try {
            $transporteurId = $this->getTransporteurId($request);

            $livraisons = Livraison::with([
                    'commande.acheteur',
                    'commande.vendeur',
                    'commande.lignesCommande.produit.sousCategorie.categorie',
                    'mission',
                ])
                ->whereHas('mission', function ($q) use ($transporteurId) {
                    $q->where('transporteur_id', $transporteurId);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($livraisons);
        } catch (\Exception $e) {
            Log::error('Erreur indexLivraisons: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des livraisons.'], 500);
        }
    }

    public function showLivraison(Request $request, $id)
    {
        try {
            $transporteurId = $this->getTransporteurId($request);

            $livraison = Livraison::with([
                'commande.acheteur',
                'commande.vendeur',
                'commande.lignesCommande.produit.sousCategorie.categorie',
                'mission',
            ])
                ->whereHas('mission', function ($q) use ($transporteurId) {
                    $q->where('transporteur_id', $transporteurId);
                })
                ->find($id);

            if (!$livraison) {
                return response()->json(['message' => 'Livraison non trouvée.'], 404);
            }

            return response()->json($livraison, 200);
        } catch (\Exception $e) {
            Log::error('Erreur showLivraison: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération de la livraison.'], 500);
        }
    }

    public function updateStatutLivraison(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transporteurId = $this->getTransporteurId($request);

            $request->validate([
                'status' => 'required|in:EnCours,Valider,Annuler',
                'date_expedition' => 'nullable|date',
                'date_livraison_reelle' => 'nullable|date',
            ]);

            $livraison = Livraison::with('mission')
                ->whereHas('mission', function ($q) use ($transporteurId) {
                    $q->where('transporteur_id', $transporteurId);
                })
                ->find($id);

            if (!$livraison) {
                return response()->json(['message' => 'Livraison non trouvée.'], 404);
            }

            $data = ['status' => $request->status];

            if ($request->filled('date_expedition')) {
                $data['date_expedition'] = $request->date_expedition;
            }

            if ($request->filled('date_livraison_reelle')) {
                $data['date_livraison_reelle'] = $request->date_livraison_reelle;
            }

            $livraison->update($data);

            if ($request->status === 'Valider') {
                $livraison->mission()->update(['status' => 'Valider']);
                $livraison->commande()->update(['status' => 'Livrer']);
            } elseif ($request->status === 'Annuler') {
                $livraison->mission()->update(['status' => 'Annuler', 'transporteur_id' => null]);
                $livraison->commande()->update(['status' => 'EnCours']);
            }

            DB::commit();

            $livraison = $livraison->fresh()->load([
                'commande.acheteur',
                'commande.vendeur',
                'commande.lignesCommande.produit.sousCategorie.categorie',
                'mission',
            ]);

            return response()->json([
                'message' => 'Statut de livraison mis à jour.',
                'livraison' => $livraison,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur updateStatutLivraison: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la mise à jour du statut.'], 500);
        }
    }

    /* -------------------------------------------------------------------
     | RÉMUNÉRATIONS
     | ------------------------------------------------------------------- */

    public function remunerations(Request $request)
    {
        try {
            $transporteurId = $this->getTransporteurId($request);

            $missions = Mission::where('transporteur_id', $transporteurId)
                ->whereIn('status', ['Valider', 'Annuler'])
                ->whereNotNull('remuneration')
                ->orderBy('updated_at', 'desc')
                ->get();

            $total = $missions->where('status', 'Valider')->sum('remuneration');

            $debutMois = now()->startOfMonth();
            $finMois = now()->endOfMonth();
            $totalMois = $missions
                ->where('status', 'Valider')
                ->filter(function ($m) use ($debutMois, $finMois) {
                    return $m->updated_at >= $debutMois && $m->updated_at <= $finMois;
                })
                ->sum('remuneration');

            return response()->json([
                'total' => $total,
                'total_mois' => $totalMois,
                'liste' => $missions->load('livraison.commande'),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur remunerations: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des rémunérations.'], 500);
        }
    }

    /* -------------------------------------------------------------------
     | DISPONIBILITÉ
     | ------------------------------------------------------------------- */

    public function updateDisponibilite(Request $request)
    {
        try {
            $request->validate([
                'disponible' => 'required|boolean',
            ]);

            $transporteur = $request->user()->transporteur;

            if (!$transporteur) {
                return response()->json(['message' => 'Profil transporteur non trouvé.'], 404);
            }

            $transporteur->update(['disponibilite' => $request->disponible]);

            return response()->json([
                'message' => $request->disponible
                    ? 'Vous êtes maintenant disponible.'
                    : 'Vous êtes maintenant indisponible.',
                'disponibilite' => (bool) $request->disponible,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur updateDisponibilite: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la mise à jour de la disponibilité.'], 500);
        }
    }
}
