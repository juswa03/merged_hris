<?php

namespace App\Exports;

use App\Models\DtrEntry;
use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DtrExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $selectedDate;
    protected $employeeId;
    protected $departmentId;

    public function __construct($selectedDate, $employeeId = null, $departmentId = null)
    {
        $this->selectedDate = $selectedDate;
        $this->employeeId = $employeeId;
        $this->departmentId = $departmentId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = DtrEntry::with(['employee.department', 'employee.position'])
            ->whereYear('dtr_date', $this->selectedDate->year)
            ->whereMonth('dtr_date', $this->selectedDate->month);

        // Apply filters
        if ($this->employeeId) {
            $query->where('employee_id', $this->employeeId);
        }

        if ($this->departmentId) {
            $query->whereHas('employee', function($q) {
                $q->where('department_id', $this->departmentId);
            });
        }

        return $query->orderBy('employee_id')->orderBy('dtr_date')->get();
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'Department',
            'Position',
            'Date',
            'Day',
            'AM Arrival',
            'AM Departure',
            'PM Arrival',
            'PM Departure',
            'Total Hours',
            'Total Minutes',
            'Undertime (Minutes)',
            'Overtime Hours',
            'Overtime Minutes',
            'Is Weekend',
            'Is Holiday',
            'Remarks',
        ];
    }

    /**
     * Map data for each row
     */
    public function map($dtrEntry): array
    {
        $totalHoursFormatted = sprintf('%dh %dm', $dtrEntry->total_hours, $dtrEntry->total_minutes);

        return [
            $dtrEntry->employee->id,
            $dtrEntry->employee->full_name,
            $dtrEntry->employee->department->name ?? 'N/A',
            $dtrEntry->employee->position->title ?? 'N/A',
            Carbon::parse($dtrEntry->dtr_date)->format('Y-m-d'),
            Carbon::parse($dtrEntry->dtr_date)->format('l'),
            $dtrEntry->am_arrival ?? '',
            $dtrEntry->am_departure ?? '',
            $dtrEntry->pm_arrival ?? '',
            $dtrEntry->pm_departure ?? '',
            $dtrEntry->total_hours,
            $dtrEntry->total_minutes,
            $dtrEntry->under_time_minutes,
            $dtrEntry->overtime_hours ?? 0,
            $dtrEntry->overtime_minutes ?? 0,
            $dtrEntry->is_weekend ? 'Yes' : 'No',
            $dtrEntry->is_holiday ? 'Yes' : 'No',
            $dtrEntry->remarks ?? '',
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    /**
     * Define worksheet title
     */
    public function title(): string
    {
        return 'DTR_' . $this->selectedDate->format('Y_m');
    }
}
