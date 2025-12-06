<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
use HasFactory;

    protected $table = 'tbl_allowances';

    protected $fillable = [
        'name',
        'amount',
        'type',
        'time_stamp_id'
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'tbl_employee_allowances', 'allowance_id', 'employee_id')
                    ->withPivot('effective_from', 'effective_to')
                    ->withTimestamps();
    }
}
