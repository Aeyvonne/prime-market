<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Producteur;
use App\Models\Distributeur;
use App\Models\Consommateur;
use App\Models\Transporteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur avec son profil spécifique
     * Roles acceptés : producteur, distributeur, consommateur, transporteur, admin_sectoriel
     */
    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'nom'       => 'required|string|max:255',
                'prenom'    => 'required|string|max:255',
                'email'     => 'required|email|unique:users',
                'password'  => 'required|min:8|confirmed',
                'telephone' => 'required|string|unique:users',
                'role'      => [
                    'required',
                    Rule::in([
                        'producteur',
                        'distributeur',
                        'consommateur',
                        'transporteur',
                        'admin_sectoriel'
                    ])
                ],
                'adresse'            => 'nullable|string|max:500',
                'acceptTerms'        => 'required|accepted',
                // Champs Producteur
                'secteur_travail'    => $request->input('role') === 'producteur' ? 'required|in:Agriculture,Elevage,Peche' : 'sometimes|nullable',
                // Champs Distributeur
                'type_distributeur'  => $request->input('role') === 'distributeur' ? 'required|in:Grossiste,Detaillant' : 'sometimes|nullable',
                // Champs Transporteur
                'type_vehicule'      => $request->input('role') === 'transporteur' ? 'required|in:Camion,Voiture,Moto' : 'sometimes|nullable',
                'zone_intervention'  => 'sometimes|nullable|string|max:255',
                'disponibilite'      => 'sometimes|nullable',
            ]);

            // Tous les comptes sont actifs par défaut à la création
            $statut = 'actif';

            return DB::transaction(function () use ($data, $statut) {
                // 1. Création de l'utilisateur de base
                $user = User::create([
                    'nom'       => $data['nom'],
                    'prenom'    => $data['prenom'],
                    'email'     => $data['email'],
                    'password'  => Hash::make($data['password']),
                    'telephone' => $data['telephone'],
                    'role'      => $data['role'],
                    'adresse'   => $data['adresse'] ?? null,
                    'statut'    => $statut,
                ]);

                // 2. Création du profil spécialisé
                switch ($data['role']) {
                    case 'producteur':
                        Producteur::create([
                            'id'              => $user->id,
                            'secteur_travail' => $data['secteur_travail'],
                            'adresse'         => $data['adresse'] ?? null,
                        ]);
                        break;
                    case 'distributeur':
                        Distributeur::create([
                            'id'                => $user->id,
                            'type_distributeur' => $data['type_distributeur'],
                            'adresse'           => $data['adresse'] ?? null,
                            'note'              => 0,
                        ]);
                        break;
                    case 'consommateur':
                        Consommateur::create([
                            'id'      => $user->id,
                            'adresse' => $data['adresse'] ?? null,
                        ]);
                        break;
                    case 'transporteur':
                        Transporteur::create([
                            'id'                => $user->id,
                            'type_vehicule'     => $data['type_vehicule'],
                            'zone_intervention' => $data['zone_intervention'] ?? null,
                            'disponibilite'     => $data['disponibilite'] ?? true,
                        ]);
                        break;
                }

                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message' => 'Compte créé avec succès. Bienvenue sur Prime Market !',
                    'user'    => $this->formatUser($user),
                    'token'   => $token,
                    'role'    => $user->role
                ], 201);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Erreur d'inscription: " . $e->getMessage());
            return response()->json([
                'message' => "Erreur lors de l'inscription",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connexion d'un utilisateur
     * Retourne : token + utilisateur + rôle
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects.'],
            ]);
        }

        if ($user->statut === 'en_attente') {
            return response()->json([
                'message' => 'Votre compte est en attente de validation par un administrateur.',
            ], 403);
        }

        if ($user->statut === 'suspendu') {
            return response()->json([
                'message' => 'Votre compte a été suspendu. Veuillez contacter l\'administrateur.',
            ], 403);
        }

        // Révocation des anciens tokens pour éviter les doublons
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'user'    => $this->formatUser($user),
            'token'   => $token,
            'role'    => $user->role,
        ], 200);
    }

    /**
     * Déconnexion de l'utilisateur connecté
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.',
        ], 200);
    }

    public function profil(Request $request)
    {
        $user = $request->user();
        
        // Charger la relation selon le rôle pour ne pas faire de requêtes séparées (sauf si manquant)
        if ($user->role === 'producteur') $user->load('producteur');
        elseif ($user->role === 'distributeur') $user->load('distributeur');
        elseif ($user->role === 'consommateur') $user->load('consommateur');
        elseif ($user->role === 'transporteur') $user->load('transporteur');

        $stats = [];
        if ($user->role === 'producteur') {
            $stats = [
                'produits_count' => DB::table('produits')->where('producteur_id', $user->id)->count(),
                'commandes_recues' => DB::table('commandes')->where('vendeur_id', $user->id)->count(),
                'ventes_mois' => 0, // Placeholder
            ];
        } elseif ($user->role === 'distributeur') {
            $stats = [
                'commandes_passees' => DB::table('commandes')->where('acheteur_id', $user->id)->count(),
                'produits_vente' => DB::table('produits')->where('proprietaire_id', $user->id)->where('proprietaire_type', 'distributeur')->count(),
                'paiements_effectues' => 0, // Placeholder
            ];
        } elseif ($user->role === 'consommateur') {
            $stats = [
                'commandes_passees' => DB::table('commandes')->where('acheteur_id', $user->id)->count(),
                'livraisons_recues' => 0,
                'evaluations_donnees' => 0,
            ];
        } elseif ($user->role === 'transporteur') {
            $stats = [
                'missions_effectuees' => 0,
                'missions_en_cours' => 0,
                'remunerations_mois' => 0,
            ];
        }
        
        $userData = $this->formatUser($user);
        if ($user->role === 'producteur' && $user->producteur) $userData['producteur'] = $user->producteur;
        if ($user->role === 'distributeur' && $user->distributeur) $userData['distributeur'] = $user->distributeur;
        if ($user->role === 'consommateur' && $user->consommateur) $userData['consommateur'] = $user->consommateur;
        if ($user->role === 'transporteur' && $user->transporteur) $userData['transporteur'] = $user->transporteur;

        return response()->json([
            'user' => $userData,
            'stats' => $stats
        ], 200);
    }

    /**
     * Mettre à jour le profil de l'utilisateur
     */
    public function updateProfil(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'nom'       => 'sometimes|required|string|max:255',
            'prenom'    => 'sometimes|required|string|max:255',
            'email'     => 'sometimes|required|email|unique:users,email,' . $user->id,
            'telephone' => 'sometimes|required|string|unique:users,telephone,' . $user->id,
            'adresse'   => 'nullable|string|max:500',
            
            // Producteur
            'secteur_travail' => 'sometimes|nullable|in:Agriculture,Elevage,Peche',
            // Distributeur
            'type_distributeur' => 'sometimes|nullable|in:Grossiste,Detaillant',
            // Transporteur
            'type_vehicule' => 'sometimes|nullable|in:Camion,Voiture,Moto',
            'zone_intervention' => 'sometimes|nullable|string|max:255',
            'disponibilite' => 'sometimes|boolean',
        ]);

        $user->update($request->only(['nom', 'prenom', 'email', 'telephone', 'adresse']));

        if ($user->role === 'producteur' && $request->has('secteur_travail')) {
            $user->producteur()->update($request->only('secteur_travail', 'adresse'));
        } elseif ($user->role === 'distributeur' && $request->has('type_distributeur')) {
            $user->distributeur()->update($request->only('type_distributeur', 'adresse'));
        } elseif ($user->role === 'consommateur') {
            $user->consommateur()->update($request->only('adresse'));
        } elseif ($user->role === 'transporteur') {
            $user->transporteur()->update([
                'type_vehicule' => $request->input('type_vehicule', $user->transporteur->type_vehicule),
                'zone_intervention' => $request->input('zone_intervention', $user->transporteur->zone_intervention),
                'disponibilite' => $request->input('disponibilite', true)
            ]);
        }

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
        ]);
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['L\'ancien mot de passe est incorrect.']
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Mot de passe mis à jour avec succès.'
        ]);
    }

    /**
     * Mise en forme de l'utilisateur pour les réponses API
     */
    private function formatUser(User $user): array
    {
        return [
            'id'        => $user->id,
            'nom'       => $user->nom,
            'prenom'    => $user->prenom,
            'email'     => $user->email,
            'role'      => $user->role,
            'telephone' => $user->telephone,
            'adresse'   => $user->adresse,
            'statut'    => $user->statut,
        ];
    }
}