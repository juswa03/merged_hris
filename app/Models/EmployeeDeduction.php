<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeDeduction extends Pivot
{
use HasFactory;

    protected $table = 'tbl_employee_deductions';

    protected $fillable = [
        'employee_id',
        'deduction_id',
        'custom_amount',
        'effective_from',
        'effective_to'
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'custom_amount' => 'decimal:2'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function deduction()
    {
        return $this->belongsTo(Deduction::class);
    }
}
