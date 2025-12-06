<?php

// app/Models/FingerprintTemplate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class FingerprintTemplate extends Model
{
    use HasFactory;

    protected $table = 'tbl_fingerprint_templates';

    protected $fillable = [
        'employee_id',
        'template',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'template', // Never expose raw fingerprint template
    ];

    protected $appends = [
        'enrollment_date',
        'enrollment_age_days'
    ];

    /**
     * Relationships
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Accessors
     */
    public function getEnrollmentDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('M d, Y') : null;
    }

    public function getEnrollmentAgeDaysAttribute()
    {
        return $this->created_at ? $this->created_at->diffInDays(now()) : 0;
    }

    /**
     * Mutators
     */
    public function setTemplateAttribute($value)
    {
        // Encrypt fingerprint template data for security
        $this->attributes['template'] = Crypt::encrypt($value);
    }

    public function getTemplateAttribute($value)
    {
        // This should only be used for matching, never exposed to frontend
        try {
            return $value ? Crypt::decrypt($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Scopes
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeRecentEnrollments($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeWithEmployeeDetails($query)
    {
        return $query->with(['employee' => function($q) {
            $q->select('id', 'first_name', 'last_name', 'employee_id', 'department_id')
              ->with(['department:id,name']);
        }]);
    }

    /**
     * Custom Methods
     */
    public function getDecryptedTemplate()
    {
        // Only use this method for biometric matching purposes
        // Never expose this data to the frontend
        return $this->template;
    }

    public function isValidTemplate()
    {
        try {
            $template = $this->getDecryptedTemplate();
            return !empty($template) && strlen($template) > 100; // Basic validation
        } catch (\Exception $e) {
            return false;
        }
    }
}

/*
 * Add these methods to your existing Employee model (app/Models/Employee.php)
 */

/*
// Add this relationship method to the Employee model:
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
*/