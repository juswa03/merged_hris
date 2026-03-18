<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\PerformanceGoal;
use App\Models\Employee;
use App\Models\User;
use App\Exports\PerformanceGoalExport;
use Maatwebsite\Excel\Facades\Excel;

class PerformanceGoalController extends Controller
{
    //

       /**
     * Display a listing of performance goals
     */
    public function index(Request $request)
    {
        $query = PerformanceGoal::with(['employee.department', 'setter']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by employee
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        $goals = $query->orderBy('target_date', 'asc')->paginate(15);

        // Statistics
        $stats = [
            'total_goals' => PerformanceGoal::count(),
            'active_goals' => PerformanceGoal::active()->count(),
            'completed_goals' => PerformanceGoal::where('status', 'completed')->count(),
            'overdue_goals' => PerformanceGoal::overdue()->count(),
        ];

        $employees = Employee::all();

        return view('admin.performance.goals.index', compact('goals', 'stats', 'employees'));
    }

    /**
     * Show the form for creating a new goal
     */
    public function create()
    {
        $employees = Employee::with('department')->get();

        return view('admin.performance.goals.create', compact('employees'));
    }

    /**
     * Store a newly created goal
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:tbl_employee,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:individual,team,department',
            'priority' => 'required|in:low,medium,high',
            'target_date' => 'required|date|after:today',
            'status' => 'required|in:not_started,in_progress,completed,cancelled',
        ]);

        try {
            $goal = PerformanceGoal::create([
                'employee_id' => $request->employee_id,
                'set_by' => auth()->id(),
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'priority' => $request->priority,
                'target_date' => $request->target_date,
                'status' => $request->status,
                'progress_percentage' => 0,
            ]);

            return redirect()->route('admin.performance.goals.index')
                ->with('success', 'Performance goal created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create goal: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified goal
     */
    public function show($id)
    {
        $goal = PerformanceGoal::with(['employee.department', 'setter'])->findOrFail($id);

        return view('admin.performance.goals.show', compact('goal'));
    }

    /**
     * Show the form for editing the specified goal
     */
    public function edit($id)
    {
        $goal = PerformanceGoal::findOrFail($id);
        $employees = Employee::with('department')->get();

        return view('admin.performance.goals.edit', compact('goal', 'employees'));
    }

    /**
     * Update the specified goal
     */
    public function update(Request $request, $id)
    {
        $goal = PerformanceGoal::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:tbl_employee,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:individual,team,department',
            'priority' => 'required|in:low,medium,high',
            'target_date' => 'required|date',
            'status' => 'required|in:not_started,in_progress,completed,cancelled',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        try {
            $data = $request->only([
                'employee_id', 'title', 'description', 'category',
                'priority', 'target_date', 'status', 'progress_percentage', 'notes'
            ]);

            // Set completion date if status changed to completed
            if ($request->status === 'completed' && $goal->status !== 'completed') {
                $data['completion_date'] = now();
                $data['progress_percentage'] = 100;
            }

            $goal->update($data);

            return redirect()->route('admin.performance.goals.index')
                ->with('success', 'Performance goal updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Failed to update goal: ' . $e->getMessage()]);
        }
    }

    /**
     * Update goal progress
     */
    public function updateProgress(Request $request, $id)
    {
        $goal = PerformanceGoal::findOrFail($id);

        $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        try {
            $data = [
                'progress_percentage' => $request->progress_percentage,
            ];

            if ($request->notes) {
                $data['notes'] = $request->notes;
            }

            // Auto-update status based on progress
            if ($request->progress_percentage == 100) {
                $data['status'] = 'completed';
                $data['completion_date'] = now();
            } elseif ($request->progress_percentage > 0 && $goal->status === 'not_started') {
                $data['status'] = 'in_progress';
            }

            $goal->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Goal progress updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update progress: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified goal
     */
    public function destroy($id)
    {
        try {
            $goal = PerformanceGoal::findOrFail($id);
            $goal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Goal deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete goal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get goals for a specific employee (API endpoint)
     */
    public function getEmployeeGoals($employeeId)
    {
        $goals = PerformanceGoal::where('employee_id', $employeeId)
            ->with('setter')
            ->orderBy('target_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'goals' => $goals
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['department_id', 'status', 'year']);
        $timestamp = now()->format('Y-m-d_His');
        $filename = "performance_goals_export_{$timestamp}.xlsx";
        return Excel::download(new PerformanceGoalExport($filters), $filename);
    }
}
