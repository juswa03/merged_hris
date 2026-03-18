<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialStatus extends Model
{
    use HasFactory;

    protected $table = 'special_statuses';

    protected $fillable = [
        'personal_information_id',
        'is_indigenous_member',
        'indigenous_group_name',
        'is_person_with_disability',
        'pwd_id_number',
        'is_solo_parent',
        'solo_parent_id_number',
    ];

    // Relationship to Personal Information
    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
