<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SalaryGrade;
use App\Models\Employee;
use App\Exports\SalaryGradeExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
class SalaryGradeController extends Controller
{
    //
        /**
     * Display all salary schedules
     */
    public function index(Request $request)
    {
        // Get all unique effective dates
        $effectiveDates = SalaryGrade::getEffectiveDates();

        // Get selected effective date or use current
        $selectedDate = $request->get('effective_date', SalaryGrade::getCurrentEffectiveDate());

        // Get salary grades for selected date
        $salaryGrades = SalaryGrade::where('effective_date', $selectedDate)
            ->orderBy('grade')
            ->orderBy('step')
            ->get()
            ->groupBy('grade');

        // Statistics
        $stats = [
            'total_schedules' => SalaryGrade::select('effective_date')->distinct()->count(),
            'total_grades' => SalaryGrade::where('effective_date', $selectedDate)->select('grade')->distinct()->count(),
            'min_salary' => SalaryGrade::where('effective_date', $selectedDate)->min('amount'),
            'max_salary' => SalaryGrade::where('effective_date', $selectedDate)->max('amount'),
            'employees_with_grades' => Employee::whereNotNull('salary_grade')->whereNotNull('salary_step')->count(),
        ];

        return view('admin.salary-grades.index', compact('salaryGrades', 'effectiveDates', 'selectedDate', 'stats'));
    }

    /**
     * Show form to create new salary schedule
     */
    public function create()
    {
        return view('admin.salary-grades.create');
    }

    /**
     * Store a new salary schedule
     */
    public function store(Request $request)
    {
        $numGrades = (int)$request->input('num_grades', 33);
        $numSteps = (int)$request->input('num_steps', 8);

        $request->validate([
            'effective_date' => 'required|date',
            'tranche' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'num_grades' => 'required|integer|min:1|max:50',
            'num_steps' => 'required|integer|min:1|max:15',
            'grades' => 'required|array',
            'grades.*.grade' => "required|integer|min:1|max:{$numGrades}",
            'grades.*.step' => "required|integer|min:1|max:{$numSteps}",
            'grades.*.amount' => 'required|numeric|min:0',
        ], [
            'num_grades.max' => 'Number of grades cannot exceed 50',
            'num_steps.max' => 'Number of steps cannot exceed 15',
            'grades.*.grade.max' => "Grade number cannot exceed {$numGrades}",
            'grades.*.step.max' => "Step number cannot exceed {$numSteps}",
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->grades as $gradeData) {
                SalaryGrade::create([
                    'grade' => $gradeData['grade'],
                    'step' => $gradeData['step'],
                    'amount' => $gradeData['amount'],
                    'effective_date' => $request->effective_date,
                    'tranche' => $request->tranche,
                    'remarks' => $request->remarks,
                    'is_active' => true,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.salary-grades.index')
                ->with('success', 'Salary schedule created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create salary schedule: ' . $e->getMessage()]);
        }
    }

    /**
     * Show edit form for a specific salary grade
     */
    public function edit($id)
    {
        $salaryGrade = SalaryGrade::findOrFail($id);
        return view('admin.salary-grades.edit', compact('salaryGrade'));
    }

    /**
     * Update a specific salary grade
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $salaryGrade = SalaryGrade::findOrFail($id);
            $salaryGrade->update([
                'amount' => $request->amount,
                'is_active' => $request->has('is_active') ? $request->is_active : $salaryGrade->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Salary grade updated successfully.',
                'data' => $salaryGrade
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update salary grade: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get salary for a specific grade and step (API)
     */
    public function getSalary(Request $request)
    {
        $request->validate([
            'grade' => 'required|integer|min:1|max:33',
            'step' => 'required|integer|min:1|max:8',
        ]);

        $salary = SalaryGrade::getSalary($request->grade, $request->step);

        if ($salary === null) {
            return response()->json([
                'success' => false,
                'message' => 'Salary grade not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'salary' => $salary,
            'formatted_salary' => '₱' . number_format($salary, 2),
            'grade' => $request->grade,
            'step' => $request->step,
        ]);
    }

    /**
     * Deactivate an entire salary schedule
     */
    public function deactivateSchedule(Request $request)
    {
        $request->validate([
            'effective_date' => 'required|date',
        ]);

        try {
            SalaryGrade::where('effective_date', $request->effective_date)
                ->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Salary schedule deactivated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update employee salaries based on their assigned grades
     */
    public function updateEmployeeSalaries(Request $request)
    {
        try {
            $employees = Employee::whereNotNull('salary_grade')
                ->whereNotNull('salary_step')
                ->get();

            $updated = 0;
            foreach ($employees as $employee) {
                $salary = $employee->getSalaryFromGrade();
                if ($salary !== null && $employee->basic_salary != $salary) {
                    $employee->basic_salary = $salary;
                    $employee->save();
                    $updated++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Updated {$updated} employee salaries based on salary grades.",
                'updated_count' => $updated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update employee salaries: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $timestamp = now()->format('Y-m-d_His');
        $filename = "salary_grades_export_{$timestamp}.xlsx";
        return Excel::download(new SalaryGradeExport(), $filename);
    }

    /**
     * Remove the specified salary grade from storage.
     */
    public function destroy($id)
    {
        try {
            $salaryGrade = SalaryGrade::findOrFail($id);
            $salaryGrade->delete();

            return response()->json([
                'success' => true,
                'message' => 'Salary grade deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete salary grade: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an entire salary schedule
     */
    public function destroySchedule(Request $request)
    {
        $request->validate([
            'effective_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $count = SalaryGrade::where('effective_date', $request->effective_date)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Salary schedule deleted successfully. Removed {$count} entries."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete schedule: ' . $e->getMessage()
            ], 500);
        }
    }
}
