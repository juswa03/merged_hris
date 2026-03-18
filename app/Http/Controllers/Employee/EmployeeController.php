<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Show the employee's own profile.
     */
    public function myProfile()
    {
        $user     = Auth::user();
        $employee = $user->employee;

        return view('employee.account.edit-profile', compact('user', 'employee'));
    }

    /**
     * Update the employee's own profile.
     */
    public function updateMyProfile(Request $request)
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

    /**
     * Show the employee's documents.
     */
    public function documents()
    {
        $user     = Auth::user();
        $employee = $user->employee;

        return view('employee.documents.index', compact('user', 'employee'));
    }

    /**
     * Show the employee's notifications.
     */
    public function notifications()
    {
        $user          = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(20);

        return view('employee.notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markNotificationRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
