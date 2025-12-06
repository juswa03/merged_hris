<?php

namespace App\Exports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartmentExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Department::with('employees')->orderBy('name')->get()->map(function ($dept) {
            return [
                'Department ID' => $dept->id,
                'Department Name' => $dept->name,
                'Description' => $dept->description,
                'Employee Count' => $dept->employees->count(),
                'Active Employees' => $dept->employees->filter(fn($e) => $e->jobStatus?->name === 'Active')->count(),
                'Budget Code' => $dept->budget_code ?? 'N/A',
                'Created At' => $dept->created_at?->format('Y-m-d'),
                'Updated At' => $dept->updated_at?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Department ID', 'Department Name', 'Description', 'Employee Count', 'Active Employees', 'Budget Code', 'Created At', 'Updated At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
