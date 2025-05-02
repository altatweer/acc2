<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshAccountTree extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-account-tree';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting account tree refresh...');
        // Disable foreign key checks and truncate accounts and balances
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Account::truncate();
        \App\Models\AccountBalance::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Seeding account categories and accounts...');
        $this->call('db:seed', ['--class' => \Database\Seeders\AccountsTableSeeder::class]);

        $this->info('Seeding account balances for all currencies...');
        $this->call('db:seed', ['--class' => \Database\Seeders\AccountBalanceSeeder::class]);

        $this->info('Account tree and balances have been refreshed successfully.');
    }
}
