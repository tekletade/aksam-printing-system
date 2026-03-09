<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'code' => 'ANNUAL',
                'max_days_per_year' => 20,
                'max_consecutive_days' => 15,
                'minimum_notice_days' => 7,
                'is_paid' => true,
                'requires_approval' => true,
                'carry_forward' => true,
                'carry_forward_limit' => 5,
                'applicable_gender' => json_encode(['male', 'female']),
                'applicable_employment_types' => json_encode(['permanent', 'contract']),
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SICK',
                'max_days_per_year' => 15,
                'max_consecutive_days' => 5,
                'minimum_notice_days' => 0,
                'is_paid' => true,
                'requires_approval' => true,
                'requires_document' => true,
                'carry_forward' => false,
                'applicable_gender' => json_encode(['male', 'female']),
                'applicable_employment_types' => json_encode(['permanent', 'contract', 'probation']),
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'MAT',
                'max_days_per_year' => 90,
                'max_consecutive_days' => 90,
                'minimum_notice_days' => 30,
                'is_paid' => true,
                'requires_approval' => true,
                'requires_document' => true,
                'carry_forward' => false,
                'applicable_gender' => json_encode(['female']),
                'applicable_employment_types' => json_encode(['permanent', 'contract']),
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PAT',
                'max_days_per_year' => 5,
                'max_consecutive_days' => 5,
                'minimum_notice_days' => 1,
                'is_paid' => true,
                'requires_approval' => true,
                'carry_forward' => false,
                'applicable_gender' => json_encode(['male']),
                'applicable_employment_types' => json_encode(['permanent', 'contract']),
            ],
            [
                'name' => 'Unpaid Leave',
                'code' => 'UNPAID',
                'max_days_per_year' => 30,
                'max_consecutive_days' => 30,
                'minimum_notice_days' => 3,
                'is_paid' => false,
                'requires_approval' => true,
                'carry_forward' => false,
                'applicable_gender' => json_encode(['male', 'female']),
                'applicable_employment_types' => json_encode(['permanent', 'contract', 'probation', 'temporary']),
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::create($type);
        }
    }
}
