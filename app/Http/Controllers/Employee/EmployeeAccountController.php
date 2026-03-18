<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class EmployeeAccountController extends Controller
{
    /** Show the account overview page. */
    public function index()
    {
        $user = Auth::user();
        return view('employee.account.index', compact('user'));
    }

    // ─── Profile ──────────────────────────────────────────────────────────────

    public function editProfile()
    {
        $user     = Auth::user();
        $employee = $user->employee;
        return view('employee.account.edit-profile', compact('user', 'employee'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $validated['profile_photo_path'] = $request->file('profile_photo')
                ->store('profile-photos', 'public');
        }

        $user->update($validated);
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    // ─── Password ─────────────────────────────────────────────────────────────

    public function editPassword()
    {
        return view('employee.account.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    // ─── Email ────────────────────────────────────────────────────────────────

    public function editEmail()
    {
        $user = Auth::user();
        return view('employee.account.change-email', compact('user'));
    }

    public function updateEmail(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        $user->update(['email' => $request->email, 'email_verified_at' => null]);
        return redirect()->back()->with('success', 'Email updated successfully.');
    }

    // ─── Two-Factor Auth ──────────────────────────────────────────────────────

    public function editTwoFactor()
    {
        $user = Auth::user();
        return view('employee.account.two-factor', compact('user'));
    }

    public function enableTwoFactor(Request $request)
    {
        // Placeholder — implement with your 2FA package
        return redirect()->back()->with('info', 'Two-factor authentication setup coming soon.');
    }

    public function verifyTwoFactor(Request $request)
    {
        return redirect()->back()->with('info', 'Two-factor verification coming soon.');
    }

    public function disableTwoFactor(Request $request)
    {
        return redirect()->back()->with('success', 'Two-factor authentication disabled.');
    }

    // ─── Activity Log ─────────────────────────────────────────────────────────

    public function activityLog()
    {
        $user = Auth::user();
        return view('employee.account.activity-log', compact('user'));
    }

    // ─── Sessions ─────────────────────────────────────────────────────────────

    public function sessions()
    {
        $user = Auth::user();
        return view('employee.account.sessions', compact('user'));
    }

    public function logoutSession(string $sessionId)
    {
        // Session invalidation depends on the driver; basic implementation:
        return redirect()->back()->with('success', 'Session logged out.');
    }

    // ─── Preferences ──────────────────────────────────────────────────────────

    public function preferences()
    {
        $user = Auth::user();
        return view('employee.account.preferences', compact('user'));
    }

    public function updatePreferences(Request $request)
    {
        // Store per-user preferences in the user model or a settings table
        return redirect()->back()->with('success', 'Preferences saved.');
    }

    // ─── Account Deletion ─────────────────────────────────────────────────────

    public function editDelete()
    {
        $user = Auth::user();
        return view('employee.account.delete-account', compact('user'));
    }

    public function deleteAccount(Request $request)
    {
        $request->validate(['password' => 'required']);
        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}
