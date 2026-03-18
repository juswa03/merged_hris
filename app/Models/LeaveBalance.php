<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'opening_balance',
        'earned',
        'used',
        'closing_balance',
        'year',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:4',
        'earned'          => 'decimal:4',
        'used'            => 'decimal:4',
        'closing_balance' => 'decimal:4',
        'year'            => 'integer',
    ];

    const LEAVE_TYPES = [
        'vacation'          => 'Vacation Leave (VL)',
        'sick'              => 'Sick Leave (SL)',
        'forced'            => 'Forced Leave (FL)',
        'special_privilege' => 'Special Privilege Leave',
        'maternity'         => 'Maternity Leave',
        'paternity'         => 'Paternity Leave',
        'solo_parent'       => 'Solo Parent Leave',
        'study'             => 'Study Leave',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}