<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PickupRequestItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pickupRequestItems = [
            // Items untuk pickup request pertama (Sales One)
            [
                'pickup_request_id' => 1,
                'product_id' => 1, // Kaos Polos
                'quantity' => 5,
                'weight_per_pcs' => 0.20,
                'price_per_pcs' => 50000.00,
                'total_weight' => 1.00,
                'total_price' => 250000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'pickup_request_id' => 1,
                'product_id' => 2, // Kemeja Formal
                'quantity' => 2,
                'weight_per_pcs' => 0.35,
                'price_per_pcs' => 125000.00,
                'total_weight' => 0.70,
                'total_price' => 250000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Items untuk pickup request kedua (Sales Two)
            [
                'pickup_request_id' => 2,
                'product_id' => 4, // Sepatu Sneakers
                'quantity' => 2,
                'weight_per_pcs' => 0.80,
                'price_per_pcs' => 350000.00,
                'total_weight' => 1.60,
                'total_price' => 700000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'pickup_request_id' => 2,
                'product_id' => 5, // Tas Ransel
                'quantity' => 3,
                'weight_per_pcs' => 0.45,
                'price_per_pcs' => 150000.00,
                'total_weight' => 1.35,
                'total_price' => 450000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('pickup_request_items')->insert($pickupRequestItems);
        
        $this->command->info('Pickup request items seeded successfully!');
    }
}