<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceAccountsSeeder extends Seeder
{
    /**
     * Default chart of accounts. Income accounts mirror
     * payment_receipts.purpose 1:1 (see FinanceAccount::incomeAccountCodeForPurpose)
     * so every existing Annex-I receipt type has somewhere to post.
     * is_system accounts are the ones the app posts to automatically and
     * are protected from deletion in the UI.
     */
    public function run(): void
    {
        $accounts = [
            ['1000', 'Cash & Bank', 'asset', true],
            ['2000', 'Accounts Payable', 'liability', false],
            ['3000', "Owner's Equity", 'equity', false],

            ['4010', 'Property Valuation Fee Income', 'income', true],
            ['4020', 'Field Visit Charge Income', 'income', true],
            ['4030', 'Digital Marketing Charge Income', 'income', true],
            ['4040', 'Preliminary Consultation Income', 'income', true],
            ['4050', 'Property Registration Service Income', 'income', true],
            ['4060', 'Brokerage Commission Income', 'income', true],
            ['4090', 'Other Income', 'income', true],

            ['5010', 'Salaries & Wages', 'expense', false],
            ['5020', 'Office Rent', 'expense', false],
            ['5030', 'Utilities', 'expense', false],
            ['5040', 'Marketing & Advertising', 'expense', false],
            ['5050', 'Office Supplies', 'expense', false],
            ['5060', 'Travel & Field Visit Expense', 'expense', false],
            ['5090', 'Other Expense', 'expense', false],
        ];

        foreach ($accounts as [$code, $name, $type, $isSystem]) {
            DB::table('finance_accounts')->updateOrInsert(
                ['account_code' => $code],
                [
                    'account_name' => $name,
                    'account_type' => $type,
                    'is_system' => $isSystem,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
