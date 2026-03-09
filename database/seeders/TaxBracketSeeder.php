<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxBracket;

class TaxBracketSeeder extends Seeder
{
    public function run(): void
    {
        // Ethiopian Income Tax Brackets (Monthly)
        $taxBrackets = [
            [
                'name' => 'Income Tax - Tier 1',
                'type' => 'income_tax',
                'min_amount' => 0,
                'max_amount' => 600,
                'rate' => 0,
                'fixed_amount' => 0,
                'excess_rate' => 0,
                'effective_from' => '2023-01-01',
                'is_active' => true,
            ],
            [
                'name' => 'Income Tax - Tier 2',
                'type' => 'income_tax',
                'min_amount' => 601,
                'max_amount' => 1650,
                'rate' => 10,
                'fixed_amount' => 60,
                'excess_rate' => 10,
                'effective_from' => '2023-01-01',
                'is_active' => true,
            ],
            [
                'name' => 'Income Tax - Tier 3',
                'type' => 'income_tax',
                'min_amount' => 1651,
                'max_amount' => 3200,
                'rate' => 15,
                'fixed_amount' => 142.5,
                'excess_rate' => 15,
                'effective_from' => '2023-01-01',
                'is_active' => true,
            ],
            [
                'name' => 'Income Tax - Tier 4',
                'type' => 'income_tax',
                'min_amount' => 3201,
                'max_amount' => 5250,
                'rate' => 20,
                'fixed_amount' => 302.5,
                'excess_rate' => 20,
                'effective_from' => '2023-01-01',
                'is_active' => true,
            ],
            [
                'name' => 'Income Tax - Tier 5',
                'type' => 'income_tax',
                'min_amount' => 5251,
                'max_amount' => 7800,
                'rate' => 25,
                'fixed_amount' => 565,
                'excess_rate' => 25,
                'effective_from' => '2023-01-01',
                'is_active' => true,
            ],
            [
                'name' => 'Income Tax - Tier 6',
                'type' => 'income_tax',
                'min_amount' => 7801,
                'max_amount' => 10900,
                'rate' => 30,
                'fixed_amount' => 955,
                'excess_rate' => 30,
                'effective_from' => '2023-01-01',
                'is_active' => true,
            ],
            [
                'name' => 'Income Tax - Tier 7',
                'type' => 'income_tax',
                'min_amount' => 10901,
                'max_amount' => null,
                'rate' => 35,
                'fixed_amount' => 1500,
                'excess_rate' => 35,
                'effective_from' => '2023-01-01',
                'is_active' => true,
            ],
            [
                'name' => 'Pension - Employee',
                'type' => 'pension_employee',
                'rate' => 7,
                'effective_from' => '2023-01-01',
                'is_active' => true,
            ],
            [
                'name' => 'Pension - Employer',
                'type' => 'pension_employer',
                'rate' => 11,
                'effective_from' => '2023-01-01',
                'is_active' => true,
            ],
            [
                'name' => 'VAT',
                'type' => 'vat',
                'rate' => 15,
                'effective_from' => '2023-01-01',
                'is_active' => true,
            ],
        ];

        foreach ($taxBrackets as $bracket) {
            TaxBracket::create($bracket);
        }
    }
}
