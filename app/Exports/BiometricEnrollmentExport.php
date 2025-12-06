<?php

namespace App\Exports;

use App\Models\FingerprintTemplate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BiometricEnrollmentExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = FingerprintTemplate::with(['employee'])->orderBy('created_at', 'desc');

        if (!empty($this->filters['department_id'])) {
            $query->whereHas('employee', function ($q) {
                $q->where('department_id', $this->filters['department_id']);
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('enrollment_status', $this->filters['status']);
        }

        return $query->get()->map(function ($enrollment) {
            return [
                'Employee ID' => $enrollment->employee_id,
                'Employee Name' => $enrollment->employee?->full_name ?? 'N/A',
                'Department' => $enrollment->employee?->department?->name ?? 'N/A',
                'Position' => $enrollment->employee?->position?->name ?? 'N/A',
                'Template ID' => $enrollment->id,
                'Finger Index' => $enrollment->finger_index ?? 'N/A',
                'Enrollment Status' => $enrollment->enrollment_status ?? 'Completed',
                'Quality Score' => number_format($enrollment->quality_score ?? 0, 2),
                'Enrolled At' => $enrollment->created_at?->format('Y-m-d H:i:s'),
                'Last Updated' => $enrollment->updated_at?->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Employee ID', 'Employee Name', 'Department', 'Position', 'Template ID', 'Finger Index', 'Enrollment Status', 'Quality Score', 'Enrolled At', 'Last Updated'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
