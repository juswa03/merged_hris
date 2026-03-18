<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoliticalActivity extends Model
{
    use HasFactory;

    protected $table = 'political_activities';

    protected $fillable = [
        'personal_information_id',
        'has_been_candidate',
        'election_details',
        'has_resigned_for_campaigning',
        'campaign_details',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class, 'personal_information_id');
    }
}
