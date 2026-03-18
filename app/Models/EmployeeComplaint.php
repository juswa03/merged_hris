<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'complaint_number',
        'type',
        'subject',
        'description',
        'incident_details',
        'incident_date',
        'location',
        'involved_parties',
        'evidence_files',
        'priority',
        'status',
        'hr_remarks',
        'assigned_hr_id',
        'submitted_at',
        'resolved_at',
        'is_confidential',
        'is_anonymous'
    ];

    protected $casts = [
        'incident_date' => 'date',
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime',
        'involved_parties' => 'array',
        'evidence_files' => 'array',
        'is_confidential' => 'boolean',
        'is_anonymous' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedHR()
    {
        return $this->belongsTo(User::class, 'assigned_hr_id');
    }

    public function updates()
    {
        return $this->hasMany(ComplaintUpdate::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['submitted', 'under_review', 'investigation_started']);
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeConfidential($query)
    {
        return $query->where('is_confidential', true);
    }

    // Methods
    public function generateComplaintNumber()
    {
        $year = now()->format('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        $this->complaint_number = "COMP-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
        $this->save();
    }

    public function getTypeLabel()
    {
        return match($this->type) {
            'harassment' => 'Harassment',
            'discrimination' => 'Discrimination',
            'workplace_bullying' => 'Workplace Bullying',
            'safety_concern' => 'Safety Concern',
            'ethical_concern' => 'Ethical Concern',
            'work_environment' => 'Work Environment',
            'management_issue' => 'Management Issue',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'submitted' => 'bg-blue-100 text-blue-800',
            'under_review' => 'bg-yellow-100 text-yellow-800',
            'investigation_started' => 'bg-orange-100 text-orange-800',
            'resolved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPriorityBadgeClass()
    {
        return match($this->priority) {
            'low' => 'bg-gray-100 text-gray-800',
            'medium' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function canBeEdited()
    {
        return $this->status === 'submitted' && $this->user_id === auth()->id();
    }

    public function addUpdate($data)
    {
        return $this->updates()->create([
            'updated_by' => auth()->id(),
            ...$data
        ]);
    }
}