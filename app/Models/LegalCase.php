<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalCase extends Model
{
    use HasFactory;

    protected $table = 'legal_cases';

    protected $fillable = [
        'personal_information_id',
        'has_admin_offense',
        'offense_details',
        'has_criminal_case',
        'date_filed',
        'status_of_case',
        'has_been_convicted',
        'conviction_details',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class, 'personal_information_id');
    }
}
