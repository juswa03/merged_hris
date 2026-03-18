<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetsRealProperty extends Model
{
    use HasFactory;

    protected $table = 'assets_real_properties';

    protected $fillable = [
        'personal_information_id',
        'description',
        'kind',
        'location',
        'assessed_value',
        'current_fair_market_value',
        'acquisition_year',
        'acquisition_mode',
        'acquisition_cost',
        'subtotal',
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
