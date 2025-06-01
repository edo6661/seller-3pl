<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BuyerRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buyerRatings = [
            [
                'phone_number' => '081234567890',
                'name' => 'Ahmad Wijaya',
                'total_orders' => 25,
                'successful_orders' => 23,
                'failed_cod_orders' => 1,
                'cancelled_orders' => 1,
                'success_rate' => 92.00,
                'risk_level' => 'low',
                'notes' => 'Buyer terpercaya dengan riwayat pembayaran yang baik',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phone_number' => '081234567891',
                'name' => 'Siti Nurhaliza',
                'total_orders' => 15,
                'successful_orders' => 12,
                'failed_cod_orders' => 2,
                'cancelled_orders' => 1,
                'success_rate' => 80.00,
                'risk_level' => 'medium',
                'notes' => 'Perlu perhatian khusus, beberapa kali menolak COD',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phone_number' => '081234567892',
                'name' => 'Budi Santoso',
                'total_orders' => 8,
                'successful_orders' => 4,
                'failed_cod_orders' => 3,
                'cancelled_orders' => 1,
                'success_rate' => 50.00,
                'risk_level' => 'high',
                'notes' => 'Sering menolak COD dan membatalkan pesanan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phone_number' => '081234567893',
                'name' => 'Maya Sari',
                'total_orders' => 30,
                'successful_orders' => 29,
                'failed_cod_orders' => 0,
                'cancelled_orders' => 1,
                'success_rate' => 96.67,
                'risk_level' => 'low',
                'notes' => 'Buyer premium dengan tingkat keberhasilan sangat tinggi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'phone_number' => '081234567894',
                'name' => 'Andi Pratama',
                'total_orders' => 12,
                'successful_orders' => 8,
                'failed_cod_orders' => 3,
                'cancelled_orders' => 1,
                'success_rate' => 66.67,
                'risk_level' => 'medium',
                'notes' => 'Kadang tidak kooperatif saat pengiriman COD',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('buyer_ratings')->insert($buyerRatings);
        
        $this->command->info('Buyer ratings seeded successfully!');
    }
}