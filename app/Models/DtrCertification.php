<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DtrCertification extends Model
{
    use HasFactory;
    protected $table = 'dtr_certifications';
      protected $fillable = [
        'employee_id',
        'monthly_year',
        'regular_days',
        'saturdays',
        'certified_by_name',
        'certified_by_position',
        'certified_at',
        'acknowledged_by_name',
        'acknowledged_by_position',
        'acknowledged_at',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
