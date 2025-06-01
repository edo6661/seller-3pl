<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Products untuk Sales One (user_id = 2)
            [
                'user_id' => 2,
                'name' => 'Kaos Polos',
                'description' => 'Kaos polos berkualitas tinggi berbahan cotton combed',
                'weight_per_pcs' => 0.20,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'name' => 'Kemeja Formal',
                'description' => 'Kemeja formal untuk kebutuhan kantor',
                'weight_per_pcs' => 0.35,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'name' => 'Celana Jeans',
                'description' => 'Celana jeans premium dengan kualitas terbaik',
                'weight_per_pcs' => 0.60,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Products untuk Sales Two (user_id = 3)
            [
                'user_id' => 3,
                'name' => 'Sepatu Sneakers',
                'description' => 'Sepatu sneakers casual untuk sehari-hari',
                'weight_per_pcs' => 0.80,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 3,
                'name' => 'Tas Ransel',
                'description' => 'Tas ransel untuk laptop dan kebutuhan sekolah',
                'weight_per_pcs' => 0.45,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 3,
                'name' => 'Jaket Hoodie',
                'description' => 'Jaket hoodie hangat untuk cuaca dingin',
                'weight_per_pcs' => 0.50,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('products')->insert($products);
        
        $this->command->info('Products seeded successfully!');
    }
}