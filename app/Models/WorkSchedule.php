<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $table = 'tbl_work_schedules';

    protected $fillable = [
        'name',
        'type',
        'work_start',
        'work_end',
        'break_minutes',
        'working_days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'working_days' => 'array',
        'is_active'    => 'boolean',
    ];

    const DAYS = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function getWorkingDaysLabelAttribute(): string
    {
        $days = $this->working_days ?? [];
        $labels = array_map(fn($d) => self::DAYS[$d] ?? $d, $days);
        return implode(', ', $labels);
    }

    public function getHoursPerDayAttribute(): float
    {
        $start = \Carbon\Carbon::createFromFormat('H:i:s', $this->work_start);
        $end   = \Carbon\Carbon::createFromFormat('H:i:s', $this->work_end);
        $totalMinutes = $start->diffInMinutes($end) - $this->break_minutes;
        return round($totalMinutes / 60, 2);
    }
}
