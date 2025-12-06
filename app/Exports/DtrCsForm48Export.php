<?php

namespace App\Exports;

use App\Models\DtrEntry;
use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class DtrCsForm48Export implements FromView, WithEvents, ShouldAutoSize
{
    protected $employee;
    protected $month;
    protected $year;
    protected $dtrEntries;

    public function __construct($employeeId, $month, $year)
    {
        $this->employee = Employee::with(['department', 'position'])->findOrFail($employeeId);
        $this->month = $month;
        $this->year = $year;

        // Get all DTR entries for the month
        $this->dtrEntries = DtrEntry::where('employee_id', $employeeId)
            ->whereYear('dtr_date', $year)
            ->whereMonth('dtr_date', $month)
            ->orderBy('dtr_date')
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->dtr_date)->day;
            });
    }

    public function view(): View
    {
        $selectedDate = Carbon::createFromDate($this->year, $this->month, 1);
        $period = CarbonPeriod::create($selectedDate->startOfMonth(), $selectedDate->copy()->endOfMonth());

        $daysInMonth = [];
        foreach ($period as $date) {
            $day = $date->day;
            $dtr = $this->dtrEntries->get($day);

            $daysInMonth[] = [
                'day' => $day,
                'date' => $date,
                'am_arrival' => $dtr->am_arrival ?? '',
                'am_departure' => $dtr->am_departure ?? '',
                'pm_arrival' => $dtr->pm_arrival ?? '',
                'pm_departure' => $dtr->pm_departure ?? '',
                'undertime_hours' => $dtr ? $this->formatUndertime($dtr->under_time_minutes) : '',
                'remarks' => $dtr->remarks ?? '',
            ];
        }

        // Calculate totals
        $totalUndertimeMinutes = $this->dtrEntries->sum('under_time_minutes');
        $totalUndertimeHours = floor($totalUndertimeMinutes / 60);
        $totalUndertimeRemainingMinutes = $totalUndertimeMinutes % 60;

        return view('exports.dtr_cs_form_48', [
            'employee' => $this->employee,
            'monthYear' => $selectedDate->format('F Y'),
            'daysInMonth' => $daysInMonth,
            'totalUndertime' => sprintf('%d hrs %d mins', $totalUndertimeHours, $totalUndertimeRemainingMinutes),
        ]);
    }

    private function formatUndertime($minutes)
    {
        if (!$minutes || $minutes == 0) {
            return '';
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return sprintf('%dh %dm', $hours, $mins);
        } elseif ($hours > 0) {
            return sprintf('%dh', $hours);
        } else {
            return sprintf('%dm', $mins);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get the highest row and column
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                $highestColumn = $event->sheet->getDelegate()->getHighestColumn();

                // Apply borders to all cells
                $event->sheet->getDelegate()->getStyle('A1:' . $highestColumn . $highestRow)
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Bold and center the header row
                $event->sheet->getDelegate()->getStyle('A1:' . $highestColumn . '8')
                    ->getFont()->setBold(true);

                // Center align specific columns
                $event->sheet->getDelegate()->getStyle('A9:G' . $highestRow)
                    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Set column widths
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(8);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(20);
            },
        ];
    }
}
