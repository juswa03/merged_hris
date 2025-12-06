<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollAudit extends Model
{
    use HasFactory;

    protected $table = 'tbl_payroll_audits';

    protected $fillable = [
        'payroll_id',
        'user_id',
        'action',
        'changes',
        'reason',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function logAction($payrollId, $action, $changes = [], $reason = null)
    {
        return self::create([
            'payroll_id' => $payrollId,
            'user_id' => auth()->id() ?? null,
            'action' => $action,
            'changes' => $changes,
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
