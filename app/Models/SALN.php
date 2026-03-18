<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SALN extends Model
{
    use HasFactory;
    protected $table = 'salns';
    protected $fillable = [
        'user_id',
        'status',
        'last_action_by',
        'last_action_at'
    ];

    protected $casts = [
        'last_action_at' => 'datetime',
    ];

    // The employee who owns this SALN
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // The HR/Admin who last verified or flagged it
    public function lastActionBy()
    {
        return $this->belongsTo(User::class, 'last_action_by');
    }
}
