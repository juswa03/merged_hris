<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalCosts extends Model
{
    use HasFactory;

    // Table name (optional if it follows pluralization)
    protected $table = 'total_costs';

    // Fillable fields
    protected $fillable = [
        'personal_information_id',
        'real_properties_total',
        'personal_property_total',
        'total_assets_costs',
        'total_liabilities',
        'net_worth',
    ];

    /**
     * Relationship: A TotalCost belongs to a PersonalInformation
     */
    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
