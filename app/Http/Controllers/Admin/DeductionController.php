<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Deduction;
use App\Models\DeductionType;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use Illuminate\Support\Facades\DB;
use App\Exports\DeductionExport;
use Maatwebsite\Excel\Facades\Excel;

class DeductionController extends Controller
{
    //

     public function index(Request $request)
    {
        $search = $request->get('search');
        $typeFilter = $request->get('type');

        $query = Deduction::with('deductionType');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($typeFilter) {
            $query->where('deduction_type_id', $typeFilter);
        }

        $deductions = $query->orderBy('name')->paginate(15);
        $deductionTypes = DeductionType::orderBy('name')->get();

        // Statistics
        $totalDeductions = Deduction::count();
        $totalEmployeesWithDeductions = EmployeeDeduction::distinct('employee_id')->count();

        return view('admin.deductions.index', compact(
            'deductions',
            'deductionTypes',
            'totalDeductions',
            'totalEmployeesWithDeductions'
        ));
    }

    /**
     * Show the form for creating a new deduction
     */
    public function create()
    {
        $deductionTypes = DeductionType::orderBy('name')->get();
        return view('admin.deductions.create', compact('deductionTypes'));
    }

    /**
     * Store a newly created deduction
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'deduction_type_id' => 'required|exists:tbl_deduction_type,id',
        ]);

        Deduction::create($validated);

        return redirect()->route('admin.payroll.settings.index', ['tab' => 'deductions'])
            ->with('success', 'Deduction created successfully.');
    }

    /**
     * Display the specified deduction
     */
    public function show($id)
    {
        $deduction = Deduction::with(['deductionType', 'employees'])->findOrFail($id);

        // Get employees with this deduction
        $employeeDeductions = EmployeeDeduction::with('employee.department')
            ->where('deduction_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.deductions.show', compact('deduction', 'employeeDeductions'));
    }

    /**
     * Show the form for editing the specified deduction
     */
    public function edit($id)
    {
        $deduction = Deduction::findOrFail($id);
        $deductionTypes = DeductionType::orderBy('name')->get();

        return view('admin.deductions.edit', compact('deduction', 'deductionTypes'));
    }

    /**
     * Update the specified deduction
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'deduction_type_id' => 'required|exists:tbl_deduction_type,id',
        ]);

        $deduction = Deduction::findOrFail($id);
        $deduction->update($validated);

        return redirect()->route('admin.payroll.settings.index', ['tab' => 'deductions'])
            ->with('success', 'Deduction updated successfully.');
    }

    /**
     * Remove the specified deduction
     */
    public function destroy($id)
    {
        $deduction = Deduction::findOrFail($id);

        // Check if deduction is assigned to any employees
        $assignedCount = EmployeeDeduction::where('deduction_id', $id)->count();

        if ($assignedCount > 0) {
            return redirect()->route('admin.payroll.settings.index', ['tab' => 'deductions'])
                ->with('error', "Cannot delete deduction. It is assigned to {$assignedCount} employee(s).");
        }

        $deduction->delete();

        return redirect()->route('admin.payroll.settings.index', ['tab' => 'deductions'])
            ->with('success', 'Deduction deleted successfully.');
    }

    /**
     * Show form to assign deduction to employees
     */
    public function assign($id)
    {
        $deduction = Deduction::findOrFail($id);
        $employees = Employee::with('department')
            ->whereHas('jobStatus', function($q) {
                $q->where('name', 'Active');
            })
            ->orderBy('last_name')
            ->get();

        // Get already assigned employees
        $assignedEmployeeIds = EmployeeDeduction::where('deduction_id', $id)
            ->pluck('employee_id')
            ->toArray();

        return view('admin.deductions.assign', compact('deduction', 'employees', 'assignedEmployeeIds'));
    }

    /**
     * Store employee deduction assignment
     */
    public function storeAssignment(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:tbl_employee,id',
            'custom_amount' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        $deduction = Deduction::findOrFail($id);
        $now = now();

        // 1. Get all employees who ALREADY have an active assignment for this deduction
        $existingEmployeeIds = EmployeeDeduction::where('deduction_id', $id)
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
                'deduction_id' => $id,
                'custom_amount' => $validated['custom_amount'] ?? null,
                'effective_from' => $validated['effective_from'],
                'effective_to' => $validated['effective_to'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 4. Perform a single bulk insert
        if (!empty($assignmentsToInsert)) {
            EmployeeDeduction::insert($assignmentsToInsert);
        }

        return redirect()->route('admin.deductions.show', $id)
            ->with('success', 'Deduction assigned to ' . count($newEmployeeIds) . ' new employee(s) successfully.');
    }

    /**
     * Remove deduction from employee
     */
    public function removeAssignment($deductionId, $employeeId)
    {
        EmployeeDeduction::where('deduction_id', $deductionId)
            ->where('employee_id', $employeeId)
            ->delete();

        return redirect()->back()
            ->with('success', 'Deduction removed from employee successfully.');
    }

    public function export(Request $request)
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "deductions_export_{$timestamp}.xlsx";
        return Excel::download(new DeductionExport(), $filename);
    }
}
