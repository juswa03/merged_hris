<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'type'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'meeting' => 'fas fa-users',
            'training' => 'fas fa-chalkboard-teacher',
            'holiday' => 'fas fa-umbrella-beach',
            default => 'fas fa-calendar'
        };
    }

    public function getIconBgClassAttribute(): string
    {
        return match($this->type) {
            'meeting' => 'bg-blue-100 text-blue-600',
            'training' => 'bg-green-100 text-green-600',
            'holiday' => 'bg-yellow-100 text-yellow-600',
            default => 'bg-purple-100 text-purple-600'
        };
    }
}