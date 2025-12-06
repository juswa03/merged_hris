<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $table = 'tbl_holidays';

    protected $fillable = [
        'date',
        'name',
        'type', // 'regular', 'special', etc.
        'is_paid',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'is_paid' => 'boolean',
    ];
}
