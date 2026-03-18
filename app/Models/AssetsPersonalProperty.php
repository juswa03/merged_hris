<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetsPersonalProperty extends Model
{
    use HasFactory;

    protected $table = 'assets_personal_properties';

    protected $fillable = [
        'personal_information_id',
        'description',
        'year_acquired',
        'acquisition_cost',
        'reporting_year',
    ];

    /**
     * Auto-fill reporting_year with current year if not provided
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reporting_year)) {
                $model->reporting_year = now()->year;
            }
        });
    }

    /**
     * Relationship with PersonalInformation
     */
    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
