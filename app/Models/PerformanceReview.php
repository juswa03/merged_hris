<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $table = 'tbl_performance_reviews';

    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_period_start',
        'review_period_end',
        'review_type',
        'status',
        'overall_rating',
        'strengths',
        'areas_for_improvement',
        'recommendations',
        'employee_comments',
        'reviewer_comments',
        'hr_comments',
        'completed_at',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'review_period_start' => 'date',
        'review_period_end' => 'date',
        'overall_rating' => 'decimal:2',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function ratings()
    {
        return $this->hasMany(PerformanceRating::class, 'performance_review_id');
    }

    // Helper methods
    public function calculateOverallRating()
    {
        if ($this->ratings->isEmpty()) {
            return 0;
        }

        $totalWeight = 0;
        $weightedSum = 0;

        foreach ($this->ratings as $rating) {
            $weight = $rating->criteria->weight ?? 20;
            $totalWeight += $weight;
            $weightedSum += ($rating->rating * $weight);
        }

        return $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : 0;
    }

    public function getRatingLabelAttribute()
    {
        $rating = $this->overall_rating;

        if ($rating >= 4.5) return 'Outstanding';
        if ($rating >= 3.5) return 'Exceeds Expectations';
        if ($rating >= 2.5) return 'Meets Expectations';
        if ($rating >= 1.5) return 'Needs Improvement';
        return 'Unsatisfactory';
    }
}
