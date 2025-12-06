<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\PayrollAudit;
use App\Models\PayrollApproval;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\DB;

class PayrollAuditService
{
    /**
     * Log payroll approval
     */
    public function logApproval(Payroll $payroll, $status, $notes = null)
    {
        PayrollAudit::logAction(
            $payroll->id,
            $status === 'approved' ? 'approved' : 'rejected',
            [],
            $notes
        );
    }

    /**
     * Log payroll processing
     */
    public function logProcessing(Payroll $payroll, $reason = null)
    {
        PayrollAudit::logAction(
            $payroll->id,
            'processed',
            [],
            $reason ?? 'Payroll marked as processed'
        );
    }

    /**
     * Log payroll as paid
     */
    public function logPaid(Payroll $payroll, $reason = null)
    {
        PayrollAudit::logAction(
            $payroll->id,
            'paid',
            [],
            $reason ?? 'Payroll marked as paid'
        );
    }

    /**
     * Log payroll regeneration
     */
    public function logRegeneration(Payroll $payroll, $oldValues, $newValues, $reason = null)
    {
        $changes = [];
        foreach ($oldValues as $key => $oldValue) {
            if (isset($newValues[$key]) && $newValues[$key] !== $oldValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValues[$key],
                ];
            }
        }

        PayrollAudit::logAction(
            $payroll->id,
            'regenerated',
            $changes,
            $reason ?? 'Payroll recalculated'
        );
    }

    /**
     * Get full audit history for a payroll
     */
    public function getAuditHistory(Payroll $payroll)
    {
        return PayrollAudit::where('payroll_id', $payroll->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get audit summary for a period
     */
    public function getAuditSummary(PayrollPeriod $period)
    {
        return PayrollAudit::whereIn('payroll_id', $period->payrolls()->pluck('id'))
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->get();
    }

    /**
     * Export audit report
     */
    public function exportAuditReport(PayrollPeriod $period, $format = 'csv')
    {
        $audits = PayrollAudit::whereIn('payroll_id', $period->payrolls()->pluck('id'))
            ->with(['payroll.employee', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'csv') {
            return $this->generateCsvReport($audits);
        }

        return $audits;
    }

    /**
     * Generate CSV audit report
     */
    private function generateCsvReport($audits)
    {
        $csv = "Date,Time,User,Employee,Action,Changes,Reason,IP Address\n";

        foreach ($audits as $audit) {
            $date = $audit->created_at->format('Y-m-d');
            $time = $audit->created_at->format('H:i:s');
            $user = $audit->user?->name ?? 'System';
            $employee = $audit->payroll?->employee?->full_name ?? 'N/A';
            $action = ucfirst($audit->action);
            $changes = $audit->changes ? json_encode($audit->changes) : '';
            $reason = $audit->reason ?? '';
            $ip = $audit->ip_address ?? '';

            $csv .= "\"{$date}\",\"{$time}\",\"{$user}\",\"{$employee}\",\"{$action}\",\"{$changes}\",\"{$reason}\",\"{$ip}\"\n";
        }

        return $csv;
    }

    /**
     * Track user modifications over time
     */
    public function getUserActivityReport($userId, $period = 30)
    {
        $since = now()->subDays($period);

        return PayrollAudit::where('user_id', $userId)
            ->where('created_at', '>=', $since)
            ->with(['payroll.employee', 'payroll.payrollPeriod'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Detect suspicious activities
     */
    public function detectSuspiciousActivities()
    {
        return PayrollAudit::whereIn('action', ['updated', 'deleted'])
            ->where('created_at', '>=', now()->subHours(24))
            ->selectRaw('payroll_id, COUNT(*) as modification_count')
            ->groupBy('payroll_id')
            ->having('modification_count', '>', 3)
            ->with(['payroll.employee', 'user'])
            ->get();
    }
}
