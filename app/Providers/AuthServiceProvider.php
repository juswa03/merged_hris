<?php

// app/Providers/AuthServiceProvider.php

namespace App\Providers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\DtrEntry;
use App\Policies\EmployeePolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\PayrollPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Employee::class => EmployeePolicy::class,
        DtrEntry::class => DtrEntry::class,
        //Department::class => DepartmentPolicy::class,
        //Attendance::class => AttendancePolicy::class,
        //Payroll::class => PayrollPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access_admin', function ($user) {
            // Allow Super Admins always
            if ($user->role_name === 'Super Admin') {
                return true;
            }


            // // Otherwise, apply normal access rules
            // return $user->hasPermission('access_admin');
        });
    }
}