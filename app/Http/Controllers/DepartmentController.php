<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function export(Request $request)
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "departments_export_{$timestamp}.xlsx";
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DepartmentExport(), $filename);
    }
    /**
     * Display a listing of departments with employee counts
     */
    public function index(Request $request)
    {
        $query = Department::withCount('employees');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $departments = $query->paginate(15);

        // Statistics
        $stats = [
            'total_departments' => Department::count(),
            'total_employees' => Employee::count(),
            'departments_with_employees' => Department::has('employees')->count(),
            'empty_departments' => Department::doesntHave('employees')->count(),
        ];

        return view('admin.departments.index', compact('departments', 'stats'));
    }

    /**
     * Show the form for creating a new department
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tbl_departments,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $department = Department::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Department created successfully.',
                    'data' => $department
                ]);
            }

            return redirect()->route('departments.index')
                ->with('success', 'Department created successfully.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create department: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Failed to create department: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified department with employee list
     */
    public function show($id)
    {
        $department = Department::with(['employees' => function($query) {
            $query->with(['position', 'employmentType', 'jobStatus'])
                  ->orderBy('first_name');
        }])->findOrFail($id);

        // Department statistics
        $stats = [
            'total_employees' => $department->employees->count(),
            'active_employees' => $department->employees->filter(function($emp) {
                return $emp->jobStatus && $emp->jobStatus->name === 'Active';
            })->count(),
            'avg_salary' => $department->employees->avg('basic_salary') ?? 0,
            'total_salary' => $department->employees->sum('basic_salary') ?? 0,
        ];

        return view('admin.departments.show', compact('department', 'stats'));
    }

    /**
     * Show the form for editing the specified department
     */
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:tbl_departments,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $department->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Department updated successfully.',
                    'data' => $department
                ]);
            }

            return redirect()->route('departments.index')
                ->with('success', 'Department updated successfully.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update department: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Failed to update department: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified department
     */
    public function destroy($id)
    {
        try {
            $department = Department::findOrFail($id);

            // Check if department has employees
            if ($department->employees()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete department with active employees. Please reassign employees first.'
                ], 422);
            }

            $department->delete();

            return response()->json([
                'success' => true,
                'message' => 'Department deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete department: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get departments as JSON for API/AJAX requests
     */
    public function list(Request $request)
    {
        $departments = Department::withCount('employees')
            ->orderBy('name')
            ->get();

        return response()->json($departments);
    }

    /**
     * Get department statistics
     */
    public function statistics()
    {
        $stats = Department::select('tbl_departments.*')
            ->withCount('employees')
            ->with(['employees' => function($query) {
                $query->select('id', 'department_id', 'basic_salary');
            }])
            ->get()
            ->map(function($dept) {
                return [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'employee_count' => $dept->employees_count,
                    'total_salary' => $dept->employees->sum('basic_salary'),
                    'avg_salary' => $dept->employees->avg('basic_salary') ?? 0,
                ];
            });

        return response()->json($stats);
    }

    /**
     * Bulk delete departments
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tbl_departments,id'
        ]);

        try {
            $departments = Department::whereIn('id', $request->ids)->get();

            // Check if any department has employees
            $hasEmployees = $departments->filter(function($dept) {
                return $dept->employees()->count() > 0;
            });

            if ($hasEmployees->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some departments have active employees. Cannot delete.'
                ], 422);
            }

            Department::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' departments deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete departments: ' . $e->getMessage()
            ], 500);
        }
    }
}
