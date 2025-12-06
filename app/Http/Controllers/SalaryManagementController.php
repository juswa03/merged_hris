<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\SalaryGrade;
use App\Models\SalaryHistory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Exports\SalaryHistoryExport;
use Maatwebsite\Excel\Facades\Excel;

class SalaryManagementController extends Controller
{
    /**
     * Display salary overview for all employees
     */
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'position'])
            ->whereNotNull('basic_salary');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%");
            });
        }

        // Department filter
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Salary grade filter
        if ($request->has('salary_grade') && $request->salary_grade) {
            $query->where('salary_grade', $request->salary_grade);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'last_name');
        $sortOrder = $request->get('sort_order', 'asc');

        if ($sortBy === 'salary') {
            $query->orderBy('basic_salary', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $employees = $query->paginate(15);

        // Statistics
        $stats = [
            'total_employees' => Employee::whereNotNull('basic_salary')->count(),
            'total_monthly_payroll' => Employee::sum('basic_salary'),
            'average_salary' => Employee::avg('basic_salary'),
            'highest_salary' => Employee::max('basic_salary'),
            'lowest_salary' => Employee::min('basic_salary'),
            'employees_with_grades' => Employee::whereNotNull('salary_grade')->whereNotNull('salary_step')->count(),
        ];

        // Department breakdown
        $departmentSalaries = Employee::select('department_id', DB::raw('SUM(basic_salary) as total_salary'), DB::raw('COUNT(*) as employee_count'))
            ->whereNotNull('basic_salary')
            ->whereNotNull('department_id')
            ->groupBy('department_id')
            ->with('department')
            ->get();

        // Salary grade distribution
        $gradeDistribution = Employee::select('salary_grade', DB::raw('COUNT(*) as count'))
            ->whereNotNull('salary_grade')
            ->groupBy('salary_grade')
            ->orderBy('salary_grade')
            ->get();

        $departments = Department::orderBy('name')->get();

        return view('admin.salaries.index', compact(
            'employees',
            'stats',
            'departmentSalaries',
            'gradeDistribution',
            'departments'
        ));
    }

    /**
     * Show detailed salary information for a specific employee
     */
    public function show($id)
    {
        Log::info("Viewing salary details for employee ID: {$id}");
        $employee = Employee::with(['department', 'position', 'activeDeductions', 'activeAllowances'])
            ->findOrFail($id);

        // Calculate salary breakdown
        $breakdown = [
            'basic_salary' => $employee->basic_salary ?? 0,
            'allowances' => $employee->activeAllowances->sum('amount'),
            'deductions' => $employee->activeDeductions->sum(function($deduction) use ($employee) {
                return $deduction->pivot->custom_amount ?? $deduction->amount;
            }),
        ];

        $breakdown['gross_salary'] = $breakdown['basic_salary'] + $breakdown['allowances'];
        $breakdown['net_salary'] = $breakdown['gross_salary'] - $breakdown['deductions'];

        // Get salary grade info if assigned
        $salaryGradeInfo = null;
        if ($employee->salary_grade && $employee->salary_step) {
            $expectedSalary = SalaryGrade::getSalary($employee->salary_grade, $employee->salary_step);
            $salaryGradeInfo = [
                'grade' => $employee->salary_grade,
                'step' => $employee->salary_step,
                'expected_salary' => $expectedSalary,
                'matches' => abs(($employee->basic_salary ?? 0) - ($expectedSalary ?? 0)) < 0.01,
            ];
        }

        return view('admin.salaries.show', compact('employee', 'breakdown', 'salaryGradeInfo'));
    }

    /**
     * Show form to adjust employee salary
     */
    public function adjustForm($id)
    {
        $employee = Employee::with(['department', 'position'])->findOrFail($id);

        return view('admin.salaries.adjust', compact('employee'));
    }

    /**
     * Process salary adjustment
     */
    public function adjust(Request $request, $id)
    {
        $request->validate([
            'adjustment_type' => 'required|string|in:increase,decrease,promotion,demotion,regularization,other',
            'new_salary' => 'nullable|numeric|min:0',
            'reason' => 'required|string|max:500',
            'effective_date' => 'required|date',
            'salary_grade' => 'nullable|integer|min:1|max:33',
            'salary_step' => 'nullable|integer|min:1|max:8',
        ]);

        if ($request->salary_grade && !$request->salary_step) {
            return back()->withInput()
                ->withErrors(['salary_step' => 'Salary step is required when assigning a salary grade.']);
        }

        if ((!$request->salary_grade || !$request->salary_step) && $request->new_salary === null) {
            return back()->withInput()
                ->withErrors(['new_salary' => 'New salary is required when no salary grade is selected.']);
        }

        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($id);
            $oldSalary = $employee->basic_salary;

            $computedSalary = $request->new_salary;

            if ($request->salary_grade && $request->salary_step) {
                $gradeSalary = SalaryGrade::getSalary($request->salary_grade, $request->salary_step);

                if ($gradeSalary === null) {
                    DB::rollBack();

                    return back()->withInput()
                        ->withErrors(['salary_grade' => 'No salary schedule found for the selected grade and step.']);
                }

                $computedSalary = $gradeSalary;
            }

            // Log salary history before making changes
            SalaryHistory::logChange(
                $employee,
                $computedSalary,
                $request->adjustment_type,
                $request->reason,
                $request->effective_date,
                $request->salary_grade,
                $request->salary_step
            );

            // Update employee salary info
            $employee->basic_salary = $computedSalary;

            if ($request->salary_grade && $request->salary_step) {
                $employee->salary_grade = $request->salary_grade;
                $employee->salary_step = $request->salary_step;
            }

            $employee->save();

            if ($request->salary_grade && $request->salary_step) {
                $employee->syncSalaryFromGrade();
            }

            DB::commit();

            return redirect()->route('salaries.show', $employee->id)
                ->with('success', 'Salary adjusted successfully from ₱' . number_format($oldSalary, 2) . ' to ₱' . number_format($computedSalary, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Failed to adjust salary: ' . $e->getMessage()]);
        }

    }

    /**
     * Show salary reports
     */
    public function reports(Request $request)
    {
        // Salary range distribution
        $salaryRanges = [
            '0-15000' => Employee::whereBetween('basic_salary', [0, 15000])->count(),
            '15001-25000' => Employee::whereBetween('basic_salary', [15001, 25000])->count(),
            '25001-40000' => Employee::whereBetween('basic_salary', [25001, 40000])->count(),
            '60001-100000' => Employee::whereBetween('basic_salary', [60001, 100000])->count(),
            '100001+' => Employee::where('basic_salary', '>', 100000)->count(),
        ];

        // Employees earning below position minimum
        $belowMinimum = Employee::whereNotNull('position_id')
            ->whereHas('position', function($q) {
                $q->whereNotNull('min_salary');
            })
            ->get()
            ->filter(function($emp) {
                return $emp->basic_salary < ($emp->position->min_salary ?? 0);
            });

        // Employees earning above position maximum
        $aboveMaximum = Employee::whereNotNull('position_id')
            ->whereHas('position', function($q) {
                $q->whereNotNull('max_salary');
            })
            ->get()
            ->filter(function($emp) {
                return $emp->basic_salary > ($emp->position->max_salary ?? 0);
            });

        // Employees with salary grade mismatch
        $gradeMismatches = Employee::whereNotNull('salary_grade')
            ->whereNotNull('salary_step')
            ->get()
            ->filter(function($emp) {
                return !$emp->isSalaryMatchingGrade();
            });

        return view('admin.salaries.reports', compact(
            'salaryRanges',
            'belowMinimum',
            'aboveMaximum',
            'gradeMismatches'
        ));
    }

    /**
     * Bulk salary adjustment form
     */
    public function bulkAdjustForm()
    {
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();

        return view('admin.salaries.bulk-adjust', compact('departments', 'positions'));
    }

    /**
     * Process bulk salary adjustment
     */
    public function bulkAdjust(Request $request)
    {
        $request->validate([
            'adjustment_type' => 'required|in:percentage,fixed_amount',
            'adjustment_value' => 'required|numeric',
            'filter_department' => 'nullable|exists:tbl_departments,id',
            'filter_position' => 'nullable|exists:tbl_positions,id',
            'filter_grade' => 'nullable|integer|min:1|max:33',
            'reason' => 'required|string|max:500',
            'effective_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $query = Employee::whereNotNull('basic_salary');

            // Apply filters
            if ($request->filter_department) {
                $query->where('department_id', $request->filter_department);
            }

            if ($request->filter_position) {
                $query->where('position_id', $request->filter_position);
            }

            if ($request->filter_grade) {
                $query->where('salary_grade', $request->filter_grade);
            }

            $employees = $query->get();
            $updated = 0;
            $skippedGradeManaged = 0;

            foreach ($employees as $employee) {
                // Skip employees whose salaries are controlled by the salary grade matrix.
                if ($employee->salary_grade && $employee->salary_step) {
                    $employee->syncSalaryFromGrade();
                    $skippedGradeManaged++;
                    continue;
                }

                $oldSalary = $employee->basic_salary;

                if ($request->adjustment_type === 'percentage') {
                    $newSalary = $oldSalary * (1 + ($request->adjustment_value / 100));
                } else {
                    $newSalary = $oldSalary + $request->adjustment_value;
                }

                $newSalaryRounded = round($newSalary, 2);

                // Log salary history for each employee
                SalaryHistory::logChange(
                    $employee,
                    $newSalaryRounded,
                    'bulk_adjustment',
                    $request->reason,
                    $request->effective_date
                );

                $employee->basic_salary = $newSalaryRounded;
                $employee->save();
                $updated++;
            }

            DB::commit();

            $message = "Bulk salary adjustment completed. Updated {$updated} employees.";

            if ($skippedGradeManaged > 0) {
                $message .= " Skipped {$skippedGradeManaged} employees managed by salary grade.";
            }

            return redirect()->route('salaries.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Failed to perform bulk adjustment: ' . $e->getMessage()]);
        }
    }

    public function exportHistory(Request $request)
    {
        $filters = $request->only(['employee_id', 'department_id', 'date_from', 'date_to']);
        $timestamp = now()->format('Y-m-d_His');
        $filename = "salary_history_export_{$timestamp}.xlsx";
        return Excel::download(new SalaryHistoryExport($filters), $filename);
    }
}
