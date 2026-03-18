<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessInterest extends Model
{
    use HasFactory;

    protected $table = 'business_interests';

    protected $fillable = [
        'personal_information_id',
        'name_of_business',
        'business_address',
        'name_of_business_interest',
        'date_of_acquisition',
        'no_business_interest',
        'reporting_year',
    ];

    /**
     * Relationship: A business interest belongs to a person.
     */
    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
