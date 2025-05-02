<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed initial account balances for default currency
        $defaultCurrency = \App\Models\Currency::where('is_default', true)->first();
        if (!$defaultCurrency) {
            // No default currency set; skip balances seeding
            return;
        }
        // For each account, set initial balance to zero in default currency
        foreach (\App\Models\Account::all() as $account) {
            \App\Models\AccountBalance::create([
                'account_id' => $account->id,
                'currency_id' => $defaultCurrency->id,
                'balance' => 0,
            ]);
        }
    }
}
