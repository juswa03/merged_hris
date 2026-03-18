<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PDS extends Model
{
    use HasFactory;

    protected $table = 'pds';

    protected $fillable = [
        'employee_id',
        'status',
        'submitted_at',
        'verified_at',
        'rejected_at',
        'last_action_by',
        'last_action_at',
        'verification_remarks',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
        'last_action_at' => 'datetime',
    ];

    // Status Constants
    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get all status options
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_INCOMPLETE => 'Incomplete',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    /**
     * Boot the model
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->status) {
                $model->status = self::STATUS_INCOMPLETE;
            }
        });
    }

    // Relationships
    /**
     * The employee who owns this PDS
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * The HR/Admin user who last acted on this PDS
     */
    public function lastActionBy()
    {
        return $this->belongsTo(User::class, 'last_action_by');
    }

    // Accessors & Mutators

    /**
     * Get status display name
     */
    public function getStatusDisplay(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Check if PDS is incomplete
     */
    public function isIncomplete(): bool
    {
        return $this->status === self::STATUS_INCOMPLETE;
    }

    /**
     * Check if PDS is submitted
     */
    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /**
     * Check if PDS is under review
     */
    public function isUnderReview(): bool
    {
        return $this->status === self::STATUS_UNDER_REVIEW;
    }

    /**
     * Check if PDS is verified
     */
    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    /**
     * Check if PDS is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if PDS can be edited
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, [
            self::STATUS_INCOMPLETE,
            self::STATUS_REJECTED,
        ]);
    }

    /**
     * Mark PDS as submitted
     */
    public function markSubmitted(User $submittedBy = null): void
    {
        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'last_action_by' => $submittedBy->id ?? null,
            'last_action_at' => now(),
        ]);
    }

    /**
     * Mark PDS as under review
     */
    public function markUnderReview(User $reviewedBy): void
    {
        $this->update([
            'status' => self::STATUS_UNDER_REVIEW,
            'last_action_by' => $reviewedBy->id,
            'last_action_at' => now(),
        ]);
    }

    /**
     * Mark PDS as verified
     */
    public function verify(User $verifiedBy, string $remarks = null): void
    {
        $this->update([
            'status' => self::STATUS_VERIFIED,
            'verified_at' => now(),
            'verification_remarks' => $remarks,
            'last_action_by' => $verifiedBy->id,
            'last_action_at' => now(),
        ]);
    }

    /**
     * Reject PDS
     */
    public function reject(User $rejectedBy, string $remarks): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
            'verification_remarks' => $remarks,
            'last_action_by' => $rejectedBy->id,
            'last_action_at' => now(),
        ]);
    }

    /**
     * Get a PDS data field
     */
    public function getDataField(string $key, $default = null)
    {
        return data_get($this->data, $key, $default);
    }

    /**
     * Set a PDS data field
     */
    public function setDataField(string $key, $value): self
    {
        $data = $this->data ?? [];
        data_set($data, $key, $value);
        $this->data = $data;
        return $this;
    }

    /**
     * Get days since submission
     */
    public function getDaysSinceSubmission(): ?int
    {
        if (!$this->submitted_at) {
            return null;
        }
        return now()->diffInDays($this->submitted_at);
    }

    /**
     * Check if PDS is overdue (more than 30 days pending review)
     */
    public function isOverdue(): bool
    {
        if (!$this->isSubmitted() && !$this->isUnderReview()) {
            return false;
        }

        return $this->getDaysSinceSubmission() > 30;
    }
}
