<?php

namespace App\Exports;

use App\Models\Holiday;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HolidayExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Holiday::orderBy('date', 'asc');

        if (!empty($this->filters['year'])) {
            $query->whereYear('date', $this->filters['year']);
        }

        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        return $query->get()->map(function ($holiday) {
            return [
                'Holiday ID' => $holiday->id,
                'Holiday Name' => $holiday->name,
                'Date' => $holiday->date?->format('Y-m-d'),
                'Day of Week' => $holiday->date?->format('l'),
                'Type' => $holiday->type ?? 'National',
                'Is Paid' => $holiday->is_paid ? 'Yes' : 'No',
                'Description' => $holiday->description,
                'Created At' => $holiday->created_at?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Holiday ID', 'Holiday Name', 'Date', 'Day of Week', 'Type', 'Is Paid', 'Description', 'Created At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
