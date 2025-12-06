<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DtrEntry extends Model
{
    use HasFactory;
    protected $table = 'tbl_dtr_entries';
     protected $fillable = [
        'employee_id',
        'dtr_date',
        'am_arrival',
        'am_departure',
        'pm_arrival',
        'pm_departure',
        'total_hours',
        'total_minutes',
        'under_time_minutes',
        'overtime_minutes',
        'remarks',
        'is_holiday',
        'is_weekend',
        'status',
    ];
    protected $casts = [
    'dtr_date' => 'date',
    'total_hours' => 'float',
    'total_minutes' => 'integer',
    'under_time_minutes' => 'integer',
    'overtime_minutes' => 'integer',
    'is_holiday' => 'boolean',
    'is_weekend' => 'boolean',
];


    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
