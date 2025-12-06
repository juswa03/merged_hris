<?php

namespace App\Exports;

use App\Models\PerformanceGoal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerformanceGoalExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PerformanceGoal::with('employee')->orderBy('created_at', 'desc');

        if (!empty($this->filters['employee_id'])) {
            $query->where('employee_id', $this->filters['employee_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->get()->map(function ($goal) {
            return [
                'Goal ID' => $goal->id,
                'Employee' => $goal->employee?->full_name ?? 'N/A',
                'Goal Title' => $goal->title,
                'Description' => $goal->description,
                'Target Value' => $goal->target_value ?? 'N/A',
                'Current Progress' => number_format($goal->current_progress ?? 0, 2),
                'Progress %' => $goal->target_value ? round(($goal->current_progress / $goal->target_value) * 100, 2) . '%' : 'N/A',
                'Start Date' => $goal->start_date?->format('Y-m-d'),
                'End Date' => $goal->end_date?->format('Y-m-d'),
                'Status' => $goal->status ?? 'Pending',
                'Created At' => $goal->created_at?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Goal ID', 'Employee', 'Goal Title', 'Description', 'Target Value', 'Current Progress', 'Progress %', 'Start Date', 'End Date', 'Status', 'Created At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
