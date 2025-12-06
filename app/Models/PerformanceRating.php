<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceRating extends Model
{
    use HasFactory;

    protected $table = 'tbl_performance_ratings';

    protected $fillable = [
        'performance_review_id',
        'performance_criteria_id',
        'rating',
        'comments',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // Relationships
    public function review()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id');
    }

    public function criteria()
    {
        return $this->belongsTo(PerformanceCriteria::class, 'performance_criteria_id');
    }
}
