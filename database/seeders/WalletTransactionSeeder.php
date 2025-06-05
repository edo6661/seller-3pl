<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WalletTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = [
            // Transaksi untuk Admin (wallet_id: 1)
            [
                'wallet_id' => 1,
                'type' => 'topup',
                'amount' => 5000000.00,
                'balance_before' => 0.00,
                'balance_after' => 5000000.00,
                'description' => 'Top up saldo awal admin',
                'reference_id' => 'TOPUP001',
                'status' => 'success',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            
            // Transaksi untuk Sales One (wallet_id: 2)
            [
                'wallet_id' => 2,
                'type' => 'topup',
                'amount' => 3000000.00,
                'balance_before' => 0.00,
                'balance_after' => 3000000.00,
                'description' => 'Top up saldo awal sales',
                'reference_id' => 'TOPUP002',
                'status' => 'success',
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(25),
            ],
            [
                'wallet_id' => 2,
                'type' => 'payment',
                'amount' => 500000.00,
                'balance_before' => 3000000.00,
                'balance_after' => 2500000.00,
                'description' => 'Pembayaran komisi penjualan',
                'reference_id' => 'ORDER001',
                'status' => 'success',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            
            // Transaksi untuk Sales Two (wallet_id: 3)
            [
                'wallet_id' => 3,
                'type' => 'topup',
                'amount' => 2000000.00,
                'balance_before' => 0.00,
                'balance_after' => 2000000.00,
                'description' => 'Top up saldo awal sales',
                'reference_id' => 'TOPUP003',
                'status' => 'success',
                'created_at' => Carbon::now()->subDays(22),
                'updated_at' => Carbon::now()->subDays(22),
            ],
            [
                'wallet_id' => 3,
                'type' => 'payment',
                'amount' => 200000.00,
                'balance_before' => 2000000.00,
                'balance_after' => 1800000.00,
                'description' => 'Pembayaran komisi penjualan',
                'reference_id' => 'ORDER002',
                'status' => 'success',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            
            
            
            // Transaksi refund
            [
                'wallet_id' => 2,
                'type' => 'refund',
                'amount' => 150000.00,
                'balance_before' => 2500000.00,
                'balance_after' => 2650000.00,
                'description' => 'Refund pesanan yang dibatalkan',
                'reference_id' => 'ORDER003',
                'status' => 'success',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
        ];

        DB::table('wallet_transactions')->insert($transactions);
        
        $this->command->info('Wallet transactions seeded successfully!');
    }
}