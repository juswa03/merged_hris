<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricLog extends Model
{
    use HasFactory;

    protected $table = 'tbl_biometric_logs';

    protected $fillable = [
        'employee_id',
        'device_id',
        'type',
        'status',
        'timestamp',
    ];
    protected $casts = [
    'timestamp' => 'datetime',
];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
