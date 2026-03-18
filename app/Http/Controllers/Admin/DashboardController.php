<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Attendance;
use App\Models\PayrollPeriod;
use App\Models\Payroll;
use App\Models\DtrEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display dashboard based on user role
     */
    public function index()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                abort(401, 'Unauthorized');
            }
            
            $roleName = $user->role->name ?? '';
            
            switch($roleName) {
                case 'Super Admin':
                case 'Admin':
                    return $this->adminDashboard();
                case 'HR':
                case 'hr':
                case 'HR Staff':
                    return $this->hrDashboard();
                case 'Employee':
                case 'employee':
                    return $this->employeeDashboard();
                default:
                    abort(403, 'Unknown role: ' . $roleName);
            }
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage(), ['user_id' => auth()->id()]);
            return back()->with('error', 'Failed to load dashboard. Please try again.');
        }
    }

    private function adminDashboard()
    {
        // Cache only the data array — Views contain Closures and cannot be serialized
        $cacheKey = 'dashboard_admin_' . auth()->id() . '_' . now()->format('Y-m-d');

        try {
            $data = cache()->remember($cacheKey, now()->addHours(1), function() {
                return $this->getAdminDashboardData();
            });
        } catch (\Exception $e) {
            Log::error('Admin dashboard cache error: ' . $e->getMessage(), ['user_id' => auth()->id()]);
            cache()->forget($cacheKey);
            $data = $this->getAdminDashboardData();
        }

        return view('admin.dashboard.index', $data);
    }

    /**
     * Get admin dashboard data with optimized queries
     */
    private function getAdminDashboardData()
    {
        try {
            $today = now();

            // Employee Statistics
            $totalEmployees = Employee::count();
            $activeEmployees = Employee::whereHas('jobStatus', function($q) {
                $q->where('name', 'Active');
            })->count();
            $newEmployeesThisMonth = Employee::whereMonth('hire_date', $today->month)
                ->whereYear('hire_date', $today->year)
                ->count();

            // Attendance Statistics for Today - Fixed N+1
            $todayAttendance = Attendance::selectRaw('COUNT(DISTINCT employee_id) as count')
                ->whereDate('created_at', $today)
                ->first();
            $todayAttendanceCount = $todayAttendance->count ?? 0;
            $attendanceRate = $activeEmployees > 0 ? round(($todayAttendanceCount / $activeEmployees) * 100, 1) : 0;

            // Late arrivals today - Fixed to use selectRaw
            $lateArrivals = Attendance::selectRaw('COUNT(DISTINCT employee_id) as count')
                ->whereDate('created_at', $today)
                ->whereTime('created_at', '>', '08:00:00')
                ->whereHas('attendanceType', function($q) {
                    $q->where('name', 'Time In');
                })
                ->first();
            $lateToday = $lateArrivals->count ?? 0;

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

        // Attendance Chart Data (Last 30 Days) - Optimized with single query
        $thirtyDaysAgo = now()->subDays(29);
        $attendanceData = Attendance::selectRaw('DATE(created_at) as date, COUNT(DISTINCT employee_id) as count')
            ->whereDate('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        $attendanceChartData = [];
        $attendanceChartLabels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $attendanceChartLabels[] = $date->format('M d');
            $attendanceChartData[] = $attendanceData[$dateStr] ?? 0;
        }

        // Weekly Hours Chart Data (Last 4 Weeks) - Optimized with single query
        $fourWeeksAgo = now()->subWeeks(4)->startOfWeek();
        $weeklyHours = DtrEntry::selectRaw('WEEK(dtr_date) as week, YEAR(dtr_date) as year, SUM(total_hours) as total')
            ->whereDate('dtr_date', '>=', $fourWeeksAgo)
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get();

        $weeklyHoursData = [];
        $weeklyHoursLabels = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            $weeklyHoursLabels[] = $weekStart->format('M d') . ' - ' . $weekEnd->format('M d');
            
            $total = $weeklyHours
                ->filter(function($item) use ($weekStart) {
                    return $item->year == $weekStart->year && $item->week == $weekStart->weekOfYear;
                })
                ->first()?->total ?? 0;
            $weeklyHoursData[] = round($total, 2);
        }

        // Recent Activities - Eager load relationships
        $recentEmployees = Employee::with(['department', 'position'])
            ->latest()
            ->take(5)
            ->get();

        $recentAttendance = Attendance::with(['employee', 'attendanceType'])
            ->latest()
            ->take(10)
            ->get();

        // Alerts and Notifications - Optimized
        $lowAttendanceDaysData = Attendance::selectRaw('DATE(created_at) as date, COUNT(DISTINCT employee_id) as count')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->having('count', '<', $activeEmployees * 0.7)
            ->get();
        $lowAttendanceDays = $lowAttendanceDaysData->count();

        $missingDtrCount = Employee::whereHas('jobStatus', function($q) {
                $q->where('name', 'Active');
            })
            ->whereDoesntHave('dtrEntries', function($q) use ($today) {
                $q->whereDate('dtr_date', $today->subDay());
            })
            ->count();

            return [
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
        } catch (\Exception $e) {
            Log::error('Admin dashboard error: ' . $e->getMessage(), ['user_id' => auth()->id(), 'trace' => $e->getTraceAsString()]);
            return [
                'totalEmployees' => 0,
                'activeEmployees' => 0,
                'newEmployeesThisMonth' => 0,
                'todayAttendance' => 0,
                'attendanceRate' => 0,
                'lateToday' => 0,
                'departmentCount' => 0,
                'departmentDistribution' => [],
                'currentPayrollPeriod' => null,
                'totalPayrollAmount' => 0,
                'pendingPayrolls' => 0,
                'lastCompletedPeriod' => null,
                'attendanceChartData' => json_encode([]),
                'attendanceChartLabels' => json_encode([]),
                'weeklyHoursData' => json_encode([]),
                'weeklyHoursLabels' => json_encode([]),
                'recentEmployees' => collect(),
                'recentAttendance' => collect(),
                'lowAttendanceDays' => 0,
                'missingDtrCount' => 0,
            ];
        }
    }

    private function hrDashboard()
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
                ? round(($todayAttendance / $activeEmployees) * 100, 1)
                : 0;

            // Leave stats
            $pendingLeaves = \App\Models\Leave::where('workflow_status', 'pending')->count();
            $leaveStats = [
                'pending'  => $pendingLeaves,
                'approved' => \App\Models\Leave::where('workflow_status', 'approved')
                    ->whereMonth('filing_date', $today->month)->count(),
                'rejected' => \App\Models\Leave::where('workflow_status', 'rejected')
                    ->whereMonth('filing_date', $today->month)->count(),
                'total'    => \App\Models\Leave::whereMonth('filing_date', $today->month)->count(),
            ];

            $recentLeaves = \App\Models\Leave::with('user')->latest('filing_date')->take(5)->get();
            $recentHires  = Employee::with(['department', 'position'])->latest('hire_date')->take(6)->get();

            $departmentDistribution = Department::withCount('employees')
                ->orderBy('employees_count', 'desc')->limit(8)->get();

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
