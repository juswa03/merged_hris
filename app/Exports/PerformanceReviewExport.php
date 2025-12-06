<?php

namespace App\Exports;

use App\Models\PerformanceReview;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerformanceReviewExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PerformanceReview::with(['employee', 'reviewer'])->orderBy('created_at', 'desc');

        if (!empty($this->filters['employee_id'])) {
            $query->where('employee_id', $this->filters['employee_id']);
        }

        if (!empty($this->filters['year'])) {
            $query->whereYear('review_date', $this->filters['year']);
        }

        if (!empty($this->filters['period'])) {
            $query->where('period', $this->filters['period']);
        }

        return $query->get()->map(function ($review) {
            return [
                'Review ID' => $review->id,
                'Employee' => $review->employee?->full_name ?? 'N/A',
                'Reviewer' => $review->reviewer?->name ?? 'N/A',
                'Review Period' => $review->period ?? 'N/A',
                'Review Date' => $review->review_date?->format('Y-m-d'),
                'Overall Rating' => $review->overall_rating ?? 'N/A',
                'Performance Score' => number_format($review->performance_score ?? 0, 2),
                'Status' => $review->status ?? 'Draft',
                'Comments' => $review->comments,
                'Created At' => $review->created_at?->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Review ID', 'Employee', 'Reviewer', 'Review Period', 'Review Date', 'Overall Rating', 'Performance Score', 'Status', 'Comments', 'Created At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
