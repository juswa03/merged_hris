<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelAuthorityApproval extends Model
{
    use HasFactory;

    protected $table = 'travel_authority_approvals';

    protected $fillable = [
        'travel_authority_id',
        'approval_type',
        'approved_by',
        'approver_role',
        'status',
        'comments',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'signature_path',
        'order'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Approval type constants
    const TYPE_FINANCE_OFFICER = 'finance_officer_approval';
    const TYPE_ACCOUNTANT = 'accountant_approval';
    const TYPE_DEPT_HEAD = 'dept_head_approval';
    const TYPE_PRESIDENT = 'president_approval';

    /**
     * Get approval type display name
     */
    public static function getApprovalTypes()
    {
        return [
            self::TYPE_FINANCE_OFFICER => 'Finance Officer Approval',
            self::TYPE_ACCOUNTANT => 'Accountant Approval',
            self::TYPE_DEPT_HEAD => 'Department Head Approval',
            self::TYPE_PRESIDENT => 'University President Final Approval',
        ];
    }

    /**
     * Get status options
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    // Relationships
    public function travelAuthority()
    {
        return $this->belongsTo(TravelAuthority::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get approval type display
     */
    public function getApprovalTypeDisplay(): string
    {
        return self::getApprovalTypes()[$this->approval_type] ?? $this->approval_type;
    }

    /**
     * Get status display
     */
    public function getStatusDisplay(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Check if approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Approve this step
     */
    public function approve(User $user, string $comments = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $user->id,
            'approver_role' => $user->getRoleNames()->first(),
            'comments' => $comments,
            'approved_at' => now()
        ]);
    }

    /**
     * Reject this step
     */
    public function reject(User $user, string $reason, string $comments = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $user->id,
            'approver_role' => $user->getRoleNames()->first(),
            'comments' => $comments,
            'rejection_reason' => $reason,
            'rejected_at' => now()
        ]);
    }
}
