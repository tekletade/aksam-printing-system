<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions grouped by module
        $permissions = [
            // User Management
            'view users', 'create users', 'edit users', 'delete users',

            // Role Management
            'view roles', 'create roles', 'edit roles', 'delete roles',

            // Permission Management
            'view permissions', 'create permissions', 'edit permissions', 'delete permissions',

            // Printer Management
            'view printers', 'create printers', 'edit printers', 'delete printers', 'configure printers',

            // Printer Models
            'view printer-models', 'create printer-models', 'edit printer-models', 'delete printer-models',

            // Print Jobs
            'view print-jobs', 'create print-jobs', 'edit print-jobs', 'delete print-jobs',

            // Toner Levels
            'view toner-levels', 'create toner-levels', 'edit toner-levels', 'delete toner-levels',

            // Paper Inventory
            'view paper-inventory', 'create paper-inventory', 'edit paper-inventory', 'delete paper-inventory',

            // Customers
            'view customers', 'create customers', 'edit customers', 'delete customers',

            // Orders
            'view orders', 'create orders', 'edit orders', 'delete orders', 'approve orders', 'process orders',

            // Employees
            'view employees', 'create employees', 'edit employees', 'delete employees',

            // Attendance
            'view attendance', 'create attendance', 'edit attendance', 'delete attendance',

            // Leave Requests
            'view leave-requests', 'create leave-requests', 'edit leave-requests', 'delete leave-requests', 'approve leave-requests',

            // Payroll
            'view payroll', 'process payroll', 'approve payroll',

            // Inventory
            'view inventory', 'manage inventory',

            // Accounting
            'view accounting', 'manage accounting',

            // Reports
            'view reports', 'generate reports',

            // Dashboard
            'view dashboard',

            // Settings
            'manage settings',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $supervisor = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        $operator = Role::firstOrCreate(['name' => 'Operator', 'guard_name' => 'web']);
        $hrManager = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web']);
        $accountant = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
        $customer = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);

        // Assign permissions to Super Admin (all permissions)
        $superAdmin->givePermissionTo(Permission::all());

        // Assign permissions to Admin
        $admin->givePermissionTo([
            'view users', 'create users', 'edit users',
            'view roles', 'view permissions',
            'view printers', 'create printers', 'edit printers',
            'view printer-models', 'create printer-models', 'edit printer-models',
            'view print-jobs',
            'view toner-levels',
            'view paper-inventory',
            'view customers', 'create customers', 'edit customers',
            'view orders', 'edit orders', 'approve orders',
            'view employees', 'create employees', 'edit employees',
            'view attendance',
            'view leave-requests', 'approve leave-requests',
            'view payroll',
            'view inventory',
            'view accounting',
            'view reports', 'generate reports',
            'view dashboard',
        ]);

        // Assign permissions to Manager
        $manager->givePermissionTo([
            'view printers',
            'view print-jobs',
            'view toner-levels',
            'view paper-inventory',
            'view customers',
            'view orders', 'approve orders', 'process orders',
            'view employees',
            'view attendance',
            'view leave-requests', 'approve leave-requests',
            'view payroll',
            'view inventory',
            'view reports',
            'view dashboard',
        ]);

        // Assign permissions to Supervisor
        $supervisor->givePermissionTo([
            'view printers',
            'view print-jobs',
            'view toner-levels',
            'view paper-inventory',
            'view orders', 'process orders',
            'view attendance', 'create attendance',
            'view leave-requests', 'create leave-requests',
            'view dashboard',
        ]);

        // Assign permissions to Operator
        $operator->givePermissionTo([
            'view printers',
            'view print-jobs', 'create print-jobs',
            'view orders', 'process orders',
            'view dashboard',
        ]);

        // Assign permissions to HR Manager
        $hrManager->givePermissionTo([
            'view employees', 'create employees', 'edit employees',
            'view attendance', 'create attendance', 'edit attendance',
            'view leave-requests', 'create leave-requests', 'edit leave-requests', 'approve leave-requests',
            'view payroll', 'process payroll',
            'view reports',
            'view dashboard',
        ]);

        // Assign permissions to Accountant
        $accountant->givePermissionTo([
            'view payroll', 'process payroll',
            'view accounting', 'manage accounting',
            'view reports', 'generate reports',
            'view dashboard',
        ]);

        // Assign permissions to Customer
        $customer->givePermissionTo([
            'view orders', 'create orders',
            'view dashboard',
        ]);

        // Create Super Admin user if not exists
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@aksam.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdminUser->assignRole('Super Admin');

        // Create Admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@aksam.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('Admin');

        // Create Manager user
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@aksam.com'],
            [
                'name' => 'Manager User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $managerUser->assignRole('Manager');

        // Create Supervisor user
        $supervisorUser = User::firstOrCreate(
            ['email' => 'supervisor@aksam.com'],
            [
                'name' => 'Supervisor User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $supervisorUser->assignRole('Supervisor');

        // Create Operator user
        $operatorUser = User::firstOrCreate(
            ['email' => 'operator@aksam.com'],
            [
                'name' => 'Operator User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $operatorUser->assignRole('Operator');

        // Create HR Manager user
        $hrUser = User::firstOrCreate(
            ['email' => 'hr@aksam.com'],
            [
                'name' => 'HR Manager',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $hrUser->assignRole('HR Manager');

        // Create Accountant user
        $accountantUser = User::firstOrCreate(
            ['email' => 'accountant@aksam.com'],
            [
                'name' => 'Accountant',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $accountantUser->assignRole('Accountant');

        $this->command->info('Permissions and roles seeded successfully!');
    }
}
