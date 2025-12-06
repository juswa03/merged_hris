<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Employee::with(['department', 'position', 'employmentType', 'jobStatus'])
            ->orderBy('last_name', 'asc');

        if (!empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }

        if (!empty($this->filters['employment_type_id'])) {
            $query->where('employment_type_id', $this->filters['employment_type_id']);
        }

        if (!empty($this->filters['job_status_id'])) {
            $query->where('job_status_id', $this->filters['job_status_id']);
        }

        return $query->get()->map(function ($employee) {
            return [
                'Employee ID' => $employee->id,
                'First Name' => $employee->first_name,
                'Last Name' => $employee->last_name,
                'Middle Name' => $employee->middle_name,
                'Email' => $employee->user?->email ?? 'N/A',
                'Contact Number' => $employee->contact_number,
                'Gender' => $employee->gender,
                'Civil Status' => $employee->civil_status,
                'Birthdate' => $employee->birthdate?->format('Y-m-d'),
                'Department' => $employee->department?->name ?? 'N/A',
                'Position' => $employee->position?->name ?? 'N/A',
                'Employment Type' => $employee->employmentType?->name ?? 'N/A',
                'Job Status' => $employee->jobStatus?->name ?? 'N/A',
                'Hire Date' => $employee->hire_date?->format('Y-m-d'),
                'Date Resigned' => $employee->date_resign?->format('Y-m-d'),
                'Basic Salary' => number_format($employee->basic_salary, 2),
                'Salary Grade' => $employee->salary_grade ? "SG-{$employee->salary_grade} Step {$employee->salary_step}" : 'N/A',
                'Address' => $employee->address,
                'RFID Code' => $employee->rfid_code,
                'Created At' => $employee->created_at?->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'First Name',
            'Last Name',
            'Middle Name',
            'Email',
            'Contact Number',
            'Gender',
            'Civil Status',
            'Birthdate',
            'Department',
            'Position',
            'Employment Type',
            'Job Status',
            'Hire Date',
            'Date Resigned',
            'Basic Salary',
            'Salary Grade',
            'Address',
            'RFID Code',
            'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']],
            ],
        ];
    }
}
