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

    // Always eager-load role to prevent N+1 queries on permission checks across the app
    protected $with = ['role'];

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
        try {
            $roleName = $this->role?->name ?? '';
            return in_array($roleName, ['Super Admin', 'Admin']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('isAdmin check failed: ' . $e->getMessage(), ['user_id' => $this->id]);
            return false;
        }
    }

    /**
     * Check if user is HR
     */
    public function isHR(): bool
    {
        try {
            $roleName = $this->role?->name ?? '';
            return in_array($roleName, ['HR', 'hr', 'HR Staff']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('isHR check failed: ' . $e->getMessage(), ['user_id' => $this->id]);
            return false;
        }
    }

    /**
     * Check if user is Employee
     */
    public function isEmployee(): bool
    {
        try {
            $roleName = $this->role?->name ?? '';
            return in_array($roleName, ['Employee', 'employee']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('isEmployee check failed: ' . $e->getMessage(), ['user_id' => $this->id]);
            return false;
        }
    }

    /**
     * Employee relationship
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}

