<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Role;
use App\Models\User;
use App\Models\RolePermission;
use App\Exports\RoleExport;
use Maatwebsite\Excel\Facades\Excel;
class RoleController extends Controller
{
    //

        /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        $query = Role::withCount('users');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $roles = $query->orderBy('name')->paginate(15);

        // Statistics
        $stats = [
            'total_roles' => Role::count(),
            'roles_with_users' => Role::has('users')->count(),
            'total_users' => User::count(),
        ];

        return view('admin.roles.index', compact('roles', 'stats'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tbl_roles,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create role: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified role with user list
     */
    public function show($id)
    {
        $role = Role::with(['users.employee.department'])->withCount('users')->findOrFail($id);

        $stats = [
            'total_users' => $role->users->count(),
            'active_users' => $role->users->where('status', 'active')->count(),
            'inactive_users' => $role->users->where('status', 'inactive')->count(),
        ];

        return view('admin.roles.show', compact('role', 'stats'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit($id)
    {
        $role = Role::withCount('users')->findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:tbl_roles,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to update role: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            // Check if role has users
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role with assigned users. Please reassign users first.'
                ], 422);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "roles_export_{$timestamp}.xlsx";
        return Excel::download(new RoleExport(), $filename);
    }

    /**
     * Visual Permission Manager — show all roles × module permissions matrix
     */
    public function permissions()
    {
        $roles = Role::orderBy('name')->get();

        // Load existing granted permissions keyed by role_id
        $granted = RolePermission::all()->groupBy('role_id')
            ->map(fn ($rows) => $rows->pluck('permission')->toArray());

        $modules = $this->modulePermissions();

        return view('admin.roles.permissions', compact('roles', 'granted', 'modules'));
    }

    /**
     * Save the permission matrix
     */
    public function updatePermissions(Request $request)
    {
        $incoming = $request->input('permissions', []);  // ['role_id' => ['perm1', 'perm2', ...]]

        DB::transaction(function () use ($incoming) {
            // Delete all existing, then re-insert
            RolePermission::truncate();

            $rows = [];
            $now  = now();
            foreach ($incoming as $roleId => $perms) {
                foreach ((array) $perms as $perm) {
                    $rows[] = [
                        'role_id'    => (int) $roleId,
                        'permission' => $perm,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($rows)) {
                RolePermission::insert($rows);
            }
        });

        return back()->with('success', 'Permissions saved successfully.');
    }

    /**
     * Canonical list of module permissions shown in the matrix.
     */
    private function modulePermissions(): array
    {
        return [
            'Employees' => [
                'employees.view'   => 'View Employees',
                'employees.create' => 'Create / Edit Employees',
                'employees.delete' => 'Delete Employees',
            ],
            'Departments & Positions' => [
                'departments.manage' => 'Manage Departments',
                'positions.manage'   => 'Manage Positions',
            ],
            'Attendance & DTR' => [
                'attendance.view'   => 'View Attendance',
                'attendance.manage' => 'Manage Attendance',
                'dtr.view'          => 'View DTR',
                'dtr.manage'        => 'Manage DTR',
            ],
            'Payroll' => [
                'payroll.view'     => 'View Payroll',
                'payroll.generate' => 'Generate Payroll',
                'payroll.approve'  => 'Approve Payroll',
                'payroll.reports'  => 'Payroll Reports',
            ],
            'Salary & Benefits' => [
                'salary.manage'      => 'Manage Salaries',
                'allowances.manage'  => 'Manage Allowances',
                'deductions.manage'  => 'Manage Deductions',
            ],
            'Leave Management' => [
                'leave.view'    => 'View Leaves',
                'leave.manage'  => 'Manage / Approve Leaves',
            ],
            'Performance' => [
                'performance.view'   => 'View Performance',
                'performance.manage' => 'Manage Performance',
            ],
            'Reports' => [
                'reports.view'    => 'View Reports',
                'reports.export'  => 'Export Reports',
                'reports.builder' => 'Report Builder',
            ],
            'Users & Roles' => [
                'users.view'        => 'View Users',
                'users.manage'      => 'Manage Users',
                'roles.manage'      => 'Manage Roles',
                'permissions.manage'=> 'Manage Permissions',
            ],
            'System' => [
                'settings.manage'       => 'System Settings',
                'audit-logs.view'       => 'View Audit Logs',
                'system-health.view'    => 'System Health',
                'login-sessions.view'   => 'Login Sessions',
            ],
        ];
    }
}
