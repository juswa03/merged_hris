<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'personal_information_id',
        'spouse_id',
        'name',
        'address',
    ];

    // Relations
    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }

    public function spouse()
    {
        return $this->belongsTo(Spouse::class);
    }
}
