<?php

namespace App\Http\Middleware;

use App\Models\LoginSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackLoginSession
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $sessionId = $request->session()->getId();

            $loginSession = LoginSession::where('session_id', $sessionId)
                ->whereNull('logged_out_at')
                ->first();

            if ($loginSession) {
                // Force-logout if session was revoked by admin
                if ($loginSession->is_revoked) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->withErrors(['email' => 'Your session was terminated by an administrator.']);
                }

                // Throttle DB writes — only update every 5 minutes
                if ($loginSession->last_activity_at->diffInMinutes(now()) >= 5) {
                    $loginSession->update(['last_activity_at' => now()]);
                }
            }
        }

        return $next($request);
    }
}
