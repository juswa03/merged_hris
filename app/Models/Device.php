<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $table = 'devices';

    protected $fillable = [
        // Common fields
        'device_uid',
        'device_type', // 'generic' or 'biometric'

        // Generic device fields (for office equipment)
        'device_id',
        'building',
        'floor',
        'room',
        'department_id',

        // Biometric device fields (for fingerprint scanners)
        'device_name',
        'device_model',
        'serial_number',
        'firmware_version',
        'ip_address',
        'port',
        'location',
        'status',
        'last_sync_at',
        'last_heartbeat_at',
        'total_capacity',
        'templates_count',
        'settings',
        'notes',
    ];

    protected $casts = [
        'settings' => 'array',
        'last_sync_at' => 'datetime',
        'last_heartbeat_at' => 'datetime',
        'templates_count' => 'integer',
        'port' => 'integer',
        'total_capacity' => 'integer',
    ];

    // =====================================
    // Relationships
    // =====================================

    /**
     * Get the department for generic devices
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get all attendance records for this device
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'device_uid', 'device_uid');
    }

    /**
     * Get failed scans for this device
     */
    public function failedScans()
    {
        return $this->hasMany(FailedScanAttempt::class, 'device_uid', 'device_uid');
    }

    // =====================================
    // Scopes
    // =====================================

    /**
     * Scope to get only biometric devices
     */
    public function scopeBiometric($query)
    {
        return $query->where('device_type', 'biometric');
    }

    /**
     * Scope to get only generic devices
     */
    public function scopeGeneric($query)
    {
        return $query->where('device_type', 'generic');
    }

    /**
     * Scope to get only active devices
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get online devices (received heartbeat within last 5 minutes)
     */
    public function scopeOnline($query)
    {
        return $query->where('last_heartbeat_at', '>=', now()->subMinutes(5));
    }

    // =====================================
    // Helper Methods
    // =====================================

    /**
     * Check if device is a biometric scanner
     */
    public function isBiometric(): bool
    {
        return $this->device_type === 'biometric';
    }

    /**
     * Check if device is a generic office device
     */
    public function isGeneric(): bool
    {
        return $this->device_type === 'generic';
    }

    /**
     * Check if biometric device is online (received heartbeat within last 5 minutes)
     */
    public function isOnline(): bool
    {
        if (!$this->isBiometric() || !$this->last_heartbeat_at) {
            return false;
        }

        return $this->last_heartbeat_at->diffInMinutes(now()) <= 5;
    }

    /**
     * Get device display name based on type
     */
    public function getDisplayName(): string
    {
        if ($this->isBiometric()) {
            return $this->device_name ?? $this->device_uid;
        }

        return $this->device_id . ' (' . $this->location ?? $this->building . ' - ' . $this->room . ')';
    }

    /**
     * Get device location description
     */
    public function getLocationDescription(): string
    {
        if ($this->isBiometric()) {
            return $this->location ?? 'Unknown Location';
        }

        return "{$this->building}, Floor {$this->floor}, Room {$this->room}";
    }
}
