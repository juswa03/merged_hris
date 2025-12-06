<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeAllowance extends Pivot
{
use HasFactory;

    protected $table = 'tbl_employee_allowances';

    protected $fillable = [
        'employee_id',
        'allowance_id',
        'effective_from',
        'effective_to'
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function allowance()
    {
        return $this->belongsTo(Allowance::class);
    }
}
