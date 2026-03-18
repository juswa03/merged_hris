<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'updated_by',
        'update_type',
        'description',
        'is_internal_note',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_internal_note' => 'boolean'
    ];

    // Relationships
    public function complaint()
    {
        return $this->belongsTo(EmployeeComplaint::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}