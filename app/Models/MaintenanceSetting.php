<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSetting extends Model
{
    protected $table = 'tbl_maintenance_settings';

    protected $fillable = [
        'is_active',
        'title',
        'message',
        'whitelisted_ips',
        'scheduled_end_at',
        'activated_by',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'scheduled_end_at' => 'datetime',
    ];

    /** Get settings (always a single row) */
    public static function settings(): self
    {
        return static::firstOrCreate([], [
            'is_active' => false,
            'title'     => 'System Maintenance',
            'message'   => 'We are currently performing scheduled maintenance. Please check back soon.',
        ]);
    }

    public function getWhitelistedIpsArrayAttribute(): array
    {
        if (empty($this->whitelisted_ips)) return [];
        return array_filter(array_map('trim', explode(',', $this->whitelisted_ips)));
    }
}
