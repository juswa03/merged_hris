<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liability extends Model
{
    use HasFactory;

    protected $table = 'liabilities';

    protected $fillable = [
        'personal_information_id',
        'nature_type',
        'name_of_creditors',
        'outstanding_balance',
        'reporting_year',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reporting_year)) {
                $model->reporting_year = now()->year; // ✅ Auto-fill current year
            }
        });
    }

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class, 'personal_information_id');
    }
}
