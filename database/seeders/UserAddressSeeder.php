<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserAddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            
            [
                'user_id' => 2,
                'label' => 'Rumah Utama',
                'name' => 'Sales One',
                'phone' => '081234567891',
                'city' => 'Tangerang',
                'province' => 'Banten',
                'postal_code' => '15143',
                'address' => 'Jl. MH Thamrin No. 456, BSD City',
                'latitude' => -6.170000,
                'longitude' => 106.630000,
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'label' => 'Kantor',
                'name' => 'Sales One',
                'phone' => '081234567891',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12560',
                'address' => 'Jl. Sudirman Kav. 52-53, SCBD',
                'latitude' => -6.225000,
                'longitude' => 106.820000,
                'is_default' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'label' => 'Toko',
                'name' => 'Sales One Store',
                'phone' => '081234567891',
                'city' => 'Tangerang',
                'province' => 'Banten',
                'postal_code' => '15144',
                'address' => 'Jl. Raya Serpong No. 789, Serpong',
                'latitude' => -6.275000,
                'longitude' => 106.675000,
                'is_default' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            
            
            [
                'user_id' => 3,
                'label' => 'Rumah',
                'name' => 'Sales Two',
                'phone' => '081234567892',
                'city' => 'Tangerang',
                'province' => 'Banten',
                'postal_code' => '15143',
                'address' => 'Jl. BSD Raya No. 321, BSD City',
                'latitude' => -6.180000,
                'longitude' => 106.640000,
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 3,
                'label' => 'Gudang',
                'name' => 'Sales Two Warehouse',
                'phone' => '081234567892',
                'city' => 'Tangerang',
                'province' => 'Banten',
                'postal_code' => '15710',
                'address' => 'Jl. Industri Raya No. 15, Cikupa',
                'latitude' => -6.240000,
                'longitude' => 106.510000,
                'is_default' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('user_addresses')->insert($addresses);
        
        $this->command->info('User addresses seeded successfully!');
    }
}