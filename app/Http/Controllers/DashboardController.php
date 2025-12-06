<?php

// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Attendance;
use App\Models\PayrollPeriod;
use App\Models\Payroll;
use Illuminate\Http\Request;
use App\Models\DtrEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roleName = $user->role->name ?? '';
        
        switch($roleName) {
            case 'Super Admin':
            case 'Admin':
                return $this->adminDashboard();
            case 'HR':
            case 'hr':
                return $this->hrDashboard();
            case 'Employee':
            case 'employee':
                return $this->employeeDashboard();
            default:
                abort(403, 'Unknown role: ' . $roleName);
        }
    }

    private function adminDashboard()
    {
        $today = now();

        // Employee Statistics
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::whereHas('jobStatus', function($q) {
            $q->where('name', 'Active');
        })->count();
        $newEmployeesThisMonth = Employee::whereMonth('hire_date', $today->month)
            ->whereYear('hire_date', $today->year)
            ->count();

        // Attendance Statistics for Today
        $todayAttendanceCount = Attendance::whereDate('created_at', $today)->distinct('employee_id')->count('employee_id');
        $attendanceRate = $activeEmployees > 0 ? round(($todayAttendanceCount / $activeEmployees) * 100, 1) : 0;

        // Late arrivals today (assuming time_in after 8:00 AM is late)
        $lateToday = Attendance::whereDate('created_at', $today)
            ->whereTime('created_at', '>', '08:00:00')
            ->whereHas('attendanceType', function($q) {
                $q->where('name', 'Time In');
            })
            ->distinct('employee_id')
            ->count('employee_id');

        // Department Statistics
        $departmentCount = Department::count();
        $departmentDistribution = Department::withCount('employees')
            ->orderBy('employees_count', 'desc')
            ->limit(8)
            ->get()
            ->map(function($dept) {
                return [
                    'name' => $dept->name,
                    'count' => $dept->employees_count
                ];
            });

        // Payroll Statistics
        $currentPayrollPeriod = PayrollPeriod::current()->first();
        $lastCompletedPeriod = PayrollPeriod::where('status', 'completed')
            ->orderBy('end_date', 'desc')
            ->first();

        $totalPayrollAmount = $lastCompletedPeriod
            ? Payroll::where('payroll_period_id', $lastCompletedPeriod->id)->sum('net_pay')
            : 0;

        $pendingPayrolls = $currentPayrollPeriod
            ? ($activeEmployees - Payroll::where('payroll_period_id', $currentPayrollPeriod->id)->count())
            : 0;

        // Attendance Chart Data (Last 30 Days)
        $attendanceChartData = [];
        $attendanceChartLabels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $attendanceChartLabels[] = $date->format('M d');
            $attendanceChartData[] = Attendance::whereDate('created_at', $date)
                ->distinct('employee_id')
                ->count('employee_id');
        }

        // Weekly Hours Chart Data (Last 4 Weeks)
        $weeklyHoursData = [];
        $weeklyHoursLabels = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            $weeklyHoursLabels[] = $weekStart->format('M d') . ' - ' . $weekEnd->format('M d');

            $totalHours = DtrEntry::whereBetween('dtr_date', [$weekStart, $weekEnd])
                ->sum('total_hours');
            $weeklyHoursData[] = round($totalHours, 2);
        }

        // Recent Activities
        $recentEmployees = Employee::with(['department', 'position'])
            ->latest()
            ->take(5)
            ->get();

        $recentAttendance = Attendance::with(['employee', 'attendanceType'])
            ->latest()
            ->take(10)
            ->get();

        // Alerts and Notifications
        $lowAttendanceDays = Attendance::selectRaw('DATE(created_at) as date, COUNT(DISTINCT employee_id) as count')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->having('count', '<', $activeEmployees * 0.7) // Less than 70% attendance
            ->count();

        $missingDtrCount = Employee::whereHas('jobStatus', function($q) {
                $q->where('name', 'Active');
            })
            ->whereDoesntHave('dtrEntries', function($q) use ($today) {
                $q->whereDate('dtr_date', $today->subDay());
            })
            ->count();

        $data = [
            // Employee Stats
            'totalEmployees' => $totalEmployees,
            'activeEmployees' => $activeEmployees,
            'newEmployeesThisMonth' => $newEmployeesThisMonth,

            // Attendance Stats
            'todayAttendance' => $todayAttendanceCount,
            'attendanceRate' => $attendanceRate,
            'lateToday' => $lateToday,

            // Department Stats
            'departmentCount' => $departmentCount,
            'departmentDistribution' => $departmentDistribution,

            // Payroll Stats
            'currentPayrollPeriod' => $currentPayrollPeriod,
            'totalPayrollAmount' => $totalPayrollAmount,
            'pendingPayrolls' => $pendingPayrolls,
            'lastCompletedPeriod' => $lastCompletedPeriod,

            // Chart Data
            'attendanceChartData' => json_encode($attendanceChartData),
            'attendanceChartLabels' => json_encode($attendanceChartLabels),
            'weeklyHoursData' => json_encode($weeklyHoursData),
            'weeklyHoursLabels' => json_encode($weeklyHoursLabels),

            // Recent Data
            'recentEmployees' => $recentEmployees,
            'recentAttendance' => $recentAttendance,

            // Alerts
            'lowAttendanceDays' => $lowAttendanceDays,
            'missingDtrCount' => $missingDtrCount,
        ];

        return view('admin.dashboard.index', $data);
    }

    private function hrDashboard()
    {
        $data = [
            'totalEmployees' => Employee::count(),
            'todayAttendance' => Attendance::whereDate('created_at', today())->count(),
            'pendingLeaves' => 0, // Add your leave model logic
            'recentHires' => Employee::latest()->take(5)->get(),
        ];

        return view('dashboards.hr', $data);
    }

    private function employeeDashboard()
    {
        $employee = auth()->user()->employee;
        
        $data = [
           'todayAttendance' => $this->getTodayAttendance($employee),
            'monthlyStats' => $this->getMonthlyStats($employee),
            'weeklyStats' => $this->getWeeklyStats($employee),
            'weeklyHours' => $this->getWeeklyHours($employee),
            'nextPayroll' => $this->getNextPayroll($employee),
            'currentPayroll' => $this->getCurrentPayroll($employee),
            'nextPayrollDate' => $this->getNextPayrollDate(),
            'leaveBalance' => $this->getLeaveBalance($employee),
            'recentAttendance' => $this->getRecentAttendance($employee),
            'lastBiometricUsage' => $this->getLastBiometricUsage($employee),
        ];

        return view('employee.dashboard.index', $data);
    }

     /**
     * Get today's attendance record
     */
   
}