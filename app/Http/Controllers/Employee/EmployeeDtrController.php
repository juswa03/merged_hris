<?php

namespace App\Http\Controllers\Employee;
use  App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\DtrEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeDtrController extends Controller
{
   public function index(Request $request)
    {
        $employee = Auth::user()->employee;
        $month = $request->get('month', now()->format('Y-m'));
        
        // Parse the selected month
        $selectedDate = Carbon::parse($month);
        $monthYear = $selectedDate->format('F Y');
        
        // Get DTR entries for the selected month
        $dtrEntries = DtrEntry::where('employee_id', $employee->id)
            ->whereYear('dtr_date', $selectedDate->year)
            ->whereMonth('dtr_date', $selectedDate->month)
            ->orderBy('dtr_date')
            ->get();
// dd($dtrEntries->first()->toArray());

        // Calculate totals
        $totalUndertime = $dtrEntries->sum('under_time_minutes');
        Log::info("Undert_time_minutes: " . $totalUndertime);
        $totalMinutes = $dtrEntries->sum(function($entry) {
            return ($entry->total_hours * 60) + $entry->total_minutes;
        });
        
        $totalOvertimeHours = $dtrEntries->sum('over_time_hours');
        $totalOvertimeMinutes = $dtrEntries->sum('over_time_minutes');

        // Calculate summary statistics
        $summary = $this->calculateSummary($dtrEntries, $selectedDate);
 
        // Get certifying officers (you might want to get these from your database)
        $certifiedBy = (object)['name' => 'SUPERVISOR NAME'];
        $verifiedBy = (object)['name' => 'HR OFFICER NAME'];

        return view('employee.dtr.index', compact(
            'employee',
            'dtrEntries',
            'monthYear',
            'totalUndertime',
            'totalMinutes',
            'totalOvertimeHours',
            'totalOvertimeMinutes',
            'certifiedBy',
            'verifiedBy',
            'summary'
        ));
    }

private function calculateSummary($dtrEntries, $selectedDate)
{
    $workDays = 0;
    $absentDays = 0;
    $totalHours = 0;
    $totalMinutes = 0;
    $totalUndertime = 0;

    $startDate = $selectedDate->copy()->startOfMonth();
    $endDate = $selectedDate->copy()->endOfMonth();

    $current = $startDate->copy();

    while ($current <= $endDate) {

        // Skip weekends (Saturday, Sunday)
        if ($current->isWeekend()) {
            $current->addDay();
            continue;
        }

        $workDays++;

        // Check if there’s a DTR entry for this date
        $entry = $dtrEntries->firstWhere('dtr_date', $current->toDateString());

        if ($entry) {
            // Add up worked hours and undertime
            $totalUndertime += $entry->undertime_minutes ?? 0;
            $totalHours += $entry->total_hours ?? 0;
            $totalMinutes += $entry->total_minutes ?? 0;
        } else {
            // Mark as absent if no entry found
            $absentDays++;
        }

        $current->addDay();
    }

    // Convert extra minutes to hours
    $totalHours += floor($totalMinutes / 60);
    $totalMinutes = $totalMinutes % 60;

    return [
        'total_work_days' => $workDays,
        'absent_days' => $absentDays,
        'total_hours' => $totalHours,
        'total_minutes' => $totalMinutes,
        'total_undertime' => $totalUndertime,
    ];
}


    public function exportPdf(Request $request)
    {
        $employee = Auth::user()->employee;
        $month = $request->get('month', now()->format('Y-m'));
        
        // Parse the selected month
        $selectedDate = Carbon::parse($month);
        $monthYear = $selectedDate->format('F Y');
        
        // Get DTR entries for the selected month
        $dtrEntries = DtrEntry::where('employee_id', $employee->id)
            ->whereYear('dtr_date', $selectedDate->year)
            ->whereMonth('dtr_date', $selectedDate->month)
            ->orderBy('dtr_date')
            ->get();

        // Calculate totals
        $totalUndertime = $dtrEntries->sum('under_time_minutes');
        
        $totalOvertimeHours = $dtrEntries->sum('over_time_hours');
        $totalOvertimeMinutes = $dtrEntries->sum('over_time_minutes');

        // Get certifying officers
        $certifiedBy = (object)['name' => 'SUPERVISOR NAME'];
        $verifiedBy = (object)['name' => 'HR OFFICER NAME'];

        $pdf = Pdf::loadView('employee.dtr.pdf', compact(
            'employee',
            'dtrEntries',
            'monthYear',
            'totalUndertime',
            'totalOvertimeHours',
            'totalOvertimeMinutes',
            'certifiedBy',
            'verifiedBy'
        ));

        return $pdf->download('DTR_' . $employee->last_name . '_' . $month . '.pdf');
    }

    /**
     * Resolve which employee record to load.
     */
    private function resolveEmployee(Request $request, $user)
    {
        $employeeId = $request->input('employee_id') ?? $user->id;
        return Employee::findOrFail($employeeId);
    }

    /**
     * Resolve start and end dates of the selected month.
     */
    private function resolveMonthRange(Request $request): array
    {
        $month = $request->input('month') ?? now()->format('Y-m');
        $start = Carbon::parse("{$month}-01")->startOfMonth();
        return [$start, $start->copy()->endOfMonth()];
    }

    /**
     * Fetch employee’s DTR entries for the given month.
     */
    private function fetchDtrEntries(int $employeeId, $startOfMonth, $endOfMonth)
    {
        return DtrEntry::where('employee_id', $employeeId)
            ->whereBetween('dtr_date', [$startOfMonth, $endOfMonth])
            ->orderBy('dtr_date')
            ->get();
    }




    public function show($month)
    {
        // Show specific month DTR
    }
}