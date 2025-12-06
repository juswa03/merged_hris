<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SalaryGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade',
        'step',
        'amount',
        'effective_date',
        'tranche',
        'remarks',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'effective_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get salary amount for a specific grade and step
     * Uses the most recent effective schedule
     */
    public static function getSalary($grade, $step, $asOfDate = null)
    {
        $date = $asOfDate ?? now();

        return self::where('grade', $grade)
            ->where('step', $step)
            ->where('effective_date', '<=', $date)
            ->where('is_active', true)
            ->orderBy('effective_date', 'desc')
            ->first()?->amount;
    }

    /**
     * Get all grades for a specific effective date
     */
    public static function getSchedule($effectiveDate = null)
    {
        $date = $effectiveDate ?? now();

        return self::where('effective_date', '<=', $date)
            ->where('is_active', true)
            ->orderBy('effective_date', 'desc')
            ->get()
            ->groupBy('grade')
            ->map(function($gradeGroup) {
                return $gradeGroup->keyBy('step');
            });
    }

    /**
     * Get all unique effective dates (salary schedules)
     */
    public static function getEffectiveDates()
    {
        return self::select('effective_date', 'tranche')
            ->distinct()
            ->orderBy('effective_date', 'desc')
            ->get();
    }

    /**
     * Get the currently active salary schedule date
     */
    public static function getCurrentEffectiveDate()
    {
        return self::where('effective_date', '<=', now())
            ->where('is_active', true)
            ->orderBy('effective_date', 'desc')
            ->first()?->effective_date;
    }

    /**
     * Scope: Filter by effective date
     */
    public function scopeByEffectiveDate($query, $date)
    {
        return $query->where('effective_date', $date);
    }

    /**
     * Scope: Only active schedules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Current schedule (effective as of today)
     */
    public function scopeCurrent($query)
    {
        $currentDate = self::getCurrentEffectiveDate();
        return $query->where('effective_date', $currentDate);
    }

    /**
     * Format amount with peso sign
     */
    public function getFormattedAmountAttribute()
    {
        return '₱' . number_format($this->amount, 2);
    }

    /**
     * Get display name (e.g., "SG-15 Step 3")
     */
    public function getDisplayNameAttribute()
    {
        return "SG-{$this->grade} Step {$this->step}";
    }
}
