<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur
     * Roles acceptés : producteur, distributeur, consommateur, transporteur, admin
     */
    public function register(Request $request)
    {
        $request->validate([
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:producteur,distributeur,consommateur,transporteur,admin',
            'telephone'=> 'nullable|string|max:20',
            'adresse'  => 'nullable|string|max:255',
        ]);

        // Les consommateurs sont activés automatiquement,
        // les autres rôles passent par validation admin
        $statut = $request->role === 'consommateur' ? 'actif' : 'en_attente';

        $user = User::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'telephone' => $request->telephone,
            'adresse'   => $request->adresse,
            'statut'    => $statut,
        ]);

        if ($statut === 'actif') {
            // Connexion immédiate pour les consommateurs
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Compte créé avec succès.',
                'user'    => $this->formatUser($user),
                'token'   => $token,
            ], 201);
        }

        return response()->json([
            'message' => 'Compte créé. En attente de validation par un administrateur.',
            'user'    => $this->formatUser($user),
        ], 201);
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
     * (évite d'exposer le mot de passe et les champs sensibles)
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