<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceService
{
    /**
     * Process check-in
     */
    public function checkIn(Employee $employee, string $method, array $metadata = [])
    {
        $today = Carbon::today();
        $now = Carbon::now();

        // Check if already checked in today
        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existing) {
            if ($existing->check_in) {
                return [
                    'success' => false,
                    'message' => 'Already checked in today at ' .
                        $existing->check_in->format('H:i'),
                    'attendance' => $existing
                ];
            }
        }

        // Get employee's shift for today
        $shift = $this->getEmployeeShift($employee, $today);

        // Prepare attendance data
        $attendanceData = [
            'employee_id' => $employee->id,
            'attendance_date' => $today,
            'check_in' => $now,
            'check_in_method' => $method,
            'shift_id' => $shift->id ?? null,
            'status' => 'present',
            'check_in_ip' => request()->ip(),
            'check_in_location' => isset($metadata['latitude']) ?
                "{$metadata['latitude']},{$metadata['longitude']}" : null,
            'check_in_lat' => $metadata['latitude'] ?? null,
            'check_in_lng' => $metadata['longitude'] ?? null,
            'qr_code_used' => $metadata['qr_code'] ?? null,
            'device_id' => $metadata['device_id'] ?? null,
        ];

        // Check if late
        if ($shift) {
            $shiftStart = Carbon::parse($shift->start_time);
            $checkInTime = Carbon::parse($now->format('H:i:s'));

            $lateMinutes = $shiftStart->diffInMinutes($checkInTime, false);

            if ($lateMinutes > ($shift->grace_late_minutes ?? 15)) {
                $attendanceData['is_late'] = true;
                $attendanceData['late_minutes'] = $lateMinutes;
                $attendanceData['status'] = 'late';
            }
        }

        // Create or update attendance
        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $today,
            ],
            $attendanceData
        );

        // Log for security audit
        $this->logAttendanceAudit($attendance, 'check_in', $metadata);

        // Send notification to manager if late
        if ($attendance->is_late) {
            $this->notifyManager($employee, 'late_check_in', [
                'minutes' => $attendance->late_minutes,
                'time' => $now->format('H:i')
            ]);
        }

        return [
            'success' => true,
            'message' => 'Check-in successful',
            'attendance' => $attendance,
            'shift' => $shift,
            'is_late' => $attendance->is_late,
            'late_minutes' => $attendance->late_minutes ?? 0,
        ];
    }

    /**
     * Process check-out
     */
    public function checkOut(Employee $employee, string $method, array $metadata = [])
    {
        $today = Carbon::today();
        $now = Carbon::now();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance) {
            return [
                'success' => false,
                'message' => 'No check-in record found for today'
            ];
        }

        if ($attendance->check_out) {
            return [
                'success' => false,
                'message' => 'Already checked out today at ' .
                    $attendance->check_out->format('H:i')
            ];
        }

        // Calculate total hours
        $checkIn = Carbon::parse($attendance->check_in);
        $totalMinutes = $checkIn->diffInMinutes($now);

        // Subtract break time if any
        $breakMinutes = $attendance->break_minutes ?? 0;
        $workingMinutes = $totalMinutes - $breakMinutes;

        $attendance->update([
            'check_out' => $now,
            'check_out_method' => $method,
            'check_out_location' => isset($metadata['latitude']) ?
                "{$metadata['latitude']},{$metadata['longitude']}" : null,
            'check_out_lat' => $metadata['latitude'] ?? null,
            'check_out_lng' => $metadata['longitude'] ?? null,
            'total_minutes' => $workingMinutes,
            'total_hours' => round($workingMinutes / 60, 2),
        ]);

        // Calculate overtime
        $this->calculateOvertime($attendance);

        // Log for security audit
        $this->logAttendanceAudit($attendance, 'check_out', $metadata);

        return [
            'success' => true,
            'message' => 'Check-out successful',
            'attendance' => $attendance,
            'total_hours' => $attendance->total_hours,
            'overtime_hours' => $attendance->overtime_hours ?? 0,
        ];
    }

    /**
     * Get employee's shift for a given date
     */
    private function getEmployeeShift(Employee $employee, Carbon $date)
    {
        // Check for special shift assignment
        $assignedShift = $employee->shifts()
            ->wherePivot('effective_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->wherePivotNull('effective_to')
                    ->orWherePivot('effective_to', '>=', $date);
            })
            ->first();

        if ($assignedShift) {
            return $assignedShift;
        }

        // Get default shift based on day of week
        $dayOfWeek = strtolower($date->format('l'));

        return Shift::whereJsonContains('applicable_days', $dayOfWeek)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Calculate overtime
     */
    private function calculateOvertime(Attendance $attendance)
    {
        if (!$attendance->shift) {
            return;
        }

        $shiftHours = $attendance->shift->getDurationInHours();

        if ($attendance->total_hours > $shiftHours) {
            $overtime = $attendance->total_hours - $shiftHours;

            // Different overtime rates based on day type
            $isWeekend = $attendance->attendance_date->isWeekend();
            $isHoliday = $this->isHoliday($attendance->attendance_date);

            $rate = 1.5; // Normal overtime
            if ($isWeekend) $rate = 2.0;
            if ($isHoliday) $rate = 2.5;

            $attendance->update([
                'is_overtime' => true,
                'overtime_hours' => round($overtime, 2),
                'overtime_rate' => $rate,
            ]);
        }
    }

    /**
     * Check if date is holiday
     */
    private function isHoliday(Carbon $date)
    {
        // Implement holiday checking logic
        // You can have a holidays table
        return false;
    }

    /**
     * Get today's summary for a branch
     */
    public function getTodaySummary($branchId = null)
    {
        $today = Carbon::today();

        $query = Employee::where('status', 'active');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $totalEmployees = $query->count();

        $attendances = Attendance::with('employee')
            ->whereDate('attendance_date', $today);

        if ($branchId) {
            $attendances->whereHas('employee', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $attendances = $attendances->get();

        $present = $attendances->where('status', 'present')->count();
        $late = $attendances->where('is_late', true)->count();
        $checkedOut = $attendances->whereNotNull('check_out')->count();

        $presentWithCheckOut = $attendances->filter(function ($a) {
            return $a->status == 'present' && $a->check_out;
        })->count();

        // Get employees not checked in
        $checkedInIds = $attendances->pluck('employee_id');

        $notCheckedIn = Employee::where('status', 'active')
            ->whereNotIn('id', $checkedInIds)
            ->when($branchId, function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->get();

        // Calculate on-time percentage
        $onTime = $present - $late;

        return [
            'date' => $today->format('Y-m-d'),
            'total_employees' => $totalEmployees,
            'present' => $present,
            'absent' => $totalEmployees - $present,
            'late' => $late,
            'checked_out' => $checkedOut,
            'still_working' => $presentWithCheckOut,
            'on_time' => $onTime,
            'attendance_rate' => $totalEmployees > 0 ?
                round(($present / $totalEmployees) * 100, 1) : 0,
            'on_time_rate' => $present > 0 ?
                round(($onTime / $present) * 100, 1) : 0,
            'not_checked_in' => $notCheckedIn->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->full_name,
                    'department' => $emp->department,
                    'phone' => $emp->phone_primary,
                ];
            }),
        ];
    }

    /**
     * Auto-detect absent employees
     */
    public function detectAbsentees($branchId = null)
    {
        $today = Carbon::today();
        $cutoffTime = Carbon::today()->setTime(10, 0); // 10:00 AM cutoff

        // Only run after cutoff time
        if (Carbon::now()->lt($cutoffTime)) {
            return;
        }

        $query = Employee::where('status', 'active');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $employees = $query->get();

        $checkedInIds = Attendance::whereDate('attendance_date', $today)
            ->pluck('employee_id');

        foreach ($employees as $employee) {
            if (!$checkedInIds->contains($employee->id)) {
                // Check if on approved leave
                $onLeave = $employee->leaveRequests()
                    ->where('status', 'approved')
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->exists();

                if (!$onLeave) {
                    Attendance::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'attendance_date' => $today,
                        ],
                        [
                            'status' => 'absent',
                            'is_absent' => true,
                        ]
                    );

                    // Notify manager
                    $this->notifyManager($employee, 'absent');
                }
            }
        }
    }

    /**
     * Log attendance for security audit
     */
    private function logAttendanceAudit($attendance, $action, $metadata)
    {
        Log::channel('attendance')->info('Attendance ' . $action, [
            'attendance_id' => $attendance->id,
            'employee_id' => $attendance->employee_id,
            'employee_name' => $attendance->employee->full_name,
            'action' => $action,
            'timestamp' => Carbon::now(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Notify manager
     */
    private function notifyManager($employee, $type, $data = [])
    {
        if (!$employee->reportsTo || !$employee->reportsTo->user) {
            return;
        }

        $manager = $employee->reportsTo->user;

        $notification = match($type) {
            'late_check_in' => [
                'title' => 'Employee Late Check-in',
                'body' => "{$employee->full_name} checked in {$data['minutes']} minutes late at {$data['time']}",
                'color' => 'warning',
            ],
            'absent' => [
                'title' => 'Employee Absent',
                'body' => "{$employee->full_name} is absent today without leave request",
                'color' => 'danger',
            ],
            'check_in' => [
                'title' => 'Employee Check-in',
                'body' => "{$employee->full_name} checked in",
                'color' => 'success',
            ],
            default => [
                'title' => 'Attendance Update',
                'body' => "{$employee->full_name} has an attendance update",
                'color' => 'info',
            ],
        };

        \Filament\Notifications\Notification::make()
            ->title($notification['title'])
            ->body($notification['body'])
            ->{$notification['color']}()
            ->sendToDatabase($manager);
    }
}
