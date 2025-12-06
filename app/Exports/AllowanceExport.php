<?php

namespace App\Exports;

use App\Models\Allowance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllowanceExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Allowance::all()->map(function ($allowance) {
            return [
                'Allowance ID' => $allowance->id,
                'Allowance Name' => $allowance->name,
                'Code' => $allowance->code ?? 'N/A',
                'Type' => $allowance->type ?? 'Fixed',
                'Amount/Percentage' => number_format($allowance->amount, 2),
                'Taxable' => $allowance->is_taxable ? 'Yes' : 'No',
                'Description' => $allowance->description,
                'Status' => $allowance->status ?? 'Active',
                'Created At' => $allowance->created_at?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Allowance ID', 'Allowance Name', 'Code', 'Type', 'Amount/Percentage', 'Taxable', 'Description', 'Status', 'Created At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
