<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WithdrawRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $withdrawRequests = [
            [
                'user_id' => 2,
                'withdrawal_code' => 'WD2406010001',
                'amount' => 500000.00,
                'admin_fee' => 2500.00,
                'bank_name' => 'Bank BCA',
                'account_number' => '1234567890',
                'account_name' => 'Sales One',
                'status' => 'completed',
                'requested_at' => Carbon::now()->subDays(7),
                'processed_at' => Carbon::now()->subDays(6),
                'completed_at' => Carbon::now()->subDays(6),
                'admin_notes' => 'Penarikan berhasil diproses',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(6),
            ],
            [
                'user_id' => 3,
                'withdrawal_code' => 'WD2406010002',
                'amount' => 300000.00,
                'admin_fee' => 2500.00,
                'bank_name' => 'Bank Mandiri',
                'account_number' => '0987654321',
                'account_name' => 'Sales Two',
                'status' => 'processing',
                'requested_at' => Carbon::now()->subDays(2),
                'processed_at' => Carbon::now()->subDays(1),
                'completed_at' => null,
                'admin_notes' => 'Sedang diproses oleh tim finance',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(1),
            ],
         
            [
                'user_id' => 2,
                'withdrawal_code' => 'WD2406010004',
                'amount' => 200000.00,
                'admin_fee' => 2500.00,
                'bank_name' => 'Bank BCA',
                'account_number' => '1234567890',
                'account_name' => 'Sales One',
                'status' => 'pending',
                'requested_at' => Carbon::now()->subHours(6),
                'processed_at' => null,
                'completed_at' => null,
                'admin_notes' => null,
                'created_at' => Carbon::now()->subHours(6),
                'updated_at' => Carbon::now()->subHours(6),
            ],
            [
                'user_id' => 3,
                'withdrawal_code' => 'WD2405300001',
                'amount' => 150000.00,
                'admin_fee' => 2500.00,
                'bank_name' => 'Bank Mandiri',
                'account_number' => '0987654321',
                'account_name' => 'Sales Two',
                'status' => 'completed',
                'requested_at' => Carbon::now()->subDays(10),
                'processed_at' => Carbon::now()->subDays(9),
                'completed_at' => Carbon::now()->subDays(9),
                'admin_notes' => 'Penarikan batch sore berhasil diproses',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(9),
            ],
        ];

        DB::table('withdraw_requests')->insert($withdrawRequests);
        
        $this->command->info('Withdraw requests seeded successfully!');
    }
}