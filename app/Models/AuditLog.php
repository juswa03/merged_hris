<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'tbl_audit_logs';

    protected $fillable = [
        'user_id', 'user_name', 'action',
        'model_type', 'model_id', 'model_label',
        'old_values', 'new_values',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getChangedFieldsAttribute(): array
    {
        if (!$this->new_values) return [];
        $skip = ['updated_at', 'created_at'];
        return array_diff_key($this->new_values, array_flip($skip));
    }

    public function actionBadgeClass(): string
    {
        return match($this->action) {
            'created' => 'bg-green-100 text-green-800',
            'updated' => 'bg-blue-100 text-blue-800',
            'deleted' => 'bg-red-100 text-red-800',
            'viewed'  => 'bg-gray-100 text-gray-600',
            default   => 'bg-gray-100 text-gray-600',
        };
    }
}
