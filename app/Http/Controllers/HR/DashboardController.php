<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $today = now();

            $totalEmployees  = Employee::count();
            $activeEmployees = Employee::whereHas('jobStatus', fn($q) => $q->where('name', 'Active'))->count();
            $newEmployeesThisMonth = Employee::whereMonth('hire_date', $today->month)
                ->whereYear('hire_date', $today->year)->count();

            $todayAttendance = Attendance::selectRaw('COUNT(DISTINCT employee_id) as count')
                ->whereDate('created_at', $today)->first()->count ?? 0;
            $attendanceRate = $activeEmployees > 0
                ? round(($todayAttendance / $activeEmployees) * 100, 1) : 0;

            $pendingLeaves = \App\Models\Leave::where('workflow_status', 'pending')->count();
            $leaveStats = [
                'pending'  => $pendingLeaves,
                'approved' => \App\Models\Leave::where('workflow_status', 'approved')->whereMonth('filing_date', $today->month)->count(),
                'rejected' => \App\Models\Leave::where('workflow_status', 'rejected')->whereMonth('filing_date', $today->month)->count(),
                'total'    => \App\Models\Leave::whereMonth('filing_date', $today->month)->count(),
            ];

            $recentLeaves = \App\Models\Leave::with('user')->latest('filing_date')->take(5)->get();
            $recentHires  = Employee::with(['department', 'position'])->latest('hire_date')->take(6)->get();
            $departmentDistribution = Department::withCount('employees')->orderBy('employees_count', 'desc')->limit(8)->get();

            return view('hr.dashboard.index', compact(
                'totalEmployees', 'activeEmployees', 'newEmployeesThisMonth',
                'todayAttendance', 'attendanceRate',
                'pendingLeaves', 'leaveStats',
                'recentLeaves', 'recentHires',
                'departmentDistribution'
            ));
        } catch (\Exception $e) {
            Log::error('HR dashboard error: ' . $e->getMessage(), ['user_id' => auth()->id()]);
            return view('hr.dashboard.index', [
                'totalEmployees' => 0, 'activeEmployees' => 0, 'newEmployeesThisMonth' => 0,
                'todayAttendance' => 0, 'attendanceRate' => 0,
                'pendingLeaves' => 0, 'leaveStats' => ['pending'=>0,'approved'=>0,'rejected'=>0,'total'=>0],
                'recentLeaves' => collect(), 'recentHires' => collect(),
                'departmentDistribution' => collect(),
            ]);
        }
    }
}
