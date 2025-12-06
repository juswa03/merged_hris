<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Department;
use App\Models\AttendanceType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records (Admin View - All Employees)
     */
    public function index(Request $request)
    {
        // Date range handling
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        // Build query for all employees
        $query = Attendance::with(['employee', 'attendanceType', 'attendanceSource'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Apply filters
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->has('attendance_type_id') && $request->attendance_type_id) {
            $query->where('attendance_type_id', $request->attendance_type_id);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $attendances = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $employees = Employee::whereHas('jobStatus', function($q) {
            $q->where('name', 'Active');
        })->orderBy('first_name')->get();

        $departments = Department::orderBy('name')->get();
        $attendanceTypes = AttendanceType::all();

        // Overall statistics
        $stats = $this->getOverallStats($startDate, $endDate);

        return view('admin.attendance.index', compact(
            'attendances',
            'employees',
            'departments',
            'attendanceTypes',
            'stats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get overall attendance statistics for admin dashboard
     */
    private function getOverallStats($startDate, $endDate)
    {
        $attendances = Attendance::with(['attendanceType', 'attendanceSource'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Count by type - use correct filtering
        $totalCheckins = 0;
        $totalCheckouts = 0;
        $biometricCount = 0;
        $manualCount = 0;
        
        Log::info('=== ATTENDANCE DEBUG V2 ===');
        Log::info('Total records: ' . $attendances->count());
        
        foreach ($attendances as $record) {
            $typeName = $record->attendanceType ? $record->attendanceType->name : 'NULL';
            $sourceName = $record->attendanceSource ? $record->attendanceSource->name : 'NULL';
            
            // Type names are "in" and "out" (lowercase)
            if ($record->attendanceType && strtolower($record->attendanceType->name) === 'in') {
                $totalCheckins++;
            }
            if ($record->attendanceType && strtolower($record->attendanceType->name) === 'out') {
                $totalCheckouts++;
            }
            // Source names are "biometric" and "manual" (lowercase)
            if ($record->attendanceSource && strtolower($record->attendanceSource->name) === 'biometric') {
                $biometricCount++;
            }
            if ($record->attendanceSource && strtolower($record->attendanceSource->name) === 'manual') {
                $manualCount++;
            }
        }
        
        Log::info('Results - Checkins: ' . $totalCheckins . ', Checkouts: ' . $totalCheckouts . ', Bio: ' . $biometricCount . ', Manual: ' . $manualCount);

        // Unique employees who checked in
        $uniqueEmployees = 0;
        $checkedInEmployees = [];
        foreach ($attendances as $record) {
            if ($record->attendanceType && strtolower($record->attendanceType->name) === 'in') {
                $checkedInEmployees[$record->employee_id] = true;
            }
        }
        $uniqueEmployees = count($checkedInEmployees);

        return [
            'total_checkins' => $totalCheckins,
            'total_checkouts' => $totalCheckouts,
            'biometric_count' => $biometricCount,
            'manual_count' => $manualCount,
            'unique_employees' => $uniqueEmployees,
            'total_records' => $attendances->count(),
        ];
    }

    /**
     * Show individual employee's attendance details
     */
    public function show($employeeId, Request $request)
    {
        $employee = Employee::findOrFail($employeeId);

        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        $attendances = Attendance::with(['attendanceType', 'attendanceSource'])
            ->where('employee_id', $employeeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate employee-specific stats
        $stats = $this->getEmployeeStats($employeeId, $startDate, $endDate);

        return view('admin.attendance.show', compact(
            'employee',
            'attendances',
            'stats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get statistics for a specific employee
     */
    private function getEmployeeStats($employeeId, $startDate, $endDate)
    {
        $attendances = Attendance::with('attendanceType')
            ->where('employee_id', $employeeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Count using foreach loop
        $checkins = 0;
        $checkouts = 0;
        foreach ($attendances as $record) {
            if ($record->attendanceType && strtolower($record->attendanceType->name) === 'in') {
                $checkins++;
            }
            if ($record->attendanceType && strtolower($record->attendanceType->name) === 'out') {
                $checkouts++;
            }
        }
        
        $totalHours = $this->calculateTotalHours($attendances);

        return [
            'total_days' => $checkins,
            'total_hours' => $totalHours,
            'avg_hours_per_day' => $checkins > 0 ? round($totalHours / $checkins, 1) : 0,
            'total_checkins' => $checkins,
            'total_checkouts' => $checkouts,
        ];
    }

    /**
     * Calculate total hours from attendance records
     */
    private function calculateTotalHours($attendances)
    {
        $totalMinutes = 0;

        $groupedByDate = $attendances->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        });

        foreach ($groupedByDate as $date => $records) {
            $checkin = null;
            $checkout = null;
            
            // Find check-in and check-out records
            foreach ($records as $record) {
                if ($record->attendanceType && strtolower($record->attendanceType->name) === 'in') {
                    $checkin = $record;
                }
                if ($record->attendanceType && strtolower($record->attendanceType->name) === 'out') {
                    $checkout = $record;
                }
            }

            if ($checkin && $checkout) {
                $totalMinutes += $checkout->created_at->diffInMinutes($checkin->created_at);
            }
        }

        return round($totalMinutes / 60, 1);
    }

    /**
     * Manual attendance entry (for corrections or late entries)
     */
    public function create()
    {
        $employees = Employee::whereHas('jobStatus', function($q) {
            $q->where('name', 'Active');
        })->orderBy('first_name')->get();

        $attendanceTypes = AttendanceType::all();

        return view('admin.attendance.create', compact('employees', 'attendanceTypes'));
    }

    /**
     * Store manual attendance entry
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:tbl_employee,id',
            'attendance_type_id' => 'required|exists:tbl_attendance_types,id',
            'attendance_date' => 'required|date',
            'attendance_time' => 'required',
            'remarks' => 'nullable|string',
        ]);

        try {
            $attendanceDateTime = Carbon::parse($request->attendance_date . ' ' . $request->attendance_time);

            Attendance::create([
                'employee_id' => $request->employee_id,
                'attendance_type_id' => $request->attendance_type_id,
                'attendance_source_id' => 3, // Manual source
                'remarks' => $request->remarks,
                'created_at' => $attendanceDateTime,
                'updated_at' => $attendanceDateTime,
            ]);

            return redirect()->route('attendance.index')
                ->with('success', 'Manual attendance record created successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create attendance record: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete attendance record
     */
    public function destroy($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance record deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attendance record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export attendance records to CSV
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        $query = Attendance::with(['employee', 'attendanceType', 'attendanceSource'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Apply same filters as index
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $attendances = $query->orderBy('created_at', 'desc')->get();

        $filename = 'attendance_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, ['Date', 'Time', 'Employee', 'Department', 'Type', 'Source']);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->created_at->format('Y-m-d'),
                    $attendance->created_at->format('H:i:s'),
                    $attendance->employee->full_name ?? 'N/A',
                    $attendance->employee->department->department_name ?? 'N/A',
                    $attendance->attendanceType->name ?? 'N/A',
                    $attendance->attendanceSource->name ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show attendance reports page
     */
    public function reports(Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        $employees = Employee::whereHas('jobStatus', function($q) {
            $q->where('name', 'Active');
        })->orderBy('first_name')->get();

        $departments = Department::orderBy('name')->get();
        
        $overallStats = $this->getOverallStats($startDate, $endDate);

        return view('admin.attendance.reports', compact(
            'employees',
            'departments',
            'overallStats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get attendance summary for all employees in detailed report
     */
    public function getAttendanceSummary(Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        $query = Attendance::with(['employee', 'attendanceType', 'attendanceSource'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $attendances = $query->get();

        // Group by employee
        $employeeData = [];
        foreach ($attendances->groupBy('employee_id') as $employeeId => $records) {
            $employee = $records->first()->employee;
            
            // Count using foreach loops
            $checkins = 0;
            $checkouts = 0;
            $biometric = 0;
            $manual = 0;
            
            foreach ($records as $record) {
                if ($record->attendanceType && strtolower($record->attendanceType->name) === 'in') {
                    $checkins++;
                }
                if ($record->attendanceType && strtolower($record->attendanceType->name) === 'out') {
                    $checkouts++;
                }
                if ($record->attendanceSource && strtolower($record->attendanceSource->name) === 'biometric') {
                    $biometric++;
                }
                if ($record->attendanceSource && strtolower($record->attendanceSource->name) === 'manual') {
                    $manual++;
                }
            }

            $employeeData[] = [
                'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                'department' => $employee->department->name ?? 'N/A',
                'checkins' => $checkins,
                'checkouts' => $checkouts,
                'biometric' => $biometric,
                'manual' => $manual,
                'total' => $records->count()
            ];
        }

        return response()->json($employeeData);
    }

    /**
     * Get employee attendance records
     */
    public function getEmployeeAttendance($employeeId, Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        $attendances = Attendance::with(['attendanceType', 'attendanceSource'])
            ->where('employee_id', $employeeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Count using foreach loop
        $checkins = 0;
        $checkouts = 0;
        $biometric = 0;
        
        foreach ($attendances as $record) {
            if ($record->attendanceType && strtolower($record->attendanceType->name) === 'in') {
                $checkins++;
            }
            if ($record->attendanceType && strtolower($record->attendanceType->name) === 'out') {
                $checkouts++;
            }
            if ($record->attendanceSource && strtolower($record->attendanceSource->name) === 'biometric') {
                $biometric++;
            }
        }
        
        $total = $attendances->count();
        $biometricPercent = $total > 0 ? round(($biometric / $total) * 100) : 0;

        $records = $attendances->map(function($record) {
            $typeName = $record->attendanceType ? $record->attendanceType->name : 'N/A';
            $sourceName = $record->attendanceSource ? $record->attendanceSource->name : 'N/A';
            
            // Convert short names to display names
            $displayType = strtolower($typeName) === 'in' ? 'Check-in' : (strtolower($typeName) === 'out' ? 'Check-out' : $typeName);
            $displaySource = strtolower($sourceName) === 'biometric' ? 'Biometric' : (strtolower($sourceName) === 'manual' ? 'Manual' : $sourceName);
            
            return [
                'date' => $record->created_at->format('Y-m-d'),
                'time' => $record->created_at->format('H:i:s'),
                'type' => $displayType,
                'source' => $displaySource
            ];
        });

        return response()->json([
            'stats' => [
                'total' => $total,
                'checkins' => $checkins,
                'checkouts' => $checkouts,
                'biometric_percent' => $biometricPercent
            ],
            'records' => $records
        ]);
    }

    /**
     * Get department-wise attendance summary
     */
    public function departmentSummary(Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        $summary = DB::table('tbl_attendances')
            ->join('tbl_employee', 'tbl_attendances.employee_id', '=', 'tbl_employee.id')
            ->join('tbl_departments', 'tbl_employee.department_id', '=', 'tbl_departments.id')
            ->join('tbl_attendance_types', 'tbl_attendances.attendance_type_id', '=', 'tbl_attendance_types.id')
            ->whereBetween('tbl_attendances.created_at', [$startDate, $endDate])
            ->where('tbl_attendance_types.name', 'Check-in')
            ->select('tbl_departments.name as department_name', DB::raw('COUNT(*) as total_checkins'))
            ->groupBy('tbl_departments.id', 'tbl_departments.name')
            ->orderBy('total_checkins', 'desc')
            ->get();

        return response()->json($summary);
    }
}
