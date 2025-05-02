<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        Currency::create([
            'name' => 'دينار عراقي',
            'code' => 'IQD',
            'symbol' => 'د.ع',
            'exchange_rate' => 1,
            'is_default' => true,
        ]);

        Currency::create([
            'name' => 'دولار أمريكي',
            'code' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1500, // مثال لسعر الصرف مقابل الدينار
            'is_default' => false,
        ]);
    }
}
