<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($model) => static::writeAuditLog('created', $model, null, $model->getAttributes()));
        static::updated(fn ($model) => static::writeAuditLog('updated', $model, $model->getOriginal(), $model->getChanges()));
        static::deleted(fn ($model) => static::writeAuditLog('deleted', $model, $model->getAttributes(), null));
    }

    private static function writeAuditLog(string $action, $model, ?array $old, ?array $new): void
    {
        // Prevent recursion
        if ($model instanceof AuditLog) {
            return;
        }

        try {
            $user    = auth()->user();
            $request = app('request');

            $hidden = $model->getHidden();
            if ($old) $old = array_diff_key($old, array_flip($hidden));
            if ($new) $new = array_diff_key($new, array_flip($hidden));

            // Strip unchanged values from old when action is updated
            if ($action === 'updated' && $old && $new) {
                $old = array_intersect_key($old, $new);
            }

            AuditLog::create([
                'user_id'     => $user?->id,
                'user_name'   => $user
                    ? ($user->employee?->full_name ?? $user->email)
                    : 'System',
                'action'      => $action,
                'model_type'  => class_basename($model),
                'model_id'    => $model->getKey(),
                'model_label' => static::resolveAuditLabel($model),
                'old_values'  => $old ?: null,
                'new_values'  => $new ?: null,
                'ip_address'  => $request?->ip(),
                'user_agent'  => $request?->userAgent(),
            ]);
        } catch (\Throwable) {
            // Never break the main operation due to audit failure
        }
    }

    private static function resolveAuditLabel($model): ?string
    {
        if (method_exists($model, 'getAuditLabel')) {
            return $model->getAuditLabel();
        }
        foreach (['name', 'title', 'email', 'first_name'] as $col) {
            if (!empty($model->$col)) {
                return (string) $model->$col;
            }
        }
        return null;
    }
}
