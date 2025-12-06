<?php

namespace App\Exports;

use App\Models\SalaryGrade;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalaryGradeExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return SalaryGrade::all()->map(function ($grade) {
            return [
                'Grade ID' => $grade->id,
                'Grade Number' => $grade->grade_number,
                'Step' => $grade->step ?? 1,
                'Salary Amount' => number_format($grade->salary_amount, 2),
                'Effective From' => $grade->effective_from?->format('Y-m-d'),
                'Effective To' => $grade->effective_to?->format('Y-m-d') ?? 'Active',
                'Description' => $grade->description,
                'Status' => $grade->status ?? 'Active',
                'Created At' => $grade->created_at?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Grade ID', 'Grade Number', 'Step', 'Salary Amount', 'Effective From', 'Effective To', 'Description', 'Status', 'Created At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
