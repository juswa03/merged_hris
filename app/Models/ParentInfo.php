<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentInfo extends Model
{
    use HasFactory;

    protected $table = 'parent_infos';

    protected $fillable = [
        'personal_information_id',
        'father_first_name',
        'father_middle_name',
        'father_last_name',
        'father_extension_name',
        'mother_first_name',
        'mother_middle_name',
        'mother_last_name',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
