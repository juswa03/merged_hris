<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelativeInGovService extends Model
{
    use HasFactory;

    protected $table = 'relatives_in_gov_service';

    protected $fillable = [
        'personal_information_id',
        'name_of_relative',
        'relationship',
        'position_of_relative',
        'name_of_agency',
        'no_relative_in_gov_service',
        'reporting_year',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
