<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CivilServiceEligibility extends Model
{
    use HasFactory;

    protected $table = 'civil_service_eligibilities';

    protected $fillable = [
        'personal_information_id',
        'eligibility_type',
        'rating',
        'exam_date',
        'exam_place',
        'license_number',
        'license_validity',
    ];

    /**
     * Relationship with PersonalInformation
     */
    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
