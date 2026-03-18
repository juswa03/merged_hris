<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginSession;
use Illuminate\Http\Request;

class LoginSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = LoginSession::with('user')
            ->latest('logged_in_at');

        // Filter by user
        if ($request->filled('user')) {
            $search = $request->user;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            match ($request->status) {
                'active'   => $query->whereNull('logged_out_at')->where('is_revoked', false)
                                     ->where('last_activity_at', '>=', now()->subHours(2)),
                'revoked'  => $query->where('is_revoked', true),
                'expired'  => $query->whereNull('logged_out_at')->where('is_revoked', false)
                                     ->where('last_activity_at', '<', now()->subHours(2)),
                'loggedout'=> $query->whereNotNull('logged_out_at'),
                default    => null,
            };
        }

        $sessions = $query->paginate(25)->withQueryString();

        $stats = [
            'active'    => LoginSession::whereNull('logged_out_at')->where('is_revoked', false)
                            ->where('last_activity_at', '>=', now()->subHours(2))->count(),
            'today'     => LoginSession::whereDate('logged_in_at', today())->count(),
            'revoked'   => LoginSession::where('is_revoked', true)->count(),
            'total'     => LoginSession::count(),
        ];

        return view('admin.login-sessions.index', compact('sessions', 'stats'));
    }

    public function revoke(LoginSession $loginSession)
    {
        $loginSession->update(['is_revoked' => true]);

        return back()->with('success', 'Session revoked. The user will be logged out on their next request.');
    }

    public function destroy(LoginSession $loginSession)
    {
        $loginSession->delete();

        return back()->with('success', 'Session record deleted.');
    }

    public function revokeAll()
    {
        // Revoke all sessions except the current admin's own session
        LoginSession::whereNull('logged_out_at')
            ->where('is_revoked', false)
            ->where('user_id', '!=', auth()->id())
            ->update(['is_revoked' => true]);

        return back()->with('success', 'All active sessions (except yours) have been revoked.');
    }
}
