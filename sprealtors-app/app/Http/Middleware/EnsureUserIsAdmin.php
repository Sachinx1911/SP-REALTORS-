<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stricter gate for Team management and Settings: the "admin" role only.
 * A Manager passes EnsureUserHasPanelAccess but is blocked here.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdmin()) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}
