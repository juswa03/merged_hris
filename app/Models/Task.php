<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_date',
        'priority',
        'status',
        'completed_at'
    ];

    protected $dates = ['due_date', 'completed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
