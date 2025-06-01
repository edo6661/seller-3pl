<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SellerProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellerProfiles = [
            [
                'user_id' => 2, // Sales One
                'business_name' => 'Toko Elektronik Maju',
                'address' => 'Jl. Sudirman No. 123',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10220',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'is_profile_complete' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 3, // Sales Two
                'business_name' => 'Fashion Store Trendy',
                'address' => 'Jl. Gatot Subroto No. 456',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12930',
                'latitude' => -6.2297,
                'longitude' => 106.8230,
                'is_profile_complete' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('seller_profiles')->insert($sellerProfiles);
        
        $this->command->info('Seller profiles seeded successfully!');
    }
}