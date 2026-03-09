<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    /**
     * Specify the table name since it doesn't follow Laravel's plural convention
     */
    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'shift_id',
        'attendance_date',
        'check_in',
        'check_out',
        'check_in_method',
        'check_out_method',
        'check_in_location',
        'check_out_location',
        'total_minutes',
        'total_hours',
        'regular_hours',
        'overtime_hours',
        'night_differential_hours',
        'is_late',
        'late_minutes',
        'is_early_leave',
        'early_leave_minutes',
        'is_overtime',
        'is_absent',
        'is_holiday',
        'is_weekend',
        'break_start',
        'break_end',
        'break_minutes',
        'status',
        'notes',
        'metadata',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'verified_at' => 'datetime',
        'is_late' => 'boolean',
        'is_early_leave' => 'boolean',
        'is_overtime' => 'boolean',
        'is_absent' => 'boolean',
        'is_holiday' => 'boolean',
        'is_weekend' => 'boolean',
        'total_minutes' => 'integer',
        'total_hours' => 'float',
        'regular_hours' => 'float',
        'overtime_hours' => 'float',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
        'break_minutes' => 'integer',
        'metadata' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Calculate total hours from check_in and check_out
     */
    public function calculateTotalHours(): void
    {
        if ($this->check_in && $this->check_out) {
            $minutes = $this->check_in->diffInMinutes($this->check_out);
            $this->total_minutes = $minutes - ($this->break_minutes ?? 0);
            $this->total_hours = round($this->total_minutes / 60, 2);
        }
    }

    /**
     * Check if employee was late
     */
    public function checkIfLate(Shift $shift): void
    {
        if ($this->check_in && $shift) {
            $shiftStart = Carbon::parse($shift->start_time);
            $actualCheckIn = Carbon::parse($this->check_in->format('H:i:s'));

            $lateMinutes = $shiftStart->diffInMinutes($actualCheckIn, false);

            if ($lateMinutes > ($shift->grace_late_minutes ?? 15)) {
                $this->is_late = true;
                $this->late_minutes = $lateMinutes;
            }
        }
    }
}
