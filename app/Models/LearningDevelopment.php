<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningDevelopment extends Model
{
    use HasFactory;

    protected $table = 'learning_developments';

    protected $fillable = [
        'personal_information_id',
        'training_title',
        'inclusive_date_from',
        'inclusive_date_to',
        'number_of_hours',
        'type_of_ld',
        'conducted_by',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
