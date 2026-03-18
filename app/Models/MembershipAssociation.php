<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipAssociation extends Model
{
    use HasFactory;

    protected $fillable = [
        'personal_information_id',
        'organization_name',
        'period_from',
        'period_to',
        'number_of_hours',
        'position',
    ];

    // Relationship: A membership belongs to a personal information
    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
