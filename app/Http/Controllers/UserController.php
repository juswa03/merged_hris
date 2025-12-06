<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'employee']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('email', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
        }

        // Filter by role
        if ($request->has('role_id') && $request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15);

        // Statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'users_with_employees' => User::has('employee')->count(),
        ];

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'stats', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        $employees = Employee::whereDoesntHave('user')
            ->orderBy('last_name')
            ->get();

        return view('admin.users.create', compact('roles', 'employees'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:tbl_users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role_id' => 'required|exists:tbl_roles,id',
            'employee_id' => 'nullable|exists:tbl_employee,id|unique:tbl_users,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'status' => $request->status,
            ]);

            // Link to employee if selected
            if ($request->employee_id) {
                $employee = Employee::findOrFail($request->employee_id);
                $employee->user_id = $user->id;
                $employee->save();
            }

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::with(['role', 'employee'])->findOrFail($id);

        // Get user activity stats if needed
        $stats = [
            'last_login' => $user->last_logged_at,
            'account_age' => $user->created_at->diffForHumans(),
            'status' => $user->status,
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = User::with('employee')->findOrFail($id);
        $roles = Role::all();
        $employees = Employee::whereDoesntHave('user')
            ->orWhere('user_id', $user->id)
            ->orderBy('last_name')
            ->get();

        return view('admin.users.edit', compact('user', 'roles', 'employees'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'email' => 'required|string|email|max:255|unique:tbl_users,email,' . $id,
            'role_id' => 'required|exists:tbl_roles,id',
            'employee_id' => 'nullable|exists:tbl_employee,id',
            'status' => 'required|in:active,inactive,suspended',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        try {
            $data = [
                'email' => $request->email,
                'role_id' => $request->role_id,
                'status' => $request->status,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            // Update employee link
            if ($request->employee_id) {
                // Remove old employee link if exists
                if ($user->employee) {
                    $oldEmployee = $user->employee;
                    $oldEmployee->user_id = null;
                    $oldEmployee->save();
                }

                // Add new employee link
                $employee = Employee::findOrFail($request->employee_id);
                $employee->user_id = $user->id;
                $employee->save();
            } else {
                // Remove employee link if deselected
                if ($user->employee) {
                    $employee = $user->employee;
                    $employee->user_id = null;
                    $employee->save();
                }
            }

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting yourself
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account.'
                ], 422);
            }

            // Remove employee link if exists
            if ($user->employee) {
                $employee = $user->employee;
                $employee->user_id = null;
                $employee->save();
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deactivating yourself
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot deactivate your own account.'
                ], 422);
            }

            $user->status = $user->status === 'active' ? 'inactive' : 'active';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully.',
                'status' => $user->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password (Admin function)
     */
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        try {
            $user = User::findOrFail($id);

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users as JSON for API/AJAX requests
     */
    public function list(Request $request)
    {
        $query = User::with(['role', 'employee']);

        if ($request->has('active_only') && $request->active_only) {
            $query->where('status', 'active');
        }

        $users = $query->orderBy('email')->get();

        return response()->json($users);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['role_id', 'status']);
        $timestamp = now()->format('Y-m-d_His');
        $filename = "users_export_{$timestamp}.xlsx";
        return Excel::download(new UserExport($filters), $filename);
    }
}
