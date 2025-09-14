<?php

namespace Database\Seeders;

use App\Models\WithdrawRequest;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SellerProfileSeeder::class,
            ProductSeeder::class,
            BuyerRatingSeeder::class,
            NotificationSeeder::class,
            // WalletSeeder::class,
            // WalletTransactionSeeder::class,
            // WithdrawRequestSeeder::class,
            UserAddressSeeder::class,
            BankAccountSeeder::class,
        ]);
        
        $this->command->info('All seeders completed successfully!');
    }
}