<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;
use Faker\Factory as Faker;

class DemoCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Create 20 demo customers
        for ($i = 1; $i <= 20; $i++) {
            $type = $faker->randomElement(['individual', 'company', 'individual', 'individual']);

            Customer::create([
                'customer_code' => 'CUST' . date('Y') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'name' => $type === 'company' ? $faker->company : $faker->name,
                'type' => $type,
                'company_name' => $type === 'company' ? $faker->company : null,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'alternate_phone' => $faker->optional(0.3)->phoneNumber,
                'tin_number' => $type === 'company' ? $faker->numerify('########') : null,
                'address' => $faker->address,
                'city' => 'Addis Ababa',
                'sub_city' => $faker->randomElement(['Bole', 'Yeka', 'Kirkos', 'Lideta', 'Arada']),
                'credit_limit' => $faker->randomFloat(2, 0, 50000),
                'status' => $faker->randomElement(['active', 'active', 'active', 'inactive']),
            ]);
        }
    }
}
