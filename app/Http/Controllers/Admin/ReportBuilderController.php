<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmploymentType;
use App\Models\JobStatus;
use App\Models\LeaveBalance;
use App\Models\Position;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class ReportBuilderController extends Controller
{
    private array $reportTypes = [
        'employees'    => 'Employee Directory',
        'leave'        => 'Leave Balances',
        'salary'       => 'Salary / Compensation',
    ];

    private array $columns = [
        'employees' => [
            'full_name'           => 'Full Name',
            'department'          => 'Department',
            'position'            => 'Position',
            'employment_type'     => 'Employment Type',
            'job_status'          => 'Job Status',
            'basic_salary'        => 'Basic Salary',
            'hire_date'           => 'Hire Date',
            'gender'              => 'Gender',
            'civil_status'        => 'Civil Status',
            'contact_number'      => 'Contact Number',
        ],
        'leave' => [
            'full_name'       => 'Full Name',
            'department'      => 'Department',
            'leave_type'      => 'Leave Type',
            'opening_balance' => 'Opening Balance',
            'earned'          => 'Earned',
            'used'            => 'Used',
            'closing_balance' => 'Closing Balance',
            'year'            => 'Year',
        ],
        'salary' => [
            'full_name'       => 'Full Name',
            'department'      => 'Department',
            'position'        => 'Position',
            'salary_grade'    => 'Salary Grade',
            'salary_step'     => 'Step',
            'basic_salary'    => 'Basic Salary',
            'employment_type' => 'Employment Type',
            'hire_date'       => 'Hire Date',
        ],
    ];

    public function index()
    {
        $departments     = Department::orderBy('name')->pluck('name', 'id');
        $positions       = Position::orderBy('name')->pluck('name', 'id');
        $employmentTypes = EmploymentType::orderBy('name')->pluck('name', 'id');
        $jobStatuses     = JobStatus::orderBy('name')->pluck('name', 'id');
        $reportTypes     = $this->reportTypes;
        $allColumns      = $this->columns;

        return view('admin.report-builder.index', compact(
            'departments', 'positions', 'employmentTypes',
            'jobStatuses', 'reportTypes', 'allColumns'
        ));
    }

    public function generate(Request $request)
    {
        $data    = $this->buildData($request);
        $columns = $request->input('columns', []);
        $labels  = $this->columns[$request->report_type] ?? [];

        return response()->json([
            'rows'    => $data->take(50)->values(),
            'columns' => array_intersect_key($labels, array_flip($columns)),
            'total'   => $data->count(),
        ]);
    }

    public function export(Request $request)
    {
        $data    = $this->buildData($request);
        $columns = $request->input('columns', array_keys($this->columns[$request->report_type] ?? []));
        $labels  = $this->columns[$request->report_type] ?? [];
        $headers = array_values(array_intersect_key($labels, array_flip($columns)));

        $rows = $data->map(fn ($row) => array_values(array_intersect_key($row, array_flip($columns))));

        $filename = 'report_' . $request->report_type . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new class($headers, $rows) implements \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithStyles {
            public function __construct(private array $headings, private Collection $rows) {}
            public function collection(): Collection { return $this->rows; }
            public function headings(): array { return $this->headings; }
            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array {
                return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '366092']]]];
            }
        }, $filename);
    }

    private function buildData(Request $request): Collection
    {
        return match($request->report_type ?? 'employees') {
            'employees' => $this->employeeData($request),
            'leave'     => $this->leaveData($request),
            'salary'    => $this->salaryData($request),
            default     => collect(),
        };
    }

    private function employeeData(Request $request): Collection
    {
        $q = Employee::with(['department', 'position', 'employmentType', 'jobStatus']);

        if ($request->filled('department_id')) $q->where('department_id', $request->department_id);
        if ($request->filled('job_status_id'))  $q->where('job_status_id', $request->job_status_id);
        if ($request->filled('employment_type_id')) $q->where('employment_type_id', $request->employment_type_id);
        if ($request->filled('hire_date_from')) $q->whereDate('hire_date', '>=', $request->hire_date_from);
        if ($request->filled('hire_date_to'))   $q->whereDate('hire_date', '<=', $request->hire_date_to);

        return $q->orderBy('last_name')->get()->map(fn ($e) => [
            'full_name'       => $e->full_name,
            'department'      => $e->department?->name ?? '—',
            'position'        => $e->position?->name ?? '—',
            'employment_type' => $e->employmentType?->name ?? '—',
            'job_status'      => $e->jobStatus?->name ?? '—',
            'basic_salary'    => number_format($e->basic_salary, 2),
            'hire_date'       => $e->hire_date?->format('Y-m-d') ?? '—',
            'gender'          => $e->gender ?? '—',
            'civil_status'    => $e->civil_status ?? '—',
            'contact_number'  => $e->contact_number ?? '—',
        ]);
    }

    private function leaveData(Request $request): Collection
    {
        $year = $request->input('year', now()->year);
        $q = LeaveBalance::with(['employee.department'])->where('year', $year);

        if ($request->filled('department_id')) {
            $q->whereHas('employee', fn ($eq) => $eq->where('department_id', $request->department_id));
        }
        if ($request->filled('leave_type')) {
            $q->where('leave_type', $request->leave_type);
        }

        return $q->get()->map(fn ($lb) => [
            'full_name'       => $lb->employee?->full_name ?? '—',
            'department'      => $lb->employee?->department?->name ?? '—',
            'leave_type'      => \App\Models\LeaveBalance::LEAVE_TYPES[$lb->leave_type] ?? $lb->leave_type,
            'opening_balance' => number_format($lb->opening_balance, 3),
            'earned'          => number_format($lb->earned, 3),
            'used'            => number_format($lb->used, 3),
            'closing_balance' => number_format($lb->closing_balance, 3),
            'year'            => $lb->year,
        ]);
    }

    private function salaryData(Request $request): Collection
    {
        $q = Employee::with(['department', 'position', 'employmentType']);

        if ($request->filled('department_id'))      $q->where('department_id', $request->department_id);
        if ($request->filled('employment_type_id')) $q->where('employment_type_id', $request->employment_type_id);

        return $q->orderBy('last_name')->get()->map(fn ($e) => [
            'full_name'       => $e->full_name,
            'department'      => $e->department?->name ?? '—',
            'position'        => $e->position?->name ?? '—',
            'salary_grade'    => $e->salary_grade ?? '—',
            'salary_step'     => $e->salary_step ?? '—',
            'basic_salary'    => number_format($e->basic_salary, 2),
            'employment_type' => $e->employmentType?->name ?? '—',
            'hire_date'       => $e->hire_date?->format('Y-m-d') ?? '—',
        ]);
    }
}
