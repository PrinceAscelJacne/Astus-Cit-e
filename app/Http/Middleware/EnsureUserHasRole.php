<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restreint une route à une liste de rôles.
 *
 * Utilisation : ->middleware('role:2,3')
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $autorises = array_map('intval', $roles);

        if (! in_array((int) $user->role_id, $autorises, true)) {
            abort(403, "Vous n'avez pas les droits nécessaires pour accéder à cette page.");
        }

        return $next($request);
    }
}
