<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceGoal extends Model
{
    use HasFactory;

    protected $table = 'tbl_performance_goals';

    protected $fillable = [
        'employee_id',
        'set_by',
        'title',
        'description',
        'category',
        'priority',
        'target_date',
        'status',
        'progress_percentage',
        'completion_date',
        'notes',
    ];

    protected $casts = [
        'target_date' => 'date',
        'completion_date' => 'date',
        'progress_percentage' => 'integer',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function setter()
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['not_started', 'in_progress']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('target_date', '<', now())
                     ->whereNotIn('status', ['completed', 'cancelled']);
    }

    // Helper methods
    public function isOverdue()
    {
        return $this->target_date < now() && !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'green',
            'in_progress' => 'blue',
            'not_started' => 'gray',
            'cancelled' => 'red',
            default => 'gray'
        };
    }
}
