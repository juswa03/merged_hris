<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'tbl_employee';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'birthdate',
        'contact_number',
        'address',
        'photo_url',
        'hire_date',
        'date_resign',
        'rfid_code',
        'time_stamp_id',
        'civil_status',
        'department_id',
        'position_id',
        'employment_type_id',
        'job_status_id',
        'biometric_user_id',
        'basic_salary',
        'salary_grade',
        'salary_step',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'hire_date' => 'date',
        'date_resign' => 'date',
        'basic_salary' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function (Employee $employee) {
            // Ensure basic salary matches the grade matrix whenever a graded employee is saved.
            if ($employee->salary_grade && $employee->salary_step) {
                $salary = SalaryGrade::getSalary($employee->salary_grade, $employee->salary_step);

                if ($salary !== null) {
                    $employee->basic_salary = $salary;
                }
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class);
    }
public function user()
{
    return $this->belongsTo(User::class);
}


    public function jobStatus()
    {
        return $this->belongsTo(JobStatus::class);
    }




    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function dtrEntries()
    {
        return $this->hasMany(DtrEntry::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }


    public function fingerprintTemplate()
    {
        return $this->hasOne(FingerprintTemplate::class, 'employee_id');
    }

    // Add this accessor to check if employee has fingerprint enrolled
    public function getIsFingerprintEnrolledAttribute()
    {
        return $this->fingerprintTemplate !== null;
    }

    // Add this accessor to get fingerprint enrollment date
    public function getFingerprintEnrollmentDateAttribute()
    {
        return $this->fingerprintTemplate ? $this->fingerprintTemplate->created_at : null;
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }
    public function deductions()
    {
        return $this->belongsToMany(Deduction::class, 'tbl_employee_deductions', 'employee_id', 'deduction_id')
                    ->using(EmployeeDeduction::class)
                    ->withPivot('custom_amount', 'effective_from', 'effective_to')
                    ->withTimestamps();
    }
    // public function deductions()
    // {
    //     return $this->belongsToMany(Deduction::class, 'tbl_employee_deductions')
    //         ->withPivot('custom_amount', 'effective_from', 'effective_to')
    //         ->withTimestamps();
    // }
    public function allowances()
    {
        return $this->belongsToMany(Allowance::class, 'tbl_employee_allowances', 'employee_id', 'allowance_id')
                    ->using(EmployeeAllowance::class)
                    ->withPivot('effective_from', 'effective_to')
                    ->withTimestamps();
    }
    
    // public function allowances()
    // {
    //     return $this->belongsToMany(Allowance::class, 'tbl_employee_allowances')
    //         ->withPivot('effective_from', 'effective_to')
    //         ->withTimestamps();
    // }

    public function activeDeductions()
    {
        return $this->deductions()
                    ->wherePivot('effective_from', '<=', now())
                    ->where(function($query) {
                        $query->whereNull('effective_to')
                              ->orWhere('effective_to', '>=', now());
                    });
    }

    public function activeAllowances()
    {
        return $this->allowances()
                    ->wherePivot('effective_from', '<=', now())
                    ->where(function($query) {
                        $query->whereNull('effective_to')
                              ->orWhere('effective_to', '>=', now());
                    });
    }
    public function latestDtrEntry()
{
    return $this->hasOne(DtrEntry::class, 'employee_id')->latestOfMany();
}

    /**
     * Get the current salary from assigned salary grade
     */
    public function getSalaryFromGrade()
    {
        if (!$this->salary_grade || !$this->salary_step) {
            return null;
        }

        return SalaryGrade::getSalary($this->salary_grade, $this->salary_step);
    }

    /**
     * Sync the stored basic salary with the current salary grade amount.
     */
    public function syncSalaryFromGrade()
    {
        $salary = $this->getSalaryFromGrade();

        if ($salary === null) {
            return null;
        }

        if ($this->basic_salary != $salary) {
            $this->basic_salary = $salary;
            $this->save();
        }

        return $salary;
    }

    /**
     * Update basic_salary based on assigned salary grade
     */
    public function updateSalaryFromGrade()
    {
        $salary = $this->getSalaryFromGrade();

        if ($salary !== null) {
            $this->basic_salary = $salary;
            $this->save();
        }

        return $this;
    }

    /**
     * Get salary grade display name (e.g., "SG-15 Step 3")
     */
    public function getSalaryGradeDisplayAttribute()
    {
        if (!$this->salary_grade || !$this->salary_step) {
            return 'Not assigned';
        }

        return "SG-{$this->salary_grade} Step {$this->salary_step}";
    }

    /**
     * Check if employee's salary matches their salary grade
     */
    public function isSalaryMatchingGrade()
    {
        if (!$this->salary_grade || !$this->salary_step) {
            return true; // No grade assigned, so no mismatch
        }

        $expectedSalary = $this->getSalaryFromGrade();

        if ($expectedSalary === null) {
            return false; // Grade assigned but no salary found in schedule
        }

        return abs($this->basic_salary - $expectedSalary) < 0.01; // Allow for floating point precision
    }


}