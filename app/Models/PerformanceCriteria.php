<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceCriteria extends Model
{
    use HasFactory;

    protected $table = 'tbl_performance_criteria';

    protected $fillable = [
        'name',
        'description',
        'category',
        'weight',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weight' => 'integer',
    ];

    // Relationships
    public function ratings()
    {
        return $this->hasMany(PerformanceRating::class, 'performance_criteria_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
