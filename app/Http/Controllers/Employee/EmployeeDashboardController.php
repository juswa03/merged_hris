<?php

namespace App\Http\Controllers\Employee;
use  App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\DtrEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $employee = Auth::user()->employee;
        
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
    public function attendanceStatus()
    {
        // Implementation from previous code
    }

    // ... other methods from previous implementation
     private function getTodayAttendance($employee)
    {
        return $employee->attendances()
            ->whereDate('created_at', today())
            ->first();
    }

    /**
     * Get monthly statistics
     */
    private function getMonthlyStats($employee)
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $attendances = $employee->attendances()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();

        $totalHours = 0;
        $workDays = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->check_in && $attendance->check_out) {
                $checkIn = Carbon::parse($attendance->check_in);
                $checkOut = Carbon::parse($attendance->check_out);
                $totalHours += $checkOut->diffInHours($checkIn);
                $workDays++;
            }
        }

        return [
            'total_hours' => $totalHours,
            'work_days' => $workDays,
            'avg_daily' => $workDays > 0 ? round($totalHours / $workDays, 1) : 0
        ];
    }

    /**
     * Get weekly statistics
     */
    private function getWeeklyStats($employee)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $attendances = $employee->attendances()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get();

        $totalHours = 0;
        $workDays = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->check_in && $attendance->check_out) {
                $checkIn = Carbon::parse($attendance->check_in);
                $checkOut = Carbon::parse($attendance->check_out);
                $totalHours += $checkOut->diffInHours($checkIn);
                $workDays++;
            }
        }

        return [
            'total_hours' => $totalHours,
            'work_days' => $workDays,
            'avg_daily' => $workDays > 0 ? round($totalHours / $workDays, 1) : 0
        ];
    }

    /**
     * Get weekly hours for chart
     */
    private function getWeeklyHours($employee)
    {
        $startOfWeek = now()->startOfWeek();
        $hours = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $attendance = $employee->attendances()
                ->whereDate('created_at', $date)
                ->first();

            if ($attendance && $attendance->check_in && $attendance->check_out) {
                $checkIn = Carbon::parse($attendance->check_in);
                $checkOut = Carbon::parse($attendance->check_out);
                $hours[] = $checkOut->diffInHours($checkIn);
            } else {
                $hours[] = 0;
            }
        }

        return $hours;
    }

    /**
     * Get next payroll
     */
    private function getNextPayroll($employee)
    {
        return $employee->payrolls()
            ->where('created_at', '>', now())
            ->orderBy('created_at')
            ->first();
    }

    /**
     * Get current month payroll
     */
    private function getCurrentPayroll($employee)
    {
        return $employee->payrolls()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->first();
    }

    /**
     * Get next payroll date (you might want to calculate this based on your payroll schedule)
     */
    private function getNextPayrollDate()
    {
        // Example: 15th and last day of month
        $today = now();
        $midMonth = $today->copy()->day(15);
        $endMonth = $today->copy()->endOfMonth();

        if ($today->lessThan($midMonth)) {
            return $midMonth;
        } else {
            return $endMonth;
        }
    }

    /**
     * Get leave balance (you'll need to implement your leave logic)
     */
    private function getLeaveBalance($employee)
    {
        // This is a placeholder - implement based on your leave system
        return 15; // Example: 15 days leave balance
    }

    /**
     * Get recent attendance records
     */
    private function getRecentAttendance($employee)
    {
        return $employee->attendances()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Get last biometric usage
     */
    private function getLastBiometricUsage($employee)
    {
        return $employee->dtrEntries()
            ->latest()
            ->first();
    }

    /**
     * Handle check-in request
     */
    public function checkIn(Request $request)
    {
        $employee = Auth::user()->employee;
        
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => today(),
            'check_in' => now(),
            'status' => 'present'
        ]);

        // You might also want to create a DTR entry for biometric tracking
        DtrEntry::create([
            'employee_id' => $employee->id,
            'timestamp' => now(),
            'type' => 'in'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully at ' . now()->format('h:i A'),
            'attendance' => $attendance
        ]);
    }

    /**
     * Handle check-out request
     */
    public function checkOut(Request $request)
    {
        $employee = Auth::user()->employee;
        
        $attendance = $employee->attendances()
            ->whereDate('date', today())
            ->first();

        if ($attendance) {
            $attendance->update([
                'check_out' => now()
            ]);

            // Create DTR entry for check-out
            DtrEntry::create([
                'employee_id' => $employee->id,
                'timestamp' => now(),
                'type' => 'out'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checked out successfully at ' . now()->format('h:i A')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No check-in record found for today'
        ], 400);
    }
}