<?php

namespace App\Exports;

use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Deduction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class GeneralPayrollSheetExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $periodId;
    protected $period;
    protected $deductionTypes;

    public function __construct($periodId)
    {
        $this->periodId = $periodId;
        $this->period = PayrollPeriod::find($periodId);
        // Fetch all distinct deduction names to create dynamic columns
        $this->deductionTypes = Deduction::whereHas('deductionType', function($q) {
            $q->where('name', '!=', 'Government Mandated');
        })->pluck('name')->toArray();
    }

    public function collection()
    {
        return Payroll::with(['employee.department', 'employee.position', 'employee.deductions'])
            ->where('payroll_period_id', $this->periodId)
            ->join('tbl_employee', 'tbl_payrolls.employee_id', '=', 'tbl_employee.id')
            ->join('tbl_users', 'tbl_employee.user_id', '=', 'tbl_users.id')
            ->join('tbl_roles', 'tbl_users.role_id', '=', 'tbl_roles.id')
            ->where('tbl_roles.name', 'Employee')
            ->orderBy('tbl_employee.last_name')
            ->orderBy('tbl_employee.first_name')
            ->select('tbl_payrolls.*') // Select payroll columns to avoid ambiguity
            ->get();
    }

    public function headings(): array
    {
        $headers = [
            'No.',
            'Name',
            'Position',
            'Basic Monthly Salary',
            'PERA', // Assuming PERA is a standard allowance we want to show
            'Gross Computation',
            // Government Deductions
            'W/Tax',
            'GSIS',
            'Pag-IBIG',
            'PhilHealth',
        ];

        // Add dynamic deduction headers
        foreach ($this->deductionTypes as $deduction) {
            $headers[] = $deduction;
        }

        // Add final columns
        $headers[] = 'Total Deductions';
        $headers[] = 'Amount Received';
        $headers[] = 'Signature';
        $headers[] = 'Remarks';

        return $headers;
    }

    public function map($payroll): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $employee = $payroll->employee;
        
        // Calculate PERA (Personal Economic Relief Allowance)
        // Assuming it's an allowance named 'PERA' or similar, or we default to 2000 if not found but standard
        // For now, let's check if there is an allowance named 'PERA'
        $pera = $employee->allowances->where('name', 'PERA')->first();
        $peraAmount = $pera ? $pera->amount : 0; 
        // If PERA is not found, maybe check 'Personal Economic Relief Allowance'
        if ($peraAmount == 0) {
             $pera = $employee->allowances->where('name', 'Personal Economic Relief Allowance')->first();
             $peraAmount = $pera ? $pera->amount : 0;
        }

        // Basic mapping
        $data = [
            $rowNumber,
            $employee->last_name . ', ' . $employee->first_name,
            $employee->position->name ?? '',
            number_format($payroll->basic_salary, 2),
            number_format($peraAmount, 2),
            number_format($payroll->gross_pay, 2),
            // Government Deductions
            number_format($payroll->withholding_tax, 2),
            number_format($payroll->gsis_contribution, 2),
            number_format($payroll->pagibig_contribution, 2),
            number_format($payroll->philhealth_contribution, 2),
        ];

        // Map dynamic deductions
        // We need to find the amount for each deduction type for this employee
        // Since we don't have a payroll_items table, we look at the employee's active deductions
        // This is an approximation.
        foreach ($this->deductionTypes as $deductionName) {
            $deduction = $employee->deductions->where('name', $deductionName)->first();
            $amount = $deduction ? $deduction->amount : 0;
            // If the deduction has a custom amount in pivot, use that
            if ($deduction && $deduction->pivot->custom_amount) {
                $amount = $deduction->pivot->custom_amount;
            }
            
            $data[] = $amount > 0 ? number_format($amount, 2) : '';
        }

        // Final columns
        $data[] = number_format($payroll->total_deductions, 2);
        $data[] = number_format($payroll->net_pay, 2);
        $data[] = ''; // Signature
        $data[] = ''; // Remarks

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $lastColumn = $sheet->getHighestColumn();
                
                // Insert 4 rows at the top for the header
                $sheet->insertNewRowBefore(1, 4);

                // Merge cells for the headers
                $sheet->mergeCells('A1:' . $lastColumn . '1');
                $sheet->mergeCells('A2:' . $lastColumn . '2');
                $sheet->mergeCells('A3:' . $lastColumn . '3');

                // Set Header Text
                $sheet->setCellValue('A1', 'Biliran Province State University');
                $sheet->setCellValue('A2', 'Naval Biliran');
                
                $month = $this->period ? $this->period->start_date->format('F Y') : 'Unknown Month';
                $sheet->setCellValue('A3', "We Acknowledged receipt of the sum shown opposite our names as full compensation for services rendered for the month of " . strtoupper($month));

                // Style the headers
                $sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                
                $sheet->getStyle('A1')->getFont()->setSize(14);

                // Style the column headers (now at row 5)
                $sheet->getStyle('A5:' . $lastColumn . '5')->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                // Auto-size columns
                foreach (range('A', $lastColumn) as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }
}
