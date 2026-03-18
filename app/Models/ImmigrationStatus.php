<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImmigrationStatus extends Model
{
    use HasFactory;

    protected $table = 'immigration_statuses';

    protected $fillable = [
        'personal_information_id',
        'has_immigrant_status',
        'country_id',
    ];

    // Relationships
    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
