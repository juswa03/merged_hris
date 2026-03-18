<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveCreditEarning;
use Illuminate\Http\Request;

class LeaveBalanceController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        $query = Employee::with(['department', 'jobStatus']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('middle_name','like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('last_name')->paginate(20)->withQueryString();

        // Attach balances for this year
        $employeeIds = $employees->pluck('id');
        $balances    = LeaveBalance::whereIn('employee_id', $employeeIds)
            ->where('year', $year)
            ->get()
            ->groupBy('employee_id');

        $departments = \App\Models\Department::orderBy('name')->pluck('name', 'id');

        $availableYears = LeaveBalance::selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->push(now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        $stats = [
            'total_employees'     => Employee::count(),
            'with_balances'       => LeaveBalance::where('year', $year)->distinct('employee_id')->count('employee_id'),
            'without_balances'    => Employee::count() - LeaveBalance::where('year', $year)->distinct('employee_id')->count('employee_id'),
        ];

        return view('admin.leave-balance.index', compact(
            'employees', 'balances', 'year', 'departments', 'availableYears', 'stats'
        ));
    }

    public function show(Request $request, Employee $employee)
    {
        $year     = (int) $request->get('year', now()->year);
        $employee->load(['department', 'position', 'jobStatus', 'user']);

        $balances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', $year)
            ->get()
            ->keyBy('leave_type');

        $creditHistory = LeaveCreditEarning::where('employee_id', $employee->id)
            ->orderBy('period_from', 'desc')
            ->paginate(15);

        $availableYears = LeaveBalance::where('employee_id', $employee->id)
            ->selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->push(now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        $leaveTypes = LeaveBalance::LEAVE_TYPES;

        return view('admin.leave-balance.show', compact(
            'employee', 'balances', 'creditHistory', 'year', 'availableYears', 'leaveTypes'
        ));
    }

    public function adjust(Employee $employee)
    {
        $employee->load(['department']);
        $leaveTypes = LeaveBalance::LEAVE_TYPES;
        $year       = now()->year;

        $existingBalances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', $year)
            ->get()
            ->keyBy('leave_type');

        return view('admin.leave-balance.adjust', compact(
            'employee', 'leaveTypes', 'year', 'existingBalances'
        ));
    }

    public function saveAdjustment(Request $request, Employee $employee)
    {
        $request->validate([
            'year'            => 'required|integer|min:2000|max:2100',
            'leave_type'      => 'required|string|in:' . implode(',', array_keys(LeaveBalance::LEAVE_TYPES)),
            'opening_balance' => 'required|numeric|min:0',
            'earned'          => 'required|numeric|min:0',
            'used'            => 'required|numeric|min:0',
        ]);

        $opening = (float) $request->opening_balance;
        $earned  = (float) $request->earned;
        $used    = (float) $request->used;
        $closing = $opening + $earned - $used;

        LeaveBalance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'leave_type'  => $request->leave_type,
                'year'        => $request->year,
            ],
            [
                'opening_balance' => $opening,
                'earned'          => $earned,
                'used'            => $used,
                'closing_balance' => max(0, $closing),
            ]
        );

        return redirect()->route('admin.leave-balance.show', $employee->id)
            ->with('success', 'Leave balance updated successfully.');
    }

    public function grantCredits(Request $request, Employee $employee)
    {
        $request->validate([
            'leave_type'    => 'required|string|in:' . implode(',', array_keys(LeaveBalance::LEAVE_TYPES)),
            'credits_earned'=> 'required|numeric|min:0.01',
            'period_from'   => 'required|date',
            'period_to'     => 'required|date|after_or_equal:period_from',
            'remarks'       => 'nullable|string|max:500',
        ]);

        LeaveCreditEarning::create([
            'employee_id'   => $employee->id,
            'leave_type'    => $request->leave_type,
            'credits_earned'=> $request->credits_earned,
            'period_from'   => $request->period_from,
            'period_to'     => $request->period_to,
            'remarks'       => $request->remarks,
        ]);

        // Also update or increment balance for the current year
        $year = now()->year;
        LeaveBalance::updateOrCreate(
            ['employee_id' => $employee->id, 'leave_type' => $request->leave_type, 'year' => $year],
            ['opening_balance' => 0, 'earned' => 0, 'used' => 0, 'closing_balance' => 0]
        );

        LeaveBalance::where('employee_id', $employee->id)
            ->where('leave_type', $request->leave_type)
            ->where('year', $year)
            ->increment('earned', $request->credits_earned);

        LeaveBalance::where('employee_id', $employee->id)
            ->where('leave_type', $request->leave_type)
            ->where('year', $year)
            ->update([
                'closing_balance' => \DB::raw('opening_balance + earned - used'),
            ]);

        return back()->with('success', 'Credits granted and balance updated.');
    }
}
