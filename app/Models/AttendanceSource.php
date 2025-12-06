<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSource extends Model
{
    use HasFactory;
    protected $table = 'tbl_attendance_sources';

    protected $fillable = [
        'name',
        'description',
    ];

}
