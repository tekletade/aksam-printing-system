<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            PrinterModelSeeder::class,
            ShiftSeeder::class,
            LeaveTypeSeeder::class,
            TaxBracketSeeder::class,
            ChartOfAccountsSeeder::class,
            // DemoDataSeeder::class, // Uncomment when ready
        ]);
    }
}
