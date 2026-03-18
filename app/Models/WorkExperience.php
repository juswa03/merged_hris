<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $table = 'work_experiences';

    protected $fillable = [
        'personal_information_id',
        'position_title',
        'department_agency',
        'monthly_salary',
        'salary_grade_step',
        'status_appointment',
        'gov_service',
        'inclusive_date_from_work',
        'inclusive_date_to_work',
    ];

    /**
     * Relationship to Personal Information
     */
    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
