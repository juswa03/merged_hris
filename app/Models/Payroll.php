<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TrackPayrollChanges;

class Payroll extends Model
{
    use HasFactory, TrackPayrollChanges;

    protected $table = 'tbl_payrolls';

    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'basic_salary',
        'hourly_rate',
        'overtime_pay',
        'regular_overtime_hours',
        'regular_overtime_pay',
        'restday_overtime_hours',
        'restday_overtime_pay',
        'holiday_overtime_hours',
        'holiday_overtime_pay',
        'total_allowances',
        'total_deductions',
        'late_deductions',
        'absent_deductions',
        'undertime_deductions',
        'gsis_contribution',
        'philhealth_contribution',
        'pagibig_contribution',
        'withholding_tax',
        'other_deductions',
        'holiday_pay',
        'night_differential',
        'bonuses',
        'gross_pay',
        'net_pay',
        'status',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'regular_overtime_hours' => 'decimal:2',
        'regular_overtime_pay' => 'decimal:2',
        'restday_overtime_hours' => 'decimal:2',
        'restday_overtime_pay' => 'decimal:2',
        'holiday_overtime_hours' => 'decimal:2',
        'holiday_overtime_pay' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'late_deductions' => 'decimal:2',
        'absent_deductions' => 'decimal:2',
        'undertime_deductions' => 'decimal:2',
        'gsis_contribution' => 'decimal:2',
        'philhealth_contribution' => 'decimal:2',
        'pagibig_contribution' => 'decimal:2',
        'withholding_tax' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'holiday_pay' => 'decimal:2',
        'night_differential' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function auditTrail()
    {
        return $this->hasMany(PayrollAudit::class)->latest();
    }

    public function approval()
    {
        return $this->payrollPeriod->approvals()->first();
    }
}