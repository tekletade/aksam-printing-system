<?php
// database/seeders/DemoDataSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Printer;
use App\Models\PrinterModel;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\User;
use App\Models\Shift;
use App\Models\TonerLevel;
use App\Models\PaperInventory;
use Faker\Factory as Faker;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Create Printers
        $printerModels = PrinterModel::all();

        $locations = ['Main Office', 'Production Floor', 'Design Department', 'Sales Office', 'Warehouse'];
        $departments = ['Production', 'Design', 'Sales', 'Administration', 'Management'];

        for ($i = 1; $i <= 10; $i++) {
            $model = $printerModels->random();
            $printer = Printer::create([
                'printer_model_id' => $model->id,
                'name' => $model->name . ' #' . $i,
                'ip_address' => '192.168.1.' . (100 + $i),
                'mac_address' => $faker->macAddress,
                'serial_number' => 'SN' . strtoupper(uniqid()),
                'status' => $faker->randomElement(['Ready', 'Printing', 'Ready', 'Ready', 'Idle']),
                'location' => $faker->randomElement($locations),
                'department' => $faker->randomElement($departments),
                'total_pages_count' => $faker->numberBetween(10000, 100000),
                'black_white_pages' => $faker->numberBetween(5000, 80000),
                'color_pages' => $faker->numberBetween(1000, 20000),
                'simplex_pages' => $faker->numberBetween(2000, 30000),
                'duplex_pages' => $faker->numberBetween(8000, 70000),
                'last_maintenance_date' => $faker->dateTimeBetween('-6 months', 'now'),
                'next_maintenance_date' => $faker->dateTimeBetween('now', '+3 months'),
                'is_monitoring_enabled' => true,
                'is_active' => true,
                'last_polled_at' => now(),
            ]);

            // Create Toner Levels
            TonerLevel::create([
                'printer_id' => $printer->id,
                'toner_color' => 'black',
                'current_level' => $faker->numberBetween(15, 95),
                'threshold_warning' => 15,
                'threshold_critical' => 5,
                'is_low' => $faker->boolean(20),
                'last_replaced_at' => $faker->dateTimeBetween('-3 months', 'now'),
            ]);

            if ($model->is_color) {
                foreach (['cyan', 'magenta', 'yellow'] as $color) {
                    TonerLevel::create([
                        'printer_id' => $printer->id,
                        'toner_color' => $color,
                        'current_level' => $faker->numberBetween(20, 90),
                        'threshold_warning' => 15,
                        'threshold_critical' => 5,
                        'is_low' => $faker->boolean(10),
                        'last_replaced_at' => $faker->dateTimeBetween('-3 months', 'now'),
                    ]);
                }
            }

            // Create Paper Inventory
            PaperInventory::create([
                'printer_id' => $printer->id,
                'tray_name' => 'Tray 1',
                'paper_size' => 'A4',
                'paper_type' => 'Plain',
                'current_sheets' => $faker->numberBetween(50, 500),
                'max_capacity' => 500,
                'threshold_reorder' => 100,
                'threshold_critical' => 50,
                'is_low' => $faker->boolean(30),
                'last_refilled_at' => $faker->dateTimeBetween('-1 week', 'now'),
            ]);

            if ($faker->boolean(50)) {
                PaperInventory::create([
                    'printer_id' => $printer->id,
                    'tray_name' => 'Tray 2',
                    'paper_size' => 'A3',
                    'paper_type' => 'Plain',
                    'current_sheets' => $faker->numberBetween(20, 200),
                    'max_capacity' => 250,
                    'threshold_reorder' => 50,
                    'threshold_critical' => 25,
                    'is_low' => $faker->boolean(20),
                    'last_refilled_at' => $faker->dateTimeBetween('-2 weeks', 'now'),
                ]);
            }
        }

        // Create Employees
        $shifts = Shift::all();

        $positions = [
            'Print Operator' => ['Print Operator', 'Senior Print Operator', 'Junior Print Operator'],
            'Supervisor' => ['Production Supervisor', 'Shift Supervisor'],
            'Manager' => ['Production Manager', 'Operations Manager', 'Sales Manager'],
            'Designer' => ['Graphic Designer', 'Senior Designer'],
            'Administration' => ['HR Officer', 'Accountant', 'Receptionist', 'Cleaner'],
        ];

        for ($i = 1; $i <= 25; $i++) {
            $department = $faker->randomElement($departments);
            $positionCategory = array_rand($positions);
            $position = $faker->randomElement($positions[$positionCategory]);

            $firstName = $faker->firstName;
            $fatherName = $faker->firstName;
            $grandfatherName = $faker->firstName;

            $employee = Employee::create([
                'employee_id' => 'EMP' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'first_name' => $firstName,
                'father_name' => $fatherName,
                'grandfather_name' => $grandfatherName,
                'last_name' => $faker->lastName,
                'date_of_birth' => $faker->dateTimeBetween('-50 years', '-20 years'),
                'gender' => $faker->randomElement(['male', 'female']),
                'phone_primary' => $faker->phoneNumber,
                'phone_secondary' => $faker->optional(0.3)->phoneNumber,
                'email' => strtolower($firstName . '.' . $fatherName . '@example.com'),
                'emergency_contact_name' => $faker->name,
                'emergency_contact_phone' => $faker->phoneNumber,
                'city' => 'Addis Ababa',
                'sub_city' => $faker->randomElement(['Bole', 'Yeka', 'Kirkos', 'Lideta', 'Arada']),
                'woreda' => $faker->numberBetween(1, 20),
                'department' => $department,
                'position' => $position,
                'job_title' => $position,
                'employment_type' => $faker->randomElement(['permanent', 'permanent', 'contract', 'probation']),
                'hire_date' => $faker->dateTimeBetween('-10 years', '-1 month'),
                'pension_number' => 'PEN' . $faker->numerify('########'),
                'tin_number' => 'TIN' . $faker->numerify('########'),
                'bank_name' => $faker->randomElement(['Commercial Bank of Ethiopia', 'Dashen Bank', 'Awash Bank', 'Bank of Abyssinia']),
                'bank_account_number' => $faker->numerify('1000##########'),
                'status' => 'active',
            ]);

            // Assign shift
            $shift = $shifts->random();
            $employee->shifts()->attach($shift->id, [
                'effective_from' => $employee->hire_date,
                'is_permanent' => true,
            ]);
        }
    }
}
