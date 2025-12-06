<?php

namespace App\Http\Controllers\Employee;
use  App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class EmployeeAttendanceController extends Controller
{
 public function index(Request $request)
    {
        $employee = Auth::user()->employee;
        
        // Date range handling
        $startDate = $request->get('start_date') 
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();
            
        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        // Get attendance records
        $attendances = Attendance::with(['attendanceType', 'attendanceSource'])
            ->where('employee_id', $employee->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Monthly statistics
        $monthlyStats = $this->getMonthlyStats($employee);
        
        // Chart data
        $chartData = $this->getChartData($employee, $startDate, $endDate);


        return view('employee.attendance.index', compact(
            'attendances',
            'monthlyStats',
            'chartData',
            'startDate',
            'endDate'
        ));
    }

    private function getMonthlyStats($employee)
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $attendances = Attendance::with('attendanceType')
            ->where('employee_id', $employee->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();
        Log::info('Attendances ' . $attendances);
        $checkins = $attendances->where('remarks', 'FIRST_IN')->count();
        $checkouts = $attendances->where('attendanceType.name', 'out')->count();
        
        // Calculate hours (you might need to adjust this based on your logic)
        $totalHours = $this->calculateTotalHours($attendances);
        
        $biometricCount = $attendances->where('attendanceSource.name', 'biometric')->count();

        return [
            'present' => $checkins, // Assuming each check-in represents a present day
            'total_hours' => $totalHours,
            'avg_hours' => $checkins > 0 ? round($totalHours / $checkins, 1) : 0,
            'biometric_count' => $biometricCount,
        ];
    }

    private function calculateTotalHours($attendances)
    {
        // This is a simplified calculation - adjust based on your business logic
        // You might need to pair check-ins with check-outs
        $totalMinutes = 0;
        
        $groupedByDate = $attendances->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        });
        
        foreach ($groupedByDate as $date => $records) {
            $checkin = $records->where('attendanceType.name', 'Check-in')->first();
            $checkout = $records->where('attendanceType.name', 'Check-out')->first();
            
            if ($checkin && $checkout) {
                $totalMinutes += $checkout->created_at->diffInMinutes($checkin->created_at);
            }
        }
        
        return round($totalMinutes / 60, 1);
    }

    private function getChartData($employee, $startDate, $endDate)
    {
        // $startOfMonth = now()->startOfMonth();
        // $endOfMonth = now()->endOfMonth();
        $startOfMonth = $startDate;
        $endOfMonth = $endDate;
        $labels = [];
        $checkins = [];
        $checkouts = [];
        
        // Generate data for each day of the month
        $current = $startOfMonth->copy();
        while ($current <= $endOfMonth) {
            $labels[] = $current->format('M d');
            
            $dayCheckins = Attendance::where('employee_id', $employee->id)
                ->whereDate('created_at', $current)
                ->whereHas('attendanceType', function($query) {
                    $query->where('remarks', 'FIRST_IN')
                    ->orWhere('remarks', 'WORK_RESUMED');
                })
                ->count(); 


            $dayCheckouts = Attendance::where('employee_id', $employee->id)
                ->whereDate('created_at', $current)
                ->whereHas('attendanceType', function($query) {
                    $query->where('remarks', 'ON_TIME_OUT')
                    ->orWhere('remarks', 'LATE_OUT')
                    ->orWhere('remarks', 'EARLY_OUT');
                })
                ->count();
                
            $checkins[] = $dayCheckins;
            $checkouts[] = $dayCheckouts;
            
            $current->addDay();
        }
        
        return [
            'labels' => $labels,
            'checkins' => $checkins,
            'checkouts' => $checkouts,
        ];
    }

    public function export(Request $request)
    {
        $employee = Auth::user()->employee;
        
        $startDate = $request->get('start_date') 
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();
            
        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        $attendances = Attendance::with(['attendanceType', 'attendanceSource'])
            ->where('employee_id', $employee->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // You can implement CSV or PDF export here
        // For now, this is a placeholder
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function history()
    {
        // Attendance history implementation
    }


    public function requestForm()
    {
        // Show request adjustment form
    }

    public function submitRequest(Request $request)
    {
        // Handle adjustment request
    }
}