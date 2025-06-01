<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wallets = [
            [
                'user_id' => 1, // Admin
                'balance' => 5000000.00,
                'pending_balance' => 0.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2, // Sales One
                'balance' => 2500000.00,
                'pending_balance' => 150000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 3, // Sales Two
                'balance' => 1800000.00,
                'pending_balance' => 75000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
        ];

        DB::table('wallets')->insert($wallets);
        
        $this->command->info('Wallets seeded successfully!');
    }
}