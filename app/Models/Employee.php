<?php
// app/Models/Employee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_id',
        'first_name',
        'father_name',
        'grandfather_name',
        'last_name',
        'date_of_birth',
        'gender',
        'nationality',
        'marital_status',
        'phone_primary',
        'phone_secondary',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'city',
        'sub_city',
        'woreda',
        'kebele',
        'house_number',
        'department',
        'position',
        'job_title',
        'employment_type',
        'hire_date',
        'probation_end_date',
        'contract_end_date',
        'termination_date',
        'termination_reason',
        'pension_number',
        'tin_number',
        'passport_number',
        'kebele_id_number',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'status',
        'notes',
        'metadata',
        'reports_to',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'probation_end_date' => 'date',
        'contract_end_date' => 'date',
        'termination_date' => 'date',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportsTo()
    {
        return $this->belongsTo(Employee::class, 'reports_to');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'reports_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function salaries()
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    public function currentSalary()
    {
        return $this->hasOne(EmployeeSalary::class)
            ->where('is_active', true)
            ->where('effective_from', '<=', now())
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            })
            ->latest('effective_from');
    }

    public function payrollRuns()
    {
        return $this->hasMany(PayrollRun::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'employee_shifts')
            ->withPivot('effective_from', 'effective_to', 'is_permanent')
            ->withTimestamps();
    }

    public function currentShift()
    {
        return $this->shifts()
            ->wherePivot('effective_from', '<=', now())
            ->where(function ($query) {
                $query->wherePivotNull('effective_to')
                    ->orWherePivot('effective_to', '>=', now());
            })
            ->latest('employee_shifts.effective_from');
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->father_name} {$this->grandfather_name} {$this->last_name}");
    }

    public function getShortNameAttribute()
    {
        return "{$this->first_name} {$this->father_name}";
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth?->age;
    }

    public function getYearsOfServiceAttribute()
    {
        return $this->hire_date?->diffInYears(now());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }
}
