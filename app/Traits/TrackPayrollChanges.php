<?php

namespace App\Traits;

use App\Models\PayrollAudit;

trait TrackPayrollChanges
{
    protected static function bootTrackPayrollChanges()
    {
        static::created(function ($model) {
            PayrollAudit::logAction(
                $model->id,
                'created',
                $model->getAttributes(),
                'Payroll record created'
            );
        });

        static::updated(function ($model) {
            $changes = [];
            
            foreach ($model->getChanges() as $key => $newValue) {
                $oldValue = $model->getOriginal($key);
                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }

            if (!empty($changes)) {
                PayrollAudit::logAction(
                    $model->id,
                    'updated',
                    $changes,
                    'Payroll record updated'
                );
            }
        });

        static::deleted(function ($model) {
            PayrollAudit::logAction(
                $model->id,
                'deleted',
                $model->getAttributes(),
                'Payroll record deleted'
            );
        });
    }
}
