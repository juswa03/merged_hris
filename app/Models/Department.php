<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, Auditable;

    protected $table = 'tbl_departments';

    protected $fillable = [
        'name',
        'description',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // Accessor for backward compatibility
    public function getDepartmentNameAttribute()
    {
        return $this->name;
    }
}