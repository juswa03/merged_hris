<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $table = 'tbl_positions';

    protected $fillable = [
        'name',
        'title',
        'description',
        'requirements',
        'level',
        'min_salary',
        'max_salary',
        'salary_grade',
        'is_active',
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // Accessor for backward compatibility
    public function getTitleAttribute($value)
    {
        return $value ?? $this->name;
    }
}