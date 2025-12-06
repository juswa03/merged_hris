<?php

namespace App\Exports;

use App\Models\SalaryHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalaryHistoryExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = SalaryHistory::with('employee')->orderBy('created_at', 'desc');

        if (!empty($this->filters['employee_id'])) {
            $query->where('employee_id', $this->filters['employee_id']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->get()->map(function ($history) {
            return [
                'Employee ID' => $history->employee_id,
                'Employee Name' => $history->employee?->full_name ?? 'N/A',
                'Previous Salary' => number_format($history->previous_salary, 2),
                'New Salary' => number_format($history->new_salary, 2),
                'Difference' => number_format($history->new_salary - $history->previous_salary, 2),
                'Reason' => $history->reason ?? 'N/A',
                'Effective From' => $history->effective_from?->format('Y-m-d'),
                'Changed By' => $history->changed_by ?? 'System',
                'Changed At' => $history->created_at?->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Employee ID', 'Employee Name', 'Previous Salary', 'New Salary', 'Difference', 'Reason', 'Effective From', 'Changed By', 'Changed At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
