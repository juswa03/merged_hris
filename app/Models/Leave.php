<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Leave extends Model
{
    use HasFactory;

    protected $table = 'leave';

    protected $fillable = [
        'employee_id',
        'department_id',
        'filing_date',
        'position_id',
        'type',
        
        // ========== CSC-SPECIFIC FIELDS ==========
        'csc_employee_type',
        'leave_basis',
        'is_vacation_service',
        'service_credits_used',
        'start_time',
        'end_time',
        'maternity_delivery_date',
        'paternity_delivery_count',
        'is_miscarriage',
        'slp_type',
        'is_lwop',
        'is_monetized',
        'monetized_days',
        'monetization_amount',
        'is_forced_leave',
        'is_terminal_leave',
        'separation_type',
        'computation_method',
        'computation_notes',
        'medical_certificate_issued_date',
        'is_fit_to_work',
        'actual_service_days',
        'included_in_service',
        
        // Date and duration fields
        'start_date',
        'end_date',
        'duration_type',
        'half_day_time',
        
        // Commutation and reason
        'commutation',
        'reason',
        
        // Documents
        'signature_data',
        'electronic_signature_path',
        'medical_certificate_path',
        'travel_itinerary_path',
        
        // Leave credits
        'credit_as_of_date',
        'vacation_earned',
        'vacation_less',
        'vacation_balance',
        'sick_earned',
        'sick_less',
        'sick_balance',
        
        // Approval fields
        'recommendation',
        'approved_for',
        'with_pay_days',
        'without_pay_days',
        'others_specify',
        'rejection_reason',
        
        // Workflow - Simplified
        'workflow_status',
        'certified_by',
        'certified_at',
        'recommended_by',
        'recommended_at',
        'approved_by_president',
        'approved_by_president_at',
        'approved_by',
        'approved_at',
        
        // Additional
        'status',
        'admin_notes',
        'handover_person_id',
        'handover_notes',
        'pdf_path',
        
        // Leave type specific details (JSON)
        'leave_type_details',
    ];

    protected $casts = [
        'filing_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'credit_as_of_date' => 'date',
        'maternity_delivery_date' => 'date',
        'medical_certificate_issued_date' => 'date',
        'salary' => 'decimal:2',
        'service_credits_used' => 'decimal:4',
        'monetized_days' => 'decimal:4',
        'monetization_amount' => 'decimal:2',
        'actual_service_days' => 'decimal:4',
        'vacation_earned' => 'decimal:4',
        'vacation_less' => 'decimal:4',
        'vacation_balance' => 'decimal:4',
        'sick_earned' => 'decimal:4',
        'sick_less' => 'decimal:4',
        'sick_balance' => 'decimal:4',
        'approved_at' => 'datetime',
        'certified_at' => 'datetime',
        'recommended_at' => 'datetime',
        'approved_by_president_at' => 'datetime',
        'last_computed_at' => 'datetime',
        'is_vacation_service' => 'boolean',
        'is_miscarriage' => 'boolean',
        'is_lwop' => 'boolean',
        'is_monetized' => 'boolean',
        'is_forced_leave' => 'boolean',
        'is_terminal_leave' => 'boolean',
        'is_fit_to_work' => 'boolean',
        'included_in_service' => 'boolean',
        'leave_type_details' => 'array',
    ];

    // Leave type constants
    const TYPE_VACATION = 'vacation';
    const TYPE_MANDATORY = 'mandatory';
    const TYPE_SICK = 'sick';
    const TYPE_MATERNITY = 'maternity';
    const TYPE_PATERNITY = 'paternity';
    const TYPE_SPECIAL_PRIVILEGE = 'special_privilege';
    const TYPE_SOLO_PARENT = 'solo_parent';
    const TYPE_STUDY = 'study';
    const TYPE_VAWC = 'vawc';
    const TYPE_REHABILITATION = 'rehabilitation';
    const TYPE_SPECIAL_WOMEN = 'special_women';
    const TYPE_EMERGENCY = 'emergency';
    const TYPE_ADOPTION = 'adoption';
    const TYPE_MONETIZATION = 'monetization';
    const TYPE_TERMINAL = 'terminal';
    const TYPE_OTHER = 'other';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Commutation constants
    const COMMUTATION_REQUESTED = 'requested';
    const COMMUTATION_NOT_REQUESTED = 'not_requested';

    // Duration type constants
    const DURATION_FULL_DAY = 'full_day';
    const DURATION_HALF_DAY = 'half_day';
    const DURATION_MULTIPLE_DAYS = 'multiple_days';

    // Half day time constants
    const HALF_DAY_MORNING = 'morning';
    const HALF_DAY_AFTERNOON = 'afternoon';

    // Recommendation constants
    const RECOMMENDATION_APPROVE = 'approve';
    const RECOMMENDATION_DISAPPROVE = 'disapprove';

    // Approved for constants
    const APPROVED_WITH_PAY = 'with_pay';
    const APPROVED_WITHOUT_PAY = 'without_pay';
    const APPROVED_OTHERS = 'others';

    // CSC Employee Type constants
    const CSC_REGULAR = 'regular';
    const CSC_TEACHER = 'teacher';
    const CSC_PART_TIME = 'part_time';
    const CSC_CONTRACTUAL = 'contractual';
    const CSC_LOCAL_ELECTIVE = 'local_elective';
    const CSC_JUDICIAL = 'judicial';
    const CSC_EXECUTIVE = 'executive';
    const CSC_FACULTY = 'faculty';

    // Leave Basis constants
    const BASIS_STANDARD_VL_SL = 'standard_vl_sl';
    const BASIS_TEACHER_PVP = 'teacher_pvp';
    const BASIS_SPECIAL_LAW = 'special_law';
    const BASIS_PART_TIME_PROPORTIONAL = 'part_time_proportional';

    // Separation Type constants
    const SEPARATION_RETIREMENT = 'retirement';
    const SEPARATION_VOLUNTARY_RESIGNATION = 'voluntary_resignation';
    const SEPARATION_NO_FAULT = 'separation_no_fault';
    const SEPARATION_NONE = 'none';

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($leave) {
            if (!$leave->filing_date) {
                $leave->filing_date = now();
            }
            
            if (!$leave->workflow_status) {
                $leave->workflow_status = 'pending';
            }
        });
    }

    /**
     * Get available leave types
     */
    public static function getLeaveTypes(): array
    {
        return [
            self::TYPE_VACATION => 'Vacation Leave',
            self::TYPE_MANDATORY => 'Mandatory/Forced Leave',
            self::TYPE_SICK => 'Sick Leave',
            self::TYPE_MATERNITY => 'Maternity Leave',
            self::TYPE_PATERNITY => 'Paternity Leave',
            self::TYPE_SPECIAL_PRIVILEGE => 'Special Privilege Leave',
            self::TYPE_SOLO_PARENT => 'Solo Parent Leave',
            self::TYPE_STUDY => 'Study Leave',
            self::TYPE_VAWC => '10-Day VAWC Leave',
            self::TYPE_REHABILITATION => 'Rehabilitation Privilege Leave',
            self::TYPE_SPECIAL_WOMEN => 'Special Leave Benefits for Women',
            self::TYPE_EMERGENCY => 'Special Emergency (Calamity) Leave',
            self::TYPE_ADOPTION => 'Adoption Leave',
            self::TYPE_MONETIZATION => 'Monetization of Leave Credits',
            self::TYPE_TERMINAL => 'Terminal Leave',
            self::TYPE_OTHER => 'Other Leave Types',
        ];
    }

    /**
     * Get CSC employee type options
     */
    public static function getCscEmployeeTypes(): array
    {
        return [
            self::CSC_REGULAR => 'Regular Employee',
            self::CSC_TEACHER => 'Teacher',
            self::CSC_PART_TIME => 'Part-time Employee',
            self::CSC_CONTRACTUAL => 'Contractual',
            self::CSC_LOCAL_ELECTIVE => 'Local Elective Official',
            self::CSC_JUDICIAL => 'Judicial Official',
            self::CSC_EXECUTIVE => 'Executive Official',
            self::CSC_FACULTY => 'Faculty Member',
        ];
    }

    /**
     * Get leave basis options
     */
    public static function getLeaveBasisOptions(): array
    {
        return [
            self::BASIS_STANDARD_VL_SL => 'Standard VL/SL (Regular Employees)',
            self::BASIS_TEACHER_PVP => 'Teacher Proportional Vacation Pay',
            self::BASIS_SPECIAL_LAW => 'Special Law Coverage',
            self::BASIS_PART_TIME_PROPORTIONAL => 'Part-time Proportional',
        ];
    }

    /**
     * Get separation type options
     */
    public static function getSeparationTypes(): array
    {
        return [
            self::SEPARATION_RETIREMENT => 'Retirement',
            self::SEPARATION_VOLUNTARY_RESIGNATION => 'Voluntary Resignation',
            self::SEPARATION_NO_FAULT => 'Separation (No Fault)',
            self::SEPARATION_NONE => 'None',
        ];
    }

    /**
     * Get duration types
     */
    public static function getDurationTypes(): array
    {
        return [
            self::DURATION_FULL_DAY => 'Full Day',
            self::DURATION_HALF_DAY => 'Half Day',
            self::DURATION_MULTIPLE_DAYS => 'Multiple Days',
        ];
    }

    /**
     * Get half day time options
     */
    public static function getHalfDayTimes(): array
    {
        return [
            self::HALF_DAY_MORNING => 'Morning (8:00 AM - 12:00 PM)',
            self::HALF_DAY_AFTERNOON => 'Afternoon (1:00 PM - 5:00 PM)',
        ];
    }

    /**
     * Get SLP type options
     */
    public static function getSlpTypes(): array
    {
        return [
            'funeral_mourning' => 'Funeral/Mourning',
            'graduation' => 'Graduation Ceremony',
            'enrollment' => 'Enrollment of Child',
            'wedding_anniversary' => 'Wedding Anniversary',
            'birthday' => 'Birthday',
            'hospitalization' => 'Hospitalization (Immediate Family)',
            'accident' => 'Accident (Immediate Family)',
            'relocation' => 'Relocation',
            'government_transaction' => 'Government Transaction',
            'calamity' => 'Calamity/Disaster',
            'none' => 'None',
        ];
    }

    /**
     * Get leave type requirements
     */
    public static function getLeaveRequirements(string $type): array
    {
        $requirements = [
            self::TYPE_VACATION => [
                'title' => 'Vacation Leave Requirements',
                'color' => 'blue',
                'items' => [
                    'Minimum 3 working days advance notice required',
                    'Maximum consecutive leave: 15 working days',
                    'Blackout periods may apply during peak seasons',
                    'Coordinate with your team before submission',
                    'Forced leave: Minimum 5 days must be taken annually'
                ]
            ],
            self::TYPE_SICK => [
                'title' => 'Sick Leave Requirements',
                'color' => 'green',
                'items' => [
                    'Medical certificate required for leaves exceeding 3 days',
                    'Notification should be sent as soon as possible',
                    'Follow-up documents may be requested by HR',
                    'Contact your supervisor immediately for emergencies',
                    'Fit-to-work certificate required after 5 days of sick leave'
                ]
            ],
            self::TYPE_MATERNITY => [
                'title' => 'Maternity Leave Requirements (CSC)',
                'color' => 'pink',
                'items' => [
                    '105 days maternity leave as per R.A. No. 11210',
                    'Submit certificate of pregnancy from physician',
                    'Additional 15 days for solo mothers',
                    '30 days for miscarriage or ectopic pregnancy',
                    'For female employees only (married or unmarried)',
                    'With or without pay depending on service years'
                ]
            ],
            self::TYPE_PATERNITY => [
                'title' => 'Paternity Leave Requirements (CSC)',
                'color' => 'blue',
                'items' => [
                    '7 days paternity leave as per R.A. No. 8187',
                    'Available to married male employees only',
                    'For first four deliveries of legitimate spouse',
                    'Submit marriage certificate and child birth documents',
                    'Not applicable for live-in partners or illegitimate children'
                ]
            ],
            self::TYPE_SPECIAL_PRIVILEGE => [
                'title' => 'Special Privilege Leave (CSC)',
                'color' => 'purple',
                'items' => [
                    'Maximum 3 days per year for any combination',
                    'Non-cumulative and non-commutative',
                    'Valid reasons: Funeral, graduation, enrollment, etc.',
                    'Cannot be used for vacation purposes',
                    'Documentation may be required depending on reason'
                ]
            ],
        ];

        return $requirements[$type] ?? [
            'title' => 'Leave Requirements',
            'color' => 'blue',
            'items' => [
                'Please ensure all required documents are submitted',
                'Follow agency-specific guidelines for this leave type',
                'Compliance with CSC Omnibus Rules is required'
            ]
        ];
    }

    /**
     * Get the PDF download URL
     */
    public function getPdfDownloadUrl()
    {
        return route('employees.leaves.download-pdf', $this);
    }

    /**
     * Check if PDF exists
     */
    public function hasPdf()
    {
        return $this->pdf_path && Storage::exists($this->pdf_path);
    }

    /**
     * Relationship with User (Applicant)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    /**
     * Relationship with Approver
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship with HR Head (Section 7.A Certifier)
     */
    public function certifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'certified_by');
    }

    /**
     * Relationship with Department Head (Section 7.B Recommender)
     */
    public function recommendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }

    /**
     * Relationship with President (Section 7.C/7.D Approver)
     */
    public function approvedByPresident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_president');
    }

    /**
     * Relationship with Handover Person
     */
    public function handoverPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handover_person_id');
    }

    /**
     * Check if leave can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if leave can be edited
     */
    public function canBeEdited(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Calculate total days based on leave dates and duration type
     */
    public function getDaysAttribute(): float
    {
        if ($this->duration_type === self::DURATION_HALF_DAY) {
            return 0.5;
        }

        if ($this->duration_type === self::DURATION_FULL_DAY) {
            return 1.0;
        }

        if ($this->start_date && $this->end_date && $this->duration_type === self::DURATION_MULTIPLE_DAYS) {
            return $this->end_date->diffInDays($this->start_date) + 1;
        }

        return 0.0;
    }

    /**
     * Get total hours for the leave period
     */
    public function getTotalHoursAttribute(): float
    {
        if ($this->duration_type === self::DURATION_HALF_DAY) {
            return 4.0; // Half day is 4 hours
        }

        if ($this->duration_type === self::DURATION_FULL_DAY) {
            $workHoursPerDay = $this->user->work_hours_per_day ?? 8.0;
            return $workHoursPerDay;
        }

        if ($this->start_date && $this->end_date && $this->duration_type === self::DURATION_MULTIPLE_DAYS) {
            $days = $this->getDaysAttribute();
            $workHoursPerDay = $this->user->work_hours_per_day ?? 8.0;
            return $days * $workHoursPerDay;
        }

        return 0.0;
    }

    /**
     * Get CSC equivalent days (computed from total hours)
     */
    public function getEquivalentDaysCscAttribute(): float
    {
        if (!$this->user) {
            return $this->getDaysAttribute();
        }

        $workHoursPerDay = $this->user->work_hours_per_day ?? 8.0;
        return round($this->getTotalHoursAttribute() / $workHoursPerDay, 4);
    }

    /**
     * Get formatted status with color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            default => 'gray'
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800 border-green-200',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800 border-red-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200'
        };
    }

    /**
     * Scope for pending leaves
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved leaves
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for rejected leaves
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope for current user's leaves
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for leaves in department
     */
    public function scopeInDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Get the display name for leave type
     */
    public function getTypeDisplayName(): string
    {
        return self::getLeaveTypes()[$this->type] ?? $this->type;
    }

    /**
     * Get the display name for leave type (alias)
     */
    public function getLeaveTypeDisplay(): string
    {
        return $this->getTypeDisplayName();
    }

    /**
     * Get CSC employee type display name
     */
    public function getCscEmployeeTypeDisplay(): string
    {
        return self::getCscEmployeeTypes()[$this->csc_employee_type] ?? $this->csc_employee_type;
    }

    /**
     * Get leave classification display name
     */
    public function getLeaveClassificationDisplay(): string
    {
        return $this->getCscEmployeeTypeDisplay();
    }

    /**
     * Get leave basis display name
     */
    public function getLeaveBasisDisplay(): string
    {
        return self::getLeaveBasisOptions()[$this->leave_basis] ?? $this->leave_basis;
    }

    /**
     * Get SLP type display name
     */
    public function getSlpTypeDisplay(): string
    {
        return self::getSlpTypes()[$this->slp_type] ?? $this->slp_type;
    }

    /**
     * Get half-day time display name
     */
    public function getHalfDayTimeDisplay(): string
    {
        $labels = [
            'morning' => 'Morning (8:00 AM - 12:00 PM)',
            'afternoon' => 'Afternoon (1:00 PM - 5:00 PM)',
            'custom' => 'Custom Hours'
        ];
        
        if ($this->half_day_time === 'custom' && $this->start_time && $this->end_time) {
            return "Custom Hours ({$this->start_time} - {$this->end_time})";
        }
        
        return $labels[$this->half_day_time] ?? ucfirst($this->half_day_time ?? 'N/A');
    }

    /**
     * Get separation type display
     */
    public function getSeparationTypeDisplay(): string
    {
        return self::getSeparationTypes()[$this->separation_type] ?? $this->separation_type;
    }

    /**
     * Check if leave requires medical certificate
     */
    public function requiresMedicalCertificate(): bool
    {
        return in_array($this->type, [
            self::TYPE_SICK,
            self::TYPE_MATERNITY,
            self::TYPE_REHABILITATION
        ]);
    }

    /**
     * Check if leave requires additional documentation
     */
    public function requiresAdditionalDocuments(): bool
    {
        return in_array($this->type, [
            self::TYPE_MATERNITY,
            self::TYPE_PATERNITY,
            self::TYPE_SOLO_PARENT,
            self::TYPE_STUDY,
            self::TYPE_VAWC,
            self::TYPE_ADOPTION
        ]);
    }

    /**
     * Check if requires fit-to-work certificate
     */
    public function requiresFitToWorkCertificate(): bool
    {
        return in_array($this->type, [
            self::TYPE_SICK,
            self::TYPE_MATERNITY,
            self::TYPE_REHABILITATION
        ]) && $this->days >= 5;
    }

    /**
     * Check if PDF exists and is ready
     */
    public function isPdfReady(): bool
    {
        return $this->pdf_path && Storage::exists($this->pdf_path);
    }

    /**
     * Get PDF file size for display
     */
    public function getPdfFileSize(): string
    {
        if (!$this->isPdfReady()) {
            return 'Not generated';
        }
        
        $size = Storage::size($this->pdf_path);
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($size) - 1) / 3);
        
        return sprintf("%.2f", $size / pow(1024, $factor)) . ' ' . $units[$factor];
    }

    /**
     * Check if leave follows CSC Omnibus Rules
     */
    public function followsCscRules(): bool
    {
        return in_array($this->csc_employee_type, [
            self::CSC_REGULAR,
            self::CSC_TEACHER,
            self::CSC_PART_TIME,
            self::CSC_CONTRACTUAL,
        ]);
    }

    /**
     * Check if this is a special privilege leave
     */
    public function isSpecialPrivilege(): bool
    {
        return $this->slp_type !== 'none';
    }

    /**
     * Check if employee is eligible for this leave type
     */
    public function isEligible(): bool
    {
        $user = $this->user;
        
        if (!$user) {
            return false;
        }

        switch ($this->type) {
            case self::TYPE_MATERNITY:
                return $user->gender === 'Female' && 
                       $user->marital_status === 'married';
                
            case self::TYPE_PATERNITY:
                return $user->gender === 'Male' && 
                       $user->marital_status === 'married' &&
                       $user->delivery_count < 4;
                
            case self::TYPE_SOLO_PARENT:
                // Add solo parent eligibility logic
                return true;
                
            default:
                return true;
        }
    }

    /**
     * Get effective leave days (CSC equivalent)
     */
    public function getEffectiveDaysAttribute(): float
    {
        return $this->getEquivalentDaysCscAttribute();
    }

    /**
     * Scope for CSC-regulated leaves
     */
    public function scopeCscRegulated($query)
    {
        return $query->whereIn('csc_employee_type', [
            self::CSC_REGULAR,
            self::CSC_TEACHER,
            self::CSC_PART_TIME,
            self::CSC_CONTRACTUAL,
        ]);
    }

    /**
     * Scope for teachers
     */
    public function scopeForTeachers($query)
    {
        return $query->where('csc_employee_type', self::CSC_TEACHER);
    }

    /**
     * Scope for part-time employees
     */
    public function scopeForPartTime($query)
    {
        return $query->where('csc_employee_type', self::CSC_PART_TIME);
    }

    /**
     * Scope for LWOP leaves
     */
    public function scopeLwop($query)
    {
        return $query->where('is_lwop', true);
    }

    /**
     * Scope for forced leaves
     */
    public function scopeForcedLeaves($query)
    {
        return $query->where('is_forced_leave', true);
    }

    /**
     * Scope for terminal leaves
     */
    public function scopeTerminalLeaves($query)
    {
        return $query->where('is_terminal_leave', true);
    }

    /**
     * Calculate LWOP deduction
     */
    public function calculateLwopDeduction(): array
    {
        if (!$this->is_lwop) {
            return ['rate' => 0, 'days_charged' => 0];
        }

        $days = min($this->getEquivalentDaysCscAttribute(), 4);
        $rates = [1 => 0.25, 2 => 0.50, 3 => 0.75, 4 => 1.00];
        $rate = $rates[(int)$days] ?? 1.00;
        $charged = $this->getEquivalentDaysCscAttribute() * $rate;

        return [
            'rate' => $rate,
            'days_charged' => $charged,
            'computation' => $this->getEquivalentDaysCscAttribute() . " days × $rate = $charged days"
        ];
    }

    /**
     * Get total work days between dates (excluding weekends)
     */
    public function getWorkingDaysCount(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $start = $this->start_date;
        $end = $this->end_date;
        $days = 0;

        while ($start <= $end) {
            if (!$start->isWeekend()) {
                $days++;
            }
            $start->addDay();
        }

        return $days;
    }

    /**
     * Check if this is within forced leave period
     */
    public function isWithinForcedLeavePeriod(): bool
    {
        // Forced leave should be taken within the calendar year
        return $this->start_date->year === now()->year;
    }

    /**
     * Get leave category for reporting
     */
    public function getLeaveCategory(): string
    {
        if ($this->is_terminal_leave) {
            return 'Terminal';
        }
        
        if ($this->is_monetized) {
            return 'Monetized';
        }
        
        if ($this->is_lwop) {
            return 'LWOP';
        }
        
        if ($this->is_forced_leave) {
            return 'Forced';
        }
        
        return 'Regular';
    }

    /**
     * Get a leave type detail value
     */
    public function getLeaveTypeDetail(string $key, $default = null)
    {
        return data_get($this->leave_type_details, $key, $default);
    }

    /**
     * Set a leave type detail value
     */
    public function setLeaveTypeDetail(string $key, $value): self
    {
        $details = $this->leave_type_details ?? [];
        data_set($details, $key, $value);
        $this->leave_type_details = $details;
        return $this;
    }

    /**
     * Get all leave type details
     */
    public function getLeaveTypeDetails(): array
    {
        return $this->leave_type_details ?? [];
    }

    /**
     * Workflow status constants
     */
    const WORKFLOW_PENDING = 'pending';
    const WORKFLOW_DEPT_RECOMMENDED = 'dept_recommended';
    const WORKFLOW_DEPT_REJECTED = 'dept_rejected';
    const WORKFLOW_HR_CERTIFIED = 'hr_certified';
    const WORKFLOW_PRESIDENT_APPROVED = 'president_approved';
    const WORKFLOW_PRESIDENT_REJECTED = 'president_rejected';
    const WORKFLOW_APPROVED = 'approved';
    const WORKFLOW_REJECTED = 'rejected';

    /**
     * Get workflow status display
     */
    public function getWorkflowStatusDisplay(): string
    {
        $statuses = [
            self::WORKFLOW_PENDING => 'Pending',
            self::WORKFLOW_DEPT_RECOMMENDED => 'Department Recommended',
            self::WORKFLOW_DEPT_REJECTED => 'Department Rejected',
            self::WORKFLOW_HR_CERTIFIED => 'HR Certified',
            self::WORKFLOW_PRESIDENT_APPROVED => 'President Approved',
            self::WORKFLOW_PRESIDENT_REJECTED => 'President Rejected',
            self::WORKFLOW_APPROVED => 'Approved',
            self::WORKFLOW_REJECTED => 'Rejected',
        ];
        return $statuses[$this->workflow_status] ?? $this->workflow_status;
    }

    /**
     * Check if leave is approved
     */
    public function isApproved(): bool
    {
        return in_array($this->workflow_status, [
            self::WORKFLOW_APPROVED,
            self::WORKFLOW_PRESIDENT_APPROVED,
        ]);
    }

    /**
     * Check if leave is rejected
     */
    public function isRejected(): bool
    {
        return in_array($this->workflow_status, [
            self::WORKFLOW_REJECTED,
            self::WORKFLOW_DEPT_REJECTED,
            self::WORKFLOW_PRESIDENT_REJECTED,
        ]);
    }

    /**
     * Check if leave is pending
     */
    public function isPending(): bool
    {
        return $this->workflow_status === self::WORKFLOW_PENDING;
    }
}