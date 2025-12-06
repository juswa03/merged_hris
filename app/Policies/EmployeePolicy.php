<?php

// app/Policies/EmployeePolicy.php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    /**
     * Determine if the user can view any employees.
     */
    public function viewAny(User $user): bool
    {
        $allowedRoles = ['Super Admin', 'Admin', 'HR', 'hr'];
        return in_array($user->role->name ?? '', $allowedRoles);
    }

    /**
     * Determine if the user can view the employee.
     */
    public function view(User $user, Employee $employee): bool
    {
        // Super Admin, Admin and HR can view all employees
        $adminRoles = ['Super Admin', 'Admin', 'HR', 'hr'];
        if (in_array($user->role->name ?? '', $adminRoles)) {
            return true;
        }
        
        // Employees can only view their own record
        $employeeRoles = ['Employee', 'employee'];
        return in_array($user->role->name ?? '', $employeeRoles) && 
               $user->id === $employee->user_id;
    }

    /**
     * Determine if the user can create employees.
     */
    public function create(User $user): bool
    {
        $allowedRoles = ['Super Admin', 'Admin', 'HR', 'hr'];
        return in_array($user->role->name ?? '', $allowedRoles);
    }

    /**
     * Determine if the user can update the employee.
     */
    public function update(User $user, Employee $employee): bool
    {
        // Super Admin, Admin and HR can update all employees
        $adminRoles = ['Super Admin', 'Admin', 'HR', 'hr'];
        if (in_array($user->role->name ?? '', $adminRoles)) {
            return true;
        }
        
        // Employees can only update their own basic info
        $employeeRoles = ['Employee', 'employee'];
        return in_array($user->role->name ?? '', $employeeRoles) && 
               $user->id === $employee->user_id;
    }

    /**
     * Determine if the user can delete the employee.
     */
    public function delete(User $user, Employee $employee): bool
    {
        // Only Super Admin and Admin can delete employees
        $allowedRoles = ['Super Admin', 'Admin'];
        return in_array($user->role->name ?? '', $allowedRoles);
    }

    /**
     * Determine if the user can restore the employee.
     */
    public function restore(User $user, Employee $employee): bool
    {
        return $user->role->name === 'Super Admin';
    }

    /**
     * Determine if the user can permanently delete the employee.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->role->name === 'Super Admin';
    }
}