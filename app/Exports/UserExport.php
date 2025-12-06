<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::with(['role', 'employee'])->orderBy('name', 'asc');

        if (!empty($this->filters['role_id'])) {
            $query->where('role_id', $this->filters['role_id']);
        }

        return $query->get()->map(function ($user) {
            return [
                'User ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Role' => $user->role?->name ?? 'No Role',
                'Employee' => $user->employee?->full_name ?? 'N/A',
                'Department' => $user->employee?->department?->name ?? 'N/A',
                'Email Verified' => $user->email_verified_at ? 'Yes' : 'No',
                'Last Login' => $user->last_login_at?->format('Y-m-d H:i:s') ?? 'Never',
                'Status' => $user->status ?? 'Active',
                'Created At' => $user->created_at?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['User ID', 'Name', 'Email', 'Role', 'Employee', 'Department', 'Email Verified', 'Last Login', 'Status', 'Created At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
