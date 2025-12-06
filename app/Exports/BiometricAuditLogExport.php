<?php

namespace App\Exports;

use App\Models\BiometricAuditLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BiometricAuditLogExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = BiometricAuditLog::with(['user', 'employee'])->orderBy('created_at', 'desc');

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (!empty($this->filters['action_type'])) {
            $query->where('action_type', $this->filters['action_type']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->get()->map(function ($log) {
            return [
                'Log ID' => $log->id,
                'User' => $log->user?->name ?? 'System',
                'Employee' => $log->employee?->full_name ?? $log->employee_id,
                'Action Type' => $log->action_type,
                'Description' => $log->description,
                'IP Address' => $log->ip_address ?? 'N/A',
                'User Agent' => $log->user_agent ?? 'N/A',
                'Status' => $log->status ?? 'Success',
                'Timestamp' => $log->created_at?->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Log ID', 'User', 'Employee', 'Action Type', 'Description', 'IP Address', 'User Agent', 'Status', 'Timestamp'];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']]]];
    }
}
