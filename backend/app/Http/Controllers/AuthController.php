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

    /**
     * Profil de l'utilisateur connecté
     */
    public function profil(Request $request)
    {
        return response()->json([
            'user' => $this->formatUser($request->user()),
        ], 200);
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