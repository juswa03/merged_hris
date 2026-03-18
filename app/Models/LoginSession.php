<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginSession extends Model
{
    protected $table = 'tbl_login_sessions';

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'logged_in_at',
        'last_activity_at',
        'logged_out_at',
        'is_revoked',
    ];

    protected $casts = [
        'logged_in_at'     => 'datetime',
        'last_activity_at' => 'datetime',
        'logged_out_at'    => 'datetime',
        'is_revoked'       => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return is_null($this->logged_out_at)
            && ! $this->is_revoked
            && $this->last_activity_at->diffInMinutes(now()) < 120;
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_revoked) return 'Revoked';
        if ($this->logged_out_at) return 'Logged Out';
        if ($this->last_activity_at->diffInMinutes(now()) >= 120) return 'Expired';
        return 'Active';
    }

    public function getBrowserAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (str_contains($ua, 'Chrome')) return 'Chrome';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Safari')) return 'Safari';
        if (str_contains($ua, 'Edge')) return 'Edge';
        return 'Unknown';
    }
}
