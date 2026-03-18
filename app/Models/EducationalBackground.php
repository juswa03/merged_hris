<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalBackground extends Model
{
    use HasFactory;

    protected $table = 'educational_backgrounds';

    protected $fillable = [
        'personal_information_id',
        'level',
        'school_name',
        'degree_course',
        'period_from',
        'period_to',
        'highest_level_unit_earned',
        'year_graduated',
        'scholarship_honors',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class, 'personal_information_id');
    }
}
