<?php

namespace App\Exports;

use App\Models\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RoleExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Role::with('users')->orderBy('name')->get()->map(function ($role) {
            return [
                'Role ID' => $role->id,
                'Role Name' => $role->name,
                'Description' => $role->description,
                'User Count' => $role->users->count(),
                'Active Users' => $role->users->where('status', 'active')->count(),
                'Created At' => $role->created_at?->format('Y-m-d'),
                'Updated At' => $role->updated_at?->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Role ID', 'Role Name', 'Description', 'User Count', 'Active Users', 'Created At', 'Updated At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
