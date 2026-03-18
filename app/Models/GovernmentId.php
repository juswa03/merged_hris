<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernmentId extends Model
{
    use HasFactory;

    protected $table = 'government_ids';

    protected $fillable = [
        'personal_information_id',
        'gsis_id',
        'pagibig_id',
        'philhealth_id',
        'sss_id',
        'tin_id',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
