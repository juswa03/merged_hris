<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tbl_users';

    protected $fillable = [
        'email',
        'password',
        'status',
        'last_logged_at',
        'role_id',
        'time_stamp_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_logged_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getRoleNameAttribute()
    {
        return $this->role->name ?? null;
    }

        /**
     * Check if user has a specific role by name
     */
    public function hasRole($roleName): bool
    {
        return $this->role->name === $roleName;
    }

    /**
     * Check if user is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Check if user is Admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role->name ?? '', ['Super Admin', 'Admin']);
    }

    /**
     * Check if user is HR
     */
    public function isHR(): bool
    {
        return $this->hasRole('HR') || $this->hasRole('hr');
    }

    /**
     * Check if user is Employee
     */
    public function isEmployee(): bool
    {
        return $this->hasRole('Employee') || $this->hasRole('employee');
    }

    /**
     * Employee relationship
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}

