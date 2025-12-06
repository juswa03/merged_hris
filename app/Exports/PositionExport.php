<?php

namespace App\Exports;

use App\Models\Position;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PositionExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Position::with('department', 'employees')->orderBy('name')->get()->map(function ($position) {
            return [
                'Position ID' => $position->id,
                'Position Name' => $position->name,
                'Department' => $position->department?->name ?? 'N/A',
                'Job Level' => $position->job_level ?? 'N/A',
                'Employee Count' => $position->employees->count(),
                'Salary Grade' => $position->salary_grade ?? 'N/A',
                'Reports To' => $position->reports_to ?? 'N/A',
                'Description' => $position->description,
                'Status' => $position->status ?? 'Active',
                'Created At' => $position->created_at?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Position ID', 'Position Name', 'Department', 'Job Level', 'Employee Count', 'Salary Grade', 'Reports To', 'Description', 'Status', 'Created At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
