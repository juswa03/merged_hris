<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedScanAttempt extends Model
{
    use HasFactory;

    protected $table = 'failed_scan_attempts';

    protected $fillable = [
        'employee_id',
        'device_uid',
        'failure_reason',
        'quality_score',
        'ip_address',
        'attempted_at'
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'quality_score' => 'integer'
    ];

    /**
     * Get the employee associated with this failed scan
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the device associated with this failed scan
     */
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_uid', 'device_uid');
    }

    /**
     * Scope to filter by failure reason
     */
    public function scopeByReason($query, $reason)
    {
        return $query->where('failure_reason', $reason);
    }

    /**
     * Scope to get recent failures
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('attempted_at', '>=', now()->subDays($days));
    }
}
