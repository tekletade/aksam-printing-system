<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Printer Management
            'view printers',
            'create printers',
            'edit printers',
            'delete printers',
            'configure printers',

            // Revenue
            'view revenue',
            'manage pricing',

            // Orders
            'view orders',
            'create orders',
            'process orders',
            'approve orders',

            // Inventory
            'view inventory',
            'manage inventory',
            'configure alerts',

            // HR
            'view employees',
            'manage employees',
            'view attendance',
            'manage attendance',

            // Payroll
            'view payroll',
            'process payroll',
            'approve payroll',

            // Accounting
            'view accounting',
            'manage accounting',

            // Payment Verification
            'verify payments',
            'audit payments',

            // Reports
            'view reports',
            'generate reports',

            // Administration
            'manage users',
            'manage roles',
            'system configuration',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'view printers',
            'view revenue',
            'manage pricing',
            'view orders',
            'approve orders',
            'view inventory',
            'manage inventory',
            'view employees',
            'view attendance',
            'view payroll',
            'approve payroll',
            'view accounting',
            'verify payments',
            'view reports',
            'generate reports',
        ]);

        $supervisor = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        $supervisor->givePermissionTo([
            'view printers',
            'view revenue',
            'view orders',
            'process orders',
            'view inventory',
            'manage inventory',
            'view employees',
            'manage attendance',
        ]);

        $operator = Role::firstOrCreate(['name' => 'Operator', 'guard_name' => 'web']);
        $operator->givePermissionTo([
            'view printers',
            'view orders',
            'process orders',
        ]);

        $hrPersonnel = Role::firstOrCreate(['name' => 'HR Personnel', 'guard_name' => 'web']);
        $hrPersonnel->givePermissionTo([
            'view employees',
            'manage employees',
            'view attendance',
            'manage attendance',
            'view payroll',
            'process payroll',
        ]);

        $financePersonnel = Role::firstOrCreate(['name' => 'Finance/Accounting', 'guard_name' => 'web']);
        $financePersonnel->givePermissionTo([
            'view revenue',
            'manage pricing',
            'view accounting',
            'manage accounting',
            'verify payments',
            'view reports',
            'generate reports',
        ]);

        $customer = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);
        $customer->givePermissionTo([
            'view orders',
        ]);

        // Create default admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@aksam.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign role to admin
        if (!$admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }

        // Create demo users for each role (optional)
        $this->createDemoUsers();
    }

    private function createDemoUsers()
    {
        $demoUsers = [
            ['name' => 'Demo Manager', 'email' => 'manager@aksam.com', 'role' => 'Manager'],
            ['name' => 'Demo Supervisor', 'email' => 'supervisor@aksam.com', 'role' => 'Supervisor'],
            ['name' => 'Demo Operator', 'email' => 'operator@aksam.com', 'role' => 'Operator'],
            ['name' => 'Demo HR', 'email' => 'hr@aksam.com', 'role' => 'HR Personnel'],
            ['name' => 'Demo Finance', 'email' => 'finance@aksam.com', 'role' => 'Finance/Accounting'],
        ];

        foreach ($demoUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}
