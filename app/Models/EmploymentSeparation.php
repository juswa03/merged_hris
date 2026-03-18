<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentSeparation extends Model
{
    use HasFactory;

    protected $table = 'employment_separations';

    protected $fillable = [
        'personal_information_id',
        'has_been_separated',
        'details_of_separation',
    ];

    // Relationships
    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
