<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'personal_information_id',
        'first_name',
        'last_name',
        'middle_name',
        'name_extension',
        'occupation',
        'position',
        'employer_business_name',
        'business_address',
        'telephone_no',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }

        public function agencies()
    {
        return $this->hasOne(Agency::class);
    }
}

