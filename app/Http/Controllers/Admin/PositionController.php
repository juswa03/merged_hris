<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Position;
use App\Models\Employee;
use App\Exports\PositionExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PositionController extends Controller
{
    //

      /**
     * Display a listing of positions with employee counts
     */
    public function index(Request $request)
    {
        $query = Position::withCount('employees');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $positions = $query->paginate(15);

        // Statistics
        $stats = [
            'total_positions' => Position::count(),
            'active_positions' => Position::where('is_active', true)->count(),
            'filled_positions' => Position::has('employees')->count(),
            'vacant_positions' => Position::doesntHave('employees')->count(),
            'total_employees' => Employee::count(),
        ];

        // Position levels for filter
        $levels = Position::select('level')
            ->distinct()
            ->whereNotNull('level')
            ->pluck('level');

        return view('admin.positions.index', compact('positions', 'stats', 'levels'));
    }

    /**
     * Show the form for creating a new position
     */
    public function create()
    {
        return view('admin.positions.create');
    }

    /**
     * Store a newly created position
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tbl_positions,name',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'requirements' => 'nullable|string|max:2000',
            'level' => 'nullable|string|in:Entry Level,Mid Level,Senior Level,Executive,Managerial',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'salary_grade' => 'nullable|integer|min:1|max:30',
            'is_active' => 'boolean',
        ]);

        try {
            $position = Position::create([
                'name' => $request->name,
                'title' => $request->title,
                'description' => $request->description,
                'requirements' => $request->requirements,
                'level' => $request->level,
                'min_salary' => $request->min_salary,
                'max_salary' => $request->max_salary,
                'salary_grade' => $request->salary_grade,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Position created successfully.',
                    'data' => $position
                ]);
            }

            return redirect()->route('admin.positions.index')
                ->with('success', 'Position created successfully.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create position: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Failed to create position: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified position with employee list
     */
    public function show($id)
    {
        $position = Position::with(['employees' => function($query) {
            $query->with(['department', 'employmentType', 'jobStatus'])
                  ->orderBy('last_name');
        }])->findOrFail($id);

        // Position statistics
        $stats = [
            'total_employees' => $position->employees->count(),
            'active_employees' => $position->employees->filter(function($emp) {
                return $emp->jobStatus && $emp->jobStatus->name === 'Active';
            })->count(),
            'avg_salary' => $position->employees->avg('basic_salary') ?? 0,
            'total_salary' => $position->employees->sum('basic_salary') ?? 0,
            'min_actual_salary' => $position->employees->min('basic_salary') ?? 0,
            'max_actual_salary' => $position->employees->max('basic_salary') ?? 0,
        ];

        return view('admin.positions.show', compact('position', 'stats'));
    }

    /**
     * Show the form for editing the specified position
     */
    public function edit($id)
    {
        $position = Position::findOrFail($id);
        return view('admin.positions.edit', compact('position'));
    }

    /**
     * Update the specified position
     */
    public function update(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:tbl_positions,name,' . $id,
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'requirements' => 'nullable|string|max:2000',
            'level' => 'nullable|string|in:Entry Level,Mid Level,Senior Level,Executive,Managerial',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'salary_grade' => 'nullable|integer|min:1|max:30',
            'is_active' => 'boolean',
        ]);

        try {
            $position->update([
                'name' => $request->name,
                'title' => $request->title,
                'description' => $request->description,
                'requirements' => $request->requirements,
                'level' => $request->level,
                'min_salary' => $request->min_salary,
                'max_salary' => $request->max_salary,
                'salary_grade' => $request->salary_grade,
                'is_active' => $request->has('is_active') ? $request->is_active : $position->is_active,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Position updated successfully.',
                    'data' => $position
                ]);
            }

            return redirect()->route('admin.positions.index')
                ->with('success', 'Position updated successfully.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update position: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Failed to update position: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified position
     */
    public function destroy($id)
    {
        try {
            $position = Position::findOrFail($id);

            // Check if position has employees
            if ($position->employees()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete position with assigned employees. Please reassign employees first.'
                ], 422);
            }

            $position->delete();

            return response()->json([
                'success' => true,
                'message' => 'Position deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete position: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle position active status
     */
    public function toggleStatus($id)
    {
        try {
            $position = Position::findOrFail($id);
            $position->is_active = !$position->is_active;
            $position->save();

            return response()->json([
                'success' => true,
                'message' => 'Position status updated successfully.',
                'is_active' => $position->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update position status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get positions as JSON for API/AJAX requests
     */
    public function list(Request $request)
    {
        $query = Position::withCount('employees');

        if ($request->has('active_only') && $request->active_only) {
            $query->where('is_active', true);
        }

        $positions = $query->orderBy('name')->get();

        return response()->json($positions);
    }

    /**
     * Get position statistics
     */
    public function statistics()
    {
        $stats = Position::select('tbl_positions.*')
            ->withCount('employees')
            ->with(['employees' => function($query) {
                $query->select('id', 'position_id', 'basic_salary');
            }])
            ->get()
            ->map(function($pos) {
                return [
                    'id' => $pos->id,
                    'name' => $pos->name,
                    'title' => $pos->title,
                    'level' => $pos->level,
                    'employee_count' => $pos->employees_count,
                    'total_salary' => $pos->employees->sum('basic_salary'),
                    'avg_salary' => $pos->employees->avg('basic_salary') ?? 0,
                    'is_active' => $pos->is_active,
                ];
            });

        return response()->json($stats);
    }

    /**
     * Bulk delete positions
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tbl_positions,id'
        ]);

        try {
            $positions = Position::whereIn('id', $request->ids)->get();

            // Check if any position has employees
            $hasEmployees = $positions->filter(function($pos) {
                return $pos->employees()->count() > 0;
            });

            if ($hasEmployees->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some positions have assigned employees. Cannot delete.'
                ], 422);
            }

            Position::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' positions deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete positions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export positions to Excel
     */
    public function export(Request $request)
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "positions_export_{$timestamp}.xlsx";
        return Excel::download(new PositionExport(), $filename);
    }
}
