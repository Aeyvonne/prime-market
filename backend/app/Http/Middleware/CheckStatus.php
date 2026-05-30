<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStatus
{
    public function handle(Request $request, Closure $next, ...$statuses): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        if (!in_array($request->user()->statut, $statuses)) {
            return response()->json([
                'message' => 'Votre compte doit être activé pour accéder à cette fonctionnalité.',
                'statut' => $request->user()->statut,
            ], 403);
        }

        return $next($request);
    }
}
