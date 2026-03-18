<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelAuthority extends Model
{
    use HasFactory;

    protected $table = 'travel_authorities';

    protected $fillable = [
        'employee_id',
        'travel_authority_no',
        'destination',
        'purpose',
        'transportation',
        'estimated_expenses',
        'source_of_funds',
        'travel_type',
        'duration_type',
        'start_date',
        'end_date',
        'other_funds_specification',
        'status',
        'remarks',
        'submitted_at',
        'completed_at',
        'submitted_by',
        'rejection_reason',
        'recommending_official_id',
        'signature_path'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Travel Type Constants
    const TYPE_OFFICIAL_TIME = 'official_time';
    const TYPE_OFFICIAL_BUSINESS = 'official_business';
    const TYPE_PERSONAL_ABROAD = 'personal_abroad';
    const TYPE_OFFICIAL_TRAVEL = 'official_travel';

    // Duration Type Constants
    const DURATION_SINGLE_DAY = 'single_day';
    const DURATION_MULTIPLE_DAYS = 'multiple_days';

    // Transportation Constants
    const TRANSPORT_UNIVERSITY_VEHICLE = 'university_vehicle';
    const TRANSPORT_PUBLIC_CONVEYANCE = 'public_conveyance';
    const TRANSPORT_PRIVATE_VEHICLE = 'private_vehicle';

    // Source of Funds Constants
    const FUNDS_MOOE = 'mooe';
    const FUNDS_PERSONAL = 'personal';
    const FUNDS_OTHER = 'other';

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public static function getTravelTypes()
    {
        return [
            self::TYPE_OFFICIAL_TIME => 'Official Time',
            self::TYPE_OFFICIAL_BUSINESS => 'Official Business',
            self::TYPE_PERSONAL_ABROAD => 'Personal Travel Abroad',
            self::TYPE_OFFICIAL_TRAVEL => 'Official Travel',
        ];
    }

    public static function getDurationTypes()
    {
        return [
            self::DURATION_SINGLE_DAY => 'Single Day',
            self::DURATION_MULTIPLE_DAYS => 'Multiple Days',
        ];
    }

    public static function getTransportationTypes()
    {
        return [
            self::TRANSPORT_UNIVERSITY_VEHICLE => 'University Vehicle',
            self::TRANSPORT_PUBLIC_CONVEYANCE => 'Public Conveyance',
            self::TRANSPORT_PRIVATE_VEHICLE => 'Private Vehicle',
        ];
    }

    public static function getFundSources()
    {
        return [
            self::FUNDS_MOOE => 'MOOE (Maintenance and Other Operating Expenses)',
            self::FUNDS_PERSONAL => 'Personal Funds',
            self::FUNDS_OTHER => 'Other Sources',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Boot the model
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->submitted_at) {
                $model->submitted_at = now();
            }
            if (!$model->status) {
                $model->status = self::STATUS_PENDING;
            }
        });
    }

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Alias so views can use $travel->user to reach the Employee's User
     */
    public function getUserAttribute()
    {
        return $this->employee?->user;
    }

    /**
     * Alias for start_date used in older views
     */
    public function getInclusiveDateOfTravelAttribute()
    {
        return $this->start_date;
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function recommendingOfficial()
    {
        return $this->belongsTo(User::class, 'recommending_official_id');
    }

    public function approvals()
    {
        return $this->hasMany(TravelAuthorityApproval::class);
    }

    /**
     * Get approval by type
     */
    public function getApprovalByType($type)
    {
        return $this->approvals()->where('approval_type', $type)->first();
    }

    /**
     * Get total travel duration in days
     */
    public function getDurationAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        return $this->end_date->diffInDays($this->start_date) + 1;
    }

    /**
     * Get travel type display name
     */
    public function getTravelTypeDisplay(): string
    {
        return self::getTravelTypes()[$this->travel_type] ?? $this->travel_type;
    }

    /**
     * Get transportation display name
     */
    public function getTransportationDisplay(): string
    {
        return self::getTransportationTypes()[$this->transportation] ?? $this->transportation;
    }

    /**
     * Get funds source display name
     */
    public function getFundSourceDisplay(): string
    {
        return self::getFundSources()[$this->source_of_funds] ?? $this->source_of_funds;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplay(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Check if travel is fully approved
     */
    public function isFullyApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED && 
               $this->allApprovalsComplete();
    }

    /**
     * Check if travel is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED || $this->hasRejection();
    }

    /**
     * Get rejection reason
     */
    public function getRejectionReason(): ?string
    {
        return $this->rejection_reason;
    }

    /**
     * Determine which stage a user can approve
     */
    public function canBeApprovedBy(User $user): ?string
    {
        // Get next pending approval stage
        $nextApproval = $this->getNextApprovalStep();
        
        if (!$nextApproval) {
            return null;
        }

        // Check if user has the required role for this stage
        $userRoles = $user->getRoleNames()->toArray();
        
        $stageRoles = [
            'finance_officer_approval' => ['Finance Officer', 'Chief Administrative Officer', 'Super Admin'],
            'accountant_approval' => ['Accountant', 'Super Admin'],
            'dept_head_approval' => ['Department Head', 'Super Admin'],
            'president_approval' => ['University President', 'Super Admin']
        ];

        if (isset($stageRoles[$nextApproval['type']])) {
            foreach ($userRoles as $role) {
                if (in_array($role, $stageRoles[$nextApproval['type']])) {
                    return $nextApproval['type'];
                }
            }
        }

        return null;
    }

    /**
     * Get the next pending approval stage
     */
    public function getNextApprovalStep(): ?array
    {
        $stages = $this->getApprovalStages();
        
        foreach ($stages as $stage) {
            $approval = $this->approvals()->where('approval_type', $stage['type'])->first();
            if (!$approval || $approval->status === 'pending') {
                return $stage;
            }
        }
        
        return null;
    }

    /**
     * Get all approval stages
     */
    public function getApprovalStages(): array
    {
        return [
            ['order' => 1, 'type' => 'finance_officer_approval', 'label' => 'Finance Officer Approval'],
            ['order' => 2, 'type' => 'accountant_approval', 'label' => 'Accountant Approval'],
            ['order' => 3, 'type' => 'dept_head_approval', 'label' => 'Department Head Approval'],
            ['order' => 4, 'type' => 'president_approval', 'label' => 'University President Final Approval'],
        ];
    }

    /**
     * Check if all approvals are complete
     */
    public function allApprovalsComplete(): bool
    {
        $stages = $this->getApprovalStages();
        $approvals = $this->approvals()->get();
        
        if ($approvals->count() !== count($stages)) {
            return false;
        }

        foreach ($approvals as $approval) {
            if ($approval->status === 'pending') {
                return false;
            }
            if ($approval->status === 'rejected') {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if request has been rejected at any stage
     */
    public function hasRejection(): bool
    {
        return $this->approvals()->where('status', 'rejected')->exists();
    }

    /**
     * Mark travel as completed
     */
    public function markCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now()
        ]);
    }

    /**
     * Mark travel as cancelled
     */
    public function markCancelled(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    /**
     * Reject the travel authority
     */
    public function reject(string $reason): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason
        ]);
    }
}