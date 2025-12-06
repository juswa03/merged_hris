<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'old_salary',
        'new_salary',
        'old_salary_grade',
        'old_salary_step',
        'new_salary_grade',
        'new_salary_step',
        'change_type',
        'change_reason',
        'changed_by_user_id',
        'effective_date',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'old_salary' => 'decimal:2',
        'new_salary' => 'decimal:2',
    ];

    /**
     * Get the employee that owns the salary history
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the user who made the change
     */
    public function changedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'changed_by_user_id');
    }

    /**
     * Get the salary difference
     */
    public function getSalaryDifferenceAttribute()
    {
        return $this->new_salary - ($this->old_salary ?? 0);
    }

    /**
     * Get the percentage change
     */
    public function getPercentageChangeAttribute()
    {
        if (!$this->old_salary || $this->old_salary == 0) {
            return null;
        }

        return (($this->new_salary - $this->old_salary) / $this->old_salary) * 100;
    }

    /**
     * Get formatted change type
     */
    public function getFormattedChangeTypeAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->change_type));
    }

    /**
     * Scope: Filter by employee
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('effective_date', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter by change type
     */
    public function scopeByChangeType($query, $changeType)
    {
        return $query->where('change_type', $changeType);
    }

    /**
     * Create a salary history record
     */
    public static function logChange($employee, $newSalary, $changeType, $reason, $effectiveDate, $newGrade = null, $newStep = null)
    {
        return self::create([
            'employee_id' => $employee->id,
            'old_salary' => $employee->basic_salary,
            'new_salary' => $newSalary,
            'old_salary_grade' => $employee->salary_grade,
            'old_salary_step' => $employee->salary_step,
            'new_salary_grade' => $newGrade ?? $employee->salary_grade,
            'new_salary_step' => $newStep ?? $employee->salary_step,
            'change_type' => $changeType,
            'change_reason' => $reason,
            'changed_by_user_id' => auth()->id(),
            'effective_date' => $effectiveDate,
        ]);
    }
}
