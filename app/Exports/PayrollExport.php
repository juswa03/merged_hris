<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PayrollExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Return the collection of payrolls to export
     */
    public function collection()
    {
        $query = Payroll::with(['employee.department', 'employee.position', 'payrollPeriod'])
            ->join('tbl_employee', 'tbl_payrolls.employee_id', '=', 'tbl_employee.id')
            ->select('tbl_payrolls.*');

        // Apply filters
        if (!empty($this->filters['period_id'])) {
            $query->where('tbl_payrolls.payroll_period_id', $this->filters['period_id']);
        }

        if (!empty($this->filters['employee_id'])) {
            $query->where('tbl_payrolls.employee_id', $this->filters['employee_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('tbl_payrolls.status', $this->filters['status']);
        }

        if (!empty($this->filters['department_id'])) {
            $query->where('tbl_employee.department_id', $this->filters['department_id']);
        }

        return $query->orderBy('tbl_employee.first_name')
                     ->orderBy('tbl_employee.last_name')
                     ->get();
    }

    /**
     * Define the headings for the Excel sheet
     */
    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'Department',
            'Position',
            'Payroll Period',
            'Period Start',
            'Period End',
            'Basic Salary',
            'Overtime Pay',
            'Allowances',
            'Gross Pay',
            'GSIS',
            'PhilHealth',
            'Pag-IBIG',
            'Withholding Tax',
            'Other Deductions',
            'Total Deductions',
            'Net Pay',
            'Status',
            'Created Date',
        ];
    }

    /**
     * Map each payroll record to a row in the Excel sheet
     */
    public function map($payroll): array
    {
        return [
            $payroll->employee->id ?? 'N/A',
            $payroll->employee->first_name . ' ' . $payroll->employee->last_name,
            $payroll->employee->department->name ?? 'N/A',
            $payroll->employee->position->name ?? 'N/A',
            $payroll->payrollPeriod->formatted_period ?? 'N/A',
            $payroll->payrollPeriod ? $payroll->payrollPeriod->start_date->format('Y-m-d') : 'N/A',
            $payroll->payrollPeriod ? $payroll->payrollPeriod->end_date->format('Y-m-d') : 'N/A',
            number_format($payroll->basic_salary, 2, '.', ''),
            number_format($payroll->overtime_pay ?? 0, 2, '.', ''),
            number_format($payroll->total_allowances ?? 0, 2, '.', ''),
            number_format($payroll->gross_pay ?? ($payroll->basic_salary + ($payroll->overtime_pay ?? 0) + ($payroll->total_allowances ?? 0)), 2, '.', ''),
            number_format($payroll->gsis_contribution ?? 0, 2, '.', ''),
            number_format($payroll->philhealth_contribution ?? 0, 2, '.', ''),
            number_format($payroll->pagibig_contribution ?? 0, 2, '.', ''),
            number_format($payroll->withholding_tax ?? 0, 2, '.', ''),
            number_format($payroll->other_deductions ?? 0, 2, '.', ''),
            number_format($payroll->total_deductions, 2, '.', ''),
            number_format($payroll->net_pay, 2, '.', ''),
            ucfirst($payroll->status ?? 'draft'),
            $payroll->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Apply styles to the Excel sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'], // Blue-600
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // Employee ID
            'B' => 25, // Employee Name
            'C' => 20, // Department
            'D' => 20, // Position
            'E' => 20, // Payroll Period
            'F' => 15, // Period Start
            'G' => 15, // Period End
            'H' => 15, // Basic Salary
            'I' => 15, // Overtime Pay
            'J' => 15, // Allowances
            'K' => 15, // Gross Pay
            'L' => 12, // SSS
            'M' => 12, // PhilHealth
            'N' => 12, // Pag-IBIG
            'O' => 15, // Withholding Tax
            'P' => 15, // Other Deductions
            'Q' => 15, // Total Deductions
            'R' => 15, // Net Pay
            'S' => 12, // Status
            'T' => 20, // Created Date
        ];
    }
}
