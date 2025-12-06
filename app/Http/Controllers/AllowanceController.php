<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use App\Models\Employee;
use App\Models\EmployeeAllowance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AllowanceExport;
use Maatwebsite\Excel\Facades\Excel;

class AllowanceController extends Controller
{
    /**
     * Display a listing of allowances
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $typeFilter = $request->get('type');

        $query = Allowance::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        $allowances = $query->orderBy('name')->paginate(15);

        // Get unique types for filter
        $types = Allowance::distinct()->pluck('type')->filter();

        // Statistics
        $totalAllowances = Allowance::count();
        $totalEmployeesWithAllowances = EmployeeAllowance::distinct('employee_id')->count();

        return view('admin.allowances.index', compact(
            'allowances',
            'types',
            'totalAllowances',
            'totalEmployeesWithAllowances'
        ));
    }

    /**
     * Show the form for creating a new allowance
     */
    public function create()
    {
        return view('admin.allowances.create');
    }

    /**
     * Store a newly created allowance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|max:100',
        ]);

        Allowance::create($validated);

        return redirect()->route('allowances.index')
            ->with('success', 'Allowance created successfully.');
    }

    /**
     * Display the specified allowance
     */
    public function show($id)
    {
        $allowance = Allowance::with('employees')->findOrFail($id);

        // Get employees with this allowance
        $employeeAllowances = EmployeeAllowance::with('employee.department')
            ->where('allowance_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.allowances.show', compact('allowance', 'employeeAllowances'));
    }

    /**
     * Show the form for editing the specified allowance
     */
    public function edit($id)
    {
        $allowance = Allowance::findOrFail($id);
        return view('admin.allowances.edit', compact('allowance'));
    }

    /**
     * Update the specified allowance
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|max:100',
        ]);

        $allowance = Allowance::findOrFail($id);
        $allowance->update($validated);

        return redirect()->route('allowances.index')
            ->with('success', 'Allowance updated successfully.');
    }

    /**
     * Remove the specified allowance
     */
    public function destroy($id)
    {
        $allowance = Allowance::findOrFail($id);

        // Check if allowance is assigned to any employees
        $assignedCount = EmployeeAllowance::where('allowance_id', $id)->count();

        if ($assignedCount > 0) {
            return redirect()->route('allowances.index')
                ->with('error', "Cannot delete allowance. It is assigned to {$assignedCount} employee(s).");
        }

        $allowance->delete();

        return redirect()->route('allowances.index')
            ->with('success', 'Allowance deleted successfully.');
    }

    /**
     * Show form to assign allowance to employees
     */
    public function assign($id)
    {
        $allowance = Allowance::findOrFail($id);
        $employees = Employee::with('department')
            ->whereHas('jobStatus', function($q) {
                $q->where('name', 'Active');
            })
            ->orderBy('last_name')
            ->get();

        // Get already assigned employees
        $assignedEmployeeIds = EmployeeAllowance::where('allowance_id', $id)
            ->pluck('employee_id')
            ->toArray();

        return view('admin.allowances.assign', compact('allowance', 'employees', 'assignedEmployeeIds'));
    }

    /**
     * Store employee allowance assignment
     */
    public function storeAssignment(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:tbl_employee,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        $allowance = Allowance::findOrFail($id);
        $now = now();

        // 1. Get all employees who ALREADY have an active assignment for this allowance
        $existingEmployeeIds = EmployeeAllowance::where('allowance_id', $id)
            ->whereIn('employee_id', $validated['employee_ids'])
            ->where(function ($query) use ($now) {
                $query->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', $now);
            })
            ->pluck('employee_id')
            ->toArray();

        // 2. Filter out the employees who already have it
        $newEmployeeIds = array_diff($validated['employee_ids'], $existingEmployeeIds);

        // 3. Prepare data for bulk insert
        $assignmentsToInsert = [];
        foreach ($newEmployeeIds as $employeeId) {
            $assignmentsToInsert[] = [
                'employee_id' => $employeeId,
                'allowance_id' => $id,
                'effective_from' => $validated['effective_from'],
                'effective_to' => $validated['effective_to'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 4. Perform a single bulk insert
        if (!empty($assignmentsToInsert)) {
            EmployeeAllowance::insert($assignmentsToInsert);
        }

        return redirect()->route('allowances.show', $id)
            ->with('success', 'Allowance assigned to ' . count($newEmployeeIds) . ' new employee(s) successfully.');
    }

    /**
     * Remove allowance from employee
     */
    public function removeAssignment($allowanceId, $employeeId)
    {
        EmployeeAllowance::where('allowance_id', $allowanceId)
            ->where('employee_id', $employeeId)
            ->delete();

        return redirect()->back()
            ->with('success', 'Allowance removed from employee successfully.');
    }

    public function export(Request $request)
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "allowances_export_{$timestamp}.xlsx";
        return Excel::download(new AllowanceExport(), $filename);
    }
}
