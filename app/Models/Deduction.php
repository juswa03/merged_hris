<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;

    protected $table = 'tbl_deductions';

    protected $fillable = [
        'name',
        'amount',
        'deduction_type_id',
        'time_stamp_id'
    ];

    public function deductionType()
    {
        return $this->belongsTo(DeductionType::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'tbl_employee_deductions', 'deduction_id', 'employee_id')
                    ->withPivot('custom_amount', 'effective_from', 'effective_to')
                    ->withTimestamps();
    }
}
