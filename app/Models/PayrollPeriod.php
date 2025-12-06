<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PayrollPeriod extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_payroll_periods';

    protected $fillable = [
        'start_date',
        'end_date',
        'cut_off_type_id',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pay_date' => 'date',
    ];

    /**
     * Relationship with payrolls
     */
    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'payroll_period_id');
    }

    /**
     * Relationship with approvals
     */
    public function approvals()
    {
        return $this->hasMany(PayrollApproval::class, 'payroll_period_id');
    }

    /**
     * Get formatted period name
     */
    public function getFormattedPeriodAttribute()
    {
        $start = Carbon::parse($this->start_date)->format('M d, Y');
        $end = Carbon::parse($this->end_date)->format('M d, Y');
        return "{$start} - {$end}";
    }

    /**
     * Get period for display (e.g., "January 2024")
     */
    public function getPeriodDisplayAttribute()
    {
        return Carbon::parse($this->start_date)->format('F Y');
    }

    /**
     * Check if period is current
     */
    public function getIsCurrentAttribute()
    {
        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date);
    }

    /**
     * Scope for active periods
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for current period
     */
    public function scopeCurrent($query)
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }
}