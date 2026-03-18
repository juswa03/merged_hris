<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialSkillsHobby extends Model
{
    use HasFactory;

    protected $table = 'special_skills_hobbies';

    protected $fillable = [
        'personal_information_id',
        'special_skills',
        'non_academic_distinctions',
        'membership_in_organization',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class, 'personal_information_id');
    }
}
