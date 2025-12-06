<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\SalaryGrade;

class EmployeeObserver
{
    /**
     * Handle the Employee "creating" event.
     * Automatically assign basic salary when salary grade and step are set.
     */
    public function creating(Employee $employee): void
    {
        $this->autoAssignSalary($employee);
    }

    /**
     * Handle the Employee "updating" event.
     * Automatically update basic salary when salary grade or step changes.
     */
    public function updating(Employee $employee): void
    {
        // Check if salary_grade or salary_step has changed
        if ($employee->isDirty('salary_grade') || $employee->isDirty('salary_step')) {
            $this->autoAssignSalary($employee);
        }
    }

    /**
     * Automatically assign basic salary based on salary grade and step
     */
    protected function autoAssignSalary(Employee $employee): void
    {
        // Only auto-assign if both grade and step are set
        if ($employee->salary_grade && $employee->salary_step) {
            $salary = SalaryGrade::getSalary($employee->salary_grade, $employee->salary_step);

            if ($salary !== null) {
                $employee->basic_salary = $salary;
            }
        }
    }

    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        //
    }
}
