<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Morning Shift',
                'code' => 'MORN',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'break_minutes' => 60,
                'break_start' => '12:00:00',
                'break_end' => '13:00:00',
                'type' => 'day',
                // Convert array to JSON
                'applicable_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']),
                'hourly_rate_multiplier' => 1.00,
                'overtime_multiplier' => 1.50,
                'night_differential_multiplier' => 1.25,
                'grace_late_minutes' => 15,
                'grace_early_leave_minutes' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Evening Shift',
                'code' => 'EVEN',
                'start_time' => '14:00:00',
                'end_time' => '22:00:00',
                'break_minutes' => 45,
                'break_start' => '18:00:00',
                'break_end' => '18:45:00',
                'type' => 'day',
                'applicable_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
                'night_differential_multiplier' => 1.25,
                'hourly_rate_multiplier' => 1.00,
                'overtime_multiplier' => 1.50,
                'grace_late_minutes' => 15,
                'grace_early_leave_minutes' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Night Shift',
                'code' => 'NITE',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'break_minutes' => 60,
                'break_start' => '02:00:00',
                'break_end' => '03:00:00',
                'type' => 'night',
                'applicable_days' => json_encode(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday']),
                'night_differential_multiplier' => 1.35,
                'hourly_rate_multiplier' => 1.00,
                'overtime_multiplier' => 1.50,
                'grace_late_minutes' => 15,
                'grace_early_leave_minutes' => 15,
                'is_active' => true,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }
    }
}
