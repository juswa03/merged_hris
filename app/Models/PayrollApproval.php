<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollApproval extends Model
{
    use HasFactory;

    protected $table = 'tbl_payroll_approvals';

    protected $fillable = [
        'payroll_period_id',
        'approver_id',
        'status',
        'approved_at',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public static function requiresApproval($payrollPeriodId)
    {
        return !self::where('payroll_period_id', $payrollPeriodId)
            ->where('status', 'approved')
            ->exists();
    }
}
