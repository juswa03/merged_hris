<?php

namespace App\Http\Middleware;

use App\Models\MaintenanceSetting;
use Closure;
use Illuminate\Http\Request;

class CheckMaintenanceMode
{
    // Routes always accessible even during maintenance
    private array $except = [
        'login', 'logout', 'admin/maintenance*',
    ];

    public function handle(Request $request, Closure $next)
    {
        // Always allow admin routes directly to avoid lock-out
        if ($request->is('admin/*') || $request->is('login') || $request->is('logout')) {
            return $next($request);
        }

        try {
            $settings = MaintenanceSetting::settings();

            if (! $settings->is_active) {
                return $next($request);
            }

            // Check scheduled end — auto-deactivate if time has passed
            if ($settings->scheduled_end_at && $settings->scheduled_end_at->isPast()) {
                $settings->update(['is_active' => false]);
                return $next($request);
            }

            // Check IP whitelist
            $clientIp   = $request->ip();
            $whitelisted = $settings->whitelisted_ips_array;

            if (! empty($whitelisted) && in_array($clientIp, $whitelisted)) {
                return $next($request);
            }

            // Return maintenance view
            return response()->view('maintenance', [
                'title'   => $settings->title,
                'message' => $settings->message,
                'endAt'   => $settings->scheduled_end_at,
            ], 503);

        } catch (\Exception $e) {
            // If DB is unavailable, fall through normally
            return $next($request);
        }
    }
}
