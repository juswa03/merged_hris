<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Block non-admin users from system-level administration routes.
 * HR Staff may pass the 'access_admin' gate for operational routes,
 * but should never reach system-level operations (user management,
 * roles, settings, maintenance, backup, queue, system health).
 */
class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Admin access required.'], 403);
            }
            abort(403, 'This section is restricted to system administrators.');
        }

        return $next($request);
    }
}
