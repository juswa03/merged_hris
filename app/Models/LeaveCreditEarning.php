<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveCreditEarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'credits_earned',
        'period_from',
        'period_to',
        'remarks',
    ];

    protected $casts = [
        'credits_earned' => 'decimal:4',
        'period_from'    => 'date',
        'period_to'      => 'date',
    ];

    const TYPE_VACATION = 'vacation';
    const TYPE_SICK = 'sick';
    const TYPE_FORCED = 'forced';
    const TYPE_SPECIAL_PRIVILEGE = 'special_privilege';
    const TYPE_MATERNITY = 'maternity';
    const TYPE_PATERNITY = 'paternity';
    const TYPE_SOLO_PARENT = 'solo_parent';
    const TYPE_STUDY = 'study';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public static function getCreditTypes(): array
    {
        return [
            self::TYPE_VACATION => 'Vacation Leave',
            self::TYPE_SICK => 'Sick Leave',
            self::TYPE_FORCED => 'Forced Leave',
            self::TYPE_SPECIAL_PRIVILEGE => 'Special Privilege Leave',
            self::TYPE_MATERNITY => 'Maternity Leave',
            self::TYPE_PATERNITY => 'Paternity Leave',
            self::TYPE_SPECIAL_PRIVILEGE => 'Special Leave Privilege',
            self::TYPE_FORCED_LEAVE => 'Forced Leave',
        ];
    }
}