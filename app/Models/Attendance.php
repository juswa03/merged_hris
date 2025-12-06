<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'tbl_attendance';

    protected $fillable = [
        'employee_id',
        'remarks',
        'attendance_source_id',
        'attendance_type_id',
        'device_uid',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


    public function attendanceSource()
    {
        return $this->belongsTo(AttendanceSource::class);
    }

    public function attendanceType()
    {
        return $this->belongsTo(AttendanceType::class);
    }
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_uid', 'device_uid');
    }
}