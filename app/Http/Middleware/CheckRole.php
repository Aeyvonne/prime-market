<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur connecté possède le(s) rôle(s) requis.
     *
     * Utilisation dans routes/api.php :
     *   ->middleware('role:producteur')
     *   ->middleware('role:producteur,admin')   // plusieurs rôles autorisés
     *
     * @param  string  $roles  Rôles autorisés séparés par des virgules
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Utilisateur non authentifié
        if (! $user) {
            return response()->json([
                'message' => 'Non authentifié. Veuillez vous connecter.',
            ], 401);
        }

        // Compte suspendu ou en attente
        if ($user->statut !== 'actif') {
            return response()->json([
                'message' => 'Votre compte n\'est pas actif. Contactez l\'administrateur.',
            ], 403);
        }

        // Vérification du rôle
        if (! in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Accès refusé. Vous n\'avez pas les droits nécessaires pour cette action.',
                'votre_role'   => $user->role,
                'roles_requis' => $roles,
            ], 403);
        }

        return $next($request);
    }
}