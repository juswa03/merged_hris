<?php

namespace App\Exports;

use App\Models\Deduction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeductionExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Deduction::all()->map(function ($deduction) {
            return [
                'Deduction ID' => $deduction->id,
                'Deduction Name' => $deduction->name,
                'Code' => $deduction->code ?? 'N/A',
                'Type' => $deduction->type ?? 'Fixed',
                'Amount/Percentage' => number_format($deduction->amount, 2),
                'Description' => $deduction->description,
                'Status' => $deduction->status ?? 'Active',
                'Created At' => $deduction->created_at?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Deduction ID', 'Deduction Name', 'Code', 'Type', 'Amount/Percentage', 'Description', 'Status', 'Created At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
