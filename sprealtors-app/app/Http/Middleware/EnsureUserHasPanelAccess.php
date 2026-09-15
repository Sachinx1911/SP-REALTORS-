<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for the whole /admin area: any assigned role (Admin or Manager) may
 * enter. Alias: "panel". Team & Settings additionally require the stricter
 * "admin" middleware — see EnsureUserIsAdmin.
 */
class EnsureUserHasPanelAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasAccess()) {
            abort(403, 'Admin panel access required.');
        }

        return $next($request);
    }
}
