<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\DtrEntry;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Exports\DtrCsForm48Export;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DtrController extends Controller
{
    //

      /**
     * Display a listing of the resource.
     */
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

        // Calculate totals
        $totalUndertime = $dtrEntries->sum('undertime_minutes');
        $totalMinutes = $dtrEntries->sum(function($entry) {
            return ($entry->total_hours * 60) + $entry->total_minutes;
        });
        
        $totalOvertimeHours = $dtrEntries->sum('overtime_hours');
        $totalOvertimeMinutes = $dtrEntries->sum('overtime_minutes');

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
        $period = CarbonPeriod::create($startDate, $endDate);

        // Create a quick lookup map of DTR entries by date
        $dtrMap = $dtrEntries->keyBy(function ($entry) {
            return Carbon::parse($entry->dtr_date)->format('Y-m-d');
        });

        // Loop through every day in the month
        foreach ($period as $date) {
            // We only care about workdays (not weekends or holidays)
            // TODO: Add holiday check from a Holiday model/service
            if ($date->isWeekend()) {
                continue;
            }

            $workDays++;
            $dateString = $date->format('Y-m-d');
            $entry = $dtrMap->get($dateString);

            if (!$entry || ($entry->total_hours == 0 && $entry->total_minutes == 0)) {
                // If no entry exists for a workday, it's an absence
                $absentDays++;
            } else {
                // If an entry exists, sum up the times
                $totalHours += $entry->total_hours;
                $totalMinutes += $entry->total_minutes;
                $totalUndertime += $entry->under_time_minutes;
            }
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

    /**
     * Admin DTR Overview - View all employees' DTR
     */
    public function adminIndex(Request $request)
    {
        // Get filter parameters
        $month = $request->get('month', now()->format('Y-m'));
        $employeeId = $request->get('employee_id');
        $departmentId = $request->get('department_id');
        $search = $request->get('search');

        // Parse the selected month
        $selectedDate = Carbon::parse($month);

        // Build query for DTR entries
        $query = DtrEntry::with(['employee.department', 'employee.position'])
            ->whereYear('dtr_date', $selectedDate->year)
            ->whereMonth('dtr_date', $selectedDate->month);

        // Apply filters
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($departmentId) {
            $query->whereHas('employee', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($search) {
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Get DTR entries grouped by employee
        $dtrEntries = $query->orderBy('dtr_date')->get();

        // Group by employee
        $employeeDtrData = $dtrEntries->groupBy('employee_id')->map(function($entries, $empId) use ($selectedDate) {
            $employee = $entries->first()->employee;
            $summary = $this->calculateSummary($entries, $selectedDate);

            return [
                'employee' => $employee,
                'entries' => $entries,
                'summary' => $summary,
            ];
        });

        // Get all employees and departments for filters
        $employees = Employee::orderBy('last_name')->get();
        $departments = Department::orderBy('name')->get();

        // Calculate overall statistics
        $totalEmployees = $employeeDtrData->count();
        $totalWorkDays = $employeeDtrData->sum(fn($data) => $data['summary']['total_work_days']);
        $totalAbsences = $employeeDtrData->sum(fn($data) => $data['summary']['absent_days']);
        $totalUndertime = $employeeDtrData->sum(fn($data) => $data['summary']['total_undertime']);

        return view('admin.dtr.index', compact(
            'employeeDtrData',
            'employees',
            'departments',
            'month',
            'selectedDate',
            'totalEmployees',
            'totalWorkDays',
            'totalAbsences',
            'totalUndertime'
        ));
    }

    /**
     * Show edit form for DTR entry
     */
    public function edit($id)
    {
        $dtrEntry = DtrEntry::with('employee')->findOrFail($id);
        return view('admin.dtr.edit', compact('dtrEntry'));
    }

    /**
     * Update DTR entry
     */
    public function update(Request $request, $id)
    {
        // Normalize time inputs to H:i format
        $timeFields = ['am_arrival', 'am_departure', 'pm_arrival', 'pm_departure'];
        $data = $request->all();

        foreach ($timeFields as $field) {
            if (!empty($data[$field])) {
                try {
                    // Try to parse as 12-hour format with AM/PM
                    $data[$field] = Carbon::parse($data[$field])->format('H:i');
                } catch (\Exception $e) {
                    // Keep original if parse fails (let validation handle it)
                }
            }
        }
        
        $request->merge($data);

        $validated = $request->validate([
            'am_arrival' => 'nullable|date_format:H:i',
            'am_departure' => 'nullable|date_format:H:i',
            'pm_arrival' => 'nullable|date_format:H:i',
            'pm_departure' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:500',
        ]);

        $dtrEntry = DtrEntry::findOrFail($id);

        // Update the entry
        $dtrEntry->update($validated);

        // Recalculate totals
        $this->recalculateDtrTotals($dtrEntry);

        return redirect()->back()->with('success', 'DTR entry updated successfully.');
    }

    /**
     * Export DTR to Excel (CSV format for now)
     */
    public function export(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $employeeId = $request->get('employee_id');
        $departmentId = $request->get('department_id');

        $selectedDate = Carbon::parse($month);

        $query = DtrEntry::with(['employee.department', 'employee.position'])
            ->whereYear('dtr_date', $selectedDate->year)
            ->whereMonth('dtr_date', $selectedDate->month);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $dtrEntries = $query->orderBy('employee_id')->orderBy('dtr_date')->get();

        // Group entries by employee for a cleaner PDF layout
        $grouped = $dtrEntries->groupBy('employee_id');

        $pdf = Pdf::loadView('admin.dtr.export_pdf', [
            'grouped'      => $grouped,
            'selectedDate' => $selectedDate,
            'monthYear'    => $selectedDate->format('F Y'),
        ]);
        $pdf->setPaper('A4', 'landscape');

        $filename = 'DTR_' . $selectedDate->format('Y_m') . '_' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Recalculate DTR totals after manual edit
     */
    private function recalculateDtrTotals(DtrEntry $dtrEntry)
    {
        $amArrival = $dtrEntry->am_arrival ? Carbon::parse($dtrEntry->am_arrival) : null;
        $amDeparture = $dtrEntry->am_departure ? Carbon::parse($dtrEntry->am_departure) : null;
        $pmArrival = $dtrEntry->pm_arrival ? Carbon::parse($dtrEntry->pm_arrival) : null;
        $pmDeparture = $dtrEntry->pm_departure ? Carbon::parse($dtrEntry->pm_departure) : null;

        $totalMinutes = 0;

        // Calculate AM hours
        if ($amArrival && $amDeparture) {
            $totalMinutes += $amArrival->diffInMinutes($amDeparture);
        }

        // Calculate PM hours
        if ($pmArrival && $pmDeparture) {
            $totalMinutes += $pmArrival->diffInMinutes($pmDeparture);
        }

        $totalHours = floor($totalMinutes / 60);
        $remainingMinutes = $totalMinutes % 60;

        // Calculate undertime (8 hours expected = 480 minutes)
        $expectedMinutes = 480;
        $undertimeMinutes = max(0, $expectedMinutes - $totalMinutes);

        $dtrEntry->update([
            'total_hours' => $totalHours,
            'total_minutes' => $remainingMinutes,
            'under_time_minutes' => $undertimeMinutes,
        ]);
    }

    /**
     * Show DTR details for a specific employee
     */
    public function show(Request $request, $employeeId)
    {
        $employee = Employee::with(['department', 'position'])->findOrFail($employeeId);
        $month = $request->get('month', now()->format('Y-m'));

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
        $totalMinutes = $dtrEntries->sum(function($entry) {
            return ($entry->total_hours * 60) + $entry->total_minutes;
        });

        $totalOvertimeHours = $dtrEntries->sum('overtime_hours');
        $totalOvertimeMinutes = $dtrEntries->sum('overtime_minutes');

        // Calculate summary statistics
        $summary = $this->calculateSummary($dtrEntries, $selectedDate);

        return view('admin.dtr.show', compact(
            'employee',
            'dtrEntries',
            'monthYear',
            'totalUndertime',
            'totalMinutes',
            'totalOvertimeHours',
            'totalOvertimeMinutes',
            'summary',
            'selectedDate'
        ));
    }

    /**
     * Export DTR using Civil Service Form No. 48 (PDF)
     */
    public function exportCsForm48(Request $request, $employeeId)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $employee = Employee::with(['department', 'position'])->findOrFail($employeeId);
        $selectedDate = Carbon::createFromDate($year, $month, 1);
        $period = CarbonPeriod::create($selectedDate->startOfMonth(), $selectedDate->copy()->endOfMonth());

        // Get DTR entries
        $dtrEntries = DtrEntry::where('employee_id', $employeeId)
            ->whereYear('dtr_date', $year)
            ->whereMonth('dtr_date', $month)
            ->orderBy('dtr_date')
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->dtr_date)->day;
            });

        // Build days array
        $daysInMonth = [];
        foreach ($period as $date) {
            $day = $date->day;
            $dtr = $dtrEntries->get($day);

            $daysInMonth[] = [
                'day' => $day,
                'date' => $date,
                'am_arrival' => $dtr->am_arrival ?? '',
                'am_departure' => $dtr->am_departure ?? '',
                'pm_arrival' => $dtr->pm_arrival ?? '',
                'pm_departure' => $dtr->pm_departure ?? '',
                'undertime_hrs'  => $dtr ? (int) floor(($dtr->under_time_minutes ?? 0) / 60) : null,
                'undertime_mins' => $dtr ? (int) (($dtr->under_time_minutes ?? 0) % 60) : null,
                'remarks' => $dtr->remarks ?? '',
            ];
        }

        // Calculate totals
        $totalUndertimeMinutes = $dtrEntries->sum('under_time_minutes');
        $totalUndertimeHours = floor($totalUndertimeMinutes / 60);
        $totalUndertimeRemainingMinutes = $totalUndertimeMinutes % 60;

        $data = [
            'employee' => $employee,
            'monthYear' => $selectedDate->format('F Y'),
            'daysInMonth' => $daysInMonth,
            'totalUndertimeHours' => $totalUndertimeHours,
            'totalUndertimeMins'  => $totalUndertimeRemainingMinutes,
            'isPdf' => true,
        ];

        $pdf = Pdf::loadView('admin.dtr.preview_cs_form_48', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'DTR_CS_Form_48_' . str_replace(' ', '_', $employee->full_name) . '_' . $selectedDate->format('F_Y') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export current employee's DTR using CS Form 48 (for employee portal)
     */
    public function exportMyCsForm48(Request $request)
    {
        $employee = Auth::user()->employee;
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $monthName = Carbon::create($year, $month, 1)->format('F_Y');

        $filename = 'My_DTR_CS_Form_48_' . $monthName . '.xlsx';

        return Excel::download(
            new DtrCsForm48Export($employee->id, $month, $year),
            $filename
        );
    }

    /**
     * Preview DTR CS Form 48 (For testing - shows HTML version)
     */
    public function previewCsForm48(Request $request, $employeeId)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $employee = Employee::with(['department', 'position'])->findOrFail($employeeId);
        $selectedDate = Carbon::createFromDate($year, $month, 1);
        $period = CarbonPeriod::create($selectedDate->startOfMonth(), $selectedDate->copy()->endOfMonth());

        // Get DTR entries
        $dtrEntries = DtrEntry::where('employee_id', $employeeId)
            ->whereYear('dtr_date', $year)
            ->whereMonth('dtr_date', $month)
            ->orderBy('dtr_date')
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->dtr_date)->day;
            });

        // Build days array
        $daysInMonth = [];
        foreach ($period as $date) {
            $day = $date->day;
            $dtr = $dtrEntries->get($day);

            $daysInMonth[] = [
                'day' => $day,
                'date' => $date,
                'am_arrival' => $dtr->am_arrival ?? '',
                'am_departure' => $dtr->am_departure ?? '',
                'pm_arrival' => $dtr->pm_arrival ?? '',
                'pm_departure' => $dtr->pm_departure ?? '',
                'undertime_hrs'  => $dtr ? (int) floor(($dtr->under_time_minutes ?? 0) / 60) : null,
                'undertime_mins' => $dtr ? (int) (($dtr->under_time_minutes ?? 0) % 60) : null,
                'remarks' => $dtr->remarks ?? '',
            ];
        }

        // Calculate totals
        $totalUndertimeMinutes = $dtrEntries->sum('under_time_minutes');
        $totalUndertimeHours = floor($totalUndertimeMinutes / 60);
        $totalUndertimeRemainingMinutes = $totalUndertimeMinutes % 60;

        return view('admin.dtr.preview_cs_form_48', [
            'employee' => $employee,
            'monthYear' => $selectedDate->format('F Y'),
            'daysInMonth' => $daysInMonth,
            'totalUndertimeHours' => $totalUndertimeHours,
            'totalUndertimeMins'  => $totalUndertimeRemainingMinutes,
            'isPdf' => false,
        ]);
    }

    private function formatUndertimePreview($minutes)
    {
        if (!$minutes || $minutes == 0) {
            return '';
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return sprintf('%dh %dm', $hours, $mins);
        } elseif ($hours > 0) {
            return sprintf('%dh', $hours);
        } else {
            return sprintf('%dm', $mins);
        }
    }
}
