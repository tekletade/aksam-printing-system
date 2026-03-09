<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // First, create parent accounts
        $parentAccounts = [
            // Assets (1000-1999)
            [
                'account_code' => '1000',
                'name' => 'Cash',
                'type' => 'asset',
                'category' => 'current_asset',
                'is_cash_account' => true,
                'level' => 1,
                'is_active' => true,
            ],
            [
                'account_code' => '1100',
                'name' => 'Bank Accounts',
                'type' => 'asset',
                'category' => 'current_asset',
                'is_bank_account' => true,
                'level' => 1,
                'is_active' => true,
            ],
            [
                'account_code' => '1200',
                'name' => 'Accounts Receivable',
                'type' => 'asset',
                'category' => 'current_asset',
                'level' => 1,
                'is_active' => true,
            ],
            [
                'account_code' => '1300',
                'name' => 'Inventory',
                'type' => 'asset',
                'category' => 'current_asset',
                'level' => 1,
                'is_active' => true,
            ],
            [
                'account_code' => '1400',
                'name' => 'Fixed Assets',
                'type' => 'asset',
                'category' => 'fixed_asset',
                'level' => 1,
                'is_active' => true,
            ],

            // Liabilities (2000-2999)
            [
                'account_code' => '2000',
                'name' => 'Liabilities',
                'type' => 'liability',
                'category' => 'current_liability',
                'level' => 1,
                'is_active' => true,
            ],
            [
                'account_code' => '2100',
                'name' => 'Accounts Payable',
                'type' => 'liability',
                'category' => 'current_liability',
                'level' => 1,
                'is_active' => true,
            ],
            [
                'account_code' => '2200',
                'name' => 'Tax Liabilities',
                'type' => 'liability',
                'category' => 'current_liability',
                'is_tax_account' => true,
                'level' => 1,
                'is_active' => true,
            ],
            [
                'account_code' => '2300',
                'name' => 'Accrued Expenses',
                'type' => 'liability',
                'category' => 'current_liability',
                'level' => 1,
                'is_active' => true,
            ],

            // Equity (3000-3999)
            [
                'account_code' => '3000',
                'name' => 'Equity',
                'type' => 'equity',
                'category' => 'equity',
                'level' => 1,
                'is_active' => true,
            ],

            // Income/Revenue (4000-4999)
            [
                'account_code' => '4000',
                'name' => 'Revenue',
                'type' => 'income',
                'category' => 'revenue',
                'level' => 1,
                'is_active' => true,
            ],
            [
                'account_code' => '4200',
                'name' => 'Other Income',
                'type' => 'income',
                'category' => 'other_income',
                'level' => 1,
                'is_active' => true,
            ],

            // Expenses (5000-5999)
            [
                'account_code' => '5000',
                'name' => 'Expenses',
                'type' => 'expense',
                'category' => 'operating_expense',
                'level' => 1,
                'is_active' => true,
            ],
            [
                'account_code' => '5100',
                'name' => 'Cost of Goods Sold',
                'type' => 'expense',
                'category' => 'cogs',
                'level' => 1,
                'is_active' => true,
            ],
            [
                'account_code' => '5200',
                'name' => 'Salary Expenses',
                'type' => 'expense',
                'category' => 'operating_expense',
                'level' => 1,
                'is_active' => true,
            ],
            [
                'account_code' => '5300',
                'name' => 'Operating Expenses',
                'type' => 'expense',
                'category' => 'operating_expense',
                'level' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($parentAccounts as $account) {
            ChartOfAccount::create($account);
        }

        // Now create child accounts
        $childAccounts = [
            // Cash children
            [
                'account_code' => '1010',
                'name' => 'Cash - Main',
                'type' => 'asset',
                'category' => 'current_asset',
                'parent_account_code' => '1000',
                'is_cash_account' => true,
                'level' => 2,
                'is_active' => true,
            ],
            [
                'account_code' => '1020',
                'name' => 'Cash - Petty',
                'type' => 'asset',
                'category' => 'current_asset',
                'parent_account_code' => '1000',
                'is_cash_account' => true,
                'level' => 2,
                'is_active' => true,
            ],

            // Bank children
            [
                'account_code' => '1110',
                'name' => 'Commercial Bank of Ethiopia',
                'type' => 'asset',
                'category' => 'current_asset',
                'parent_account_code' => '1100',
                'is_bank_account' => true,
                'level' => 2,
                'is_active' => true,
            ],
            [
                'account_code' => '1120',
                'name' => 'Dashen Bank',
                'type' => 'asset',
                'category' => 'current_asset',
                'parent_account_code' => '1100',
                'is_bank_account' => true,
                'level' => 2,
                'is_active' => true,
            ],

            // Accounts Receivable children
            [
                'account_code' => '1210',
                'name' => 'Trade Debtors',
                'type' => 'asset',
                'category' => 'current_asset',
                'parent_account_code' => '1200',
                'level' => 2,
                'is_active' => true,
            ],
            [
                'account_code' => '1220',
                'name' => 'Other Receivables',
                'type' => 'asset',
                'category' => 'current_asset',
                'parent_account_code' => '1200',
                'level' => 2,
                'is_active' => true,
            ],

            // Inventory children
            [
                'account_code' => '1310',
                'name' => 'Toner Inventory',
                'type' => 'asset',
                'category' => 'current_asset',
                'parent_account_code' => '1300',
                'level' => 2,
                'is_active' => true,
            ],
            [
                'account_code' => '1320',
                'name' => 'Paper Inventory',
                'type' => 'asset',
                'category' => 'current_asset',
                'parent_account_code' => '1300',
                'level' => 2,
                'is_active' => true,
            ],

            // Tax Liabilities children
            [
                'account_code' => '2210',
                'name' => 'PAYE Payable',
                'type' => 'liability',
                'category' => 'current_liability',
                'parent_account_code' => '2200',
                'is_tax_account' => true,
                'level' => 2,
                'is_active' => true,
            ],
            [
                'account_code' => '2220',
                'name' => 'Pension Payable',
                'type' => 'liability',
                'category' => 'current_liability',
                'parent_account_code' => '2200',
                'level' => 2,
                'is_active' => true,
            ],
            [
                'account_code' => '2230',
                'name' => 'VAT Payable',
                'type' => 'liability',
                'category' => 'current_liability',
                'parent_account_code' => '2200',
                'is_tax_account' => true,
                'level' => 2,
                'is_active' => true,
            ],

            // Revenue children
            [
                'account_code' => '4100',
                'name' => 'Printing Revenue',
                'type' => 'income',
                'category' => 'revenue',
                'parent_account_code' => '4000',
                'level' => 2,
                'is_active' => true,
            ],
            [
                'account_code' => '4110',
                'name' => 'B&W Printing',
                'type' => 'income',
                'category' => 'revenue',
                'parent_account_code' => '4100',
                'level' => 3,
                'is_active' => true,
            ],
            [
                'account_code' => '4120',
                'name' => 'Color Printing',
                'type' => 'income',
                'category' => 'revenue',
                'parent_account_code' => '4100',
                'level' => 3,
                'is_active' => true,
            ],

            // COGS children
            [
                'account_code' => '5110',
                'name' => 'Toner Consumables',
                'type' => 'expense',
                'category' => 'cogs',
                'parent_account_code' => '5100',
                'level' => 2,
                'is_active' => true,
            ],
            [
                'account_code' => '5120',
                'name' => 'Paper Consumables',
                'type' => 'expense',
                'category' => 'cogs',
                'parent_account_code' => '5100',
                'level' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($childAccounts as $account) {
            // Find parent by account_code
            $parent = ChartOfAccount::where('account_code', $account['parent_account_code'])->first();
            if ($parent) {
                unset($account['parent_account_code']);
                $account['parent_id'] = $parent->id;
                ChartOfAccount::create($account);
            }
        }
    }
}
