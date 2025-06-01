<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PickupRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pickupRequests = [
            [
                'pickup_code' => 'PU' . Carbon::now()->format('ymd') . '0001',
                'user_id' => 2, // Sales One
                'recipient_name' => 'John Doe',
                'recipient_phone' => '081234567800',
                'recipient_city' => 'Jakarta',
                'recipient_province' => 'DKI Jakarta',
                'recipient_postal_code' => '12345',
                'recipient_address' => 'Jl. Sudirman No. 123',
                'recipient_latitude' => -6.200000,
                'recipient_longitude' => 106.816666,
                'pickup_name' => 'Sales One',
                'pickup_phone' => '081234567891',
                'pickup_city' => 'Tangerang',
                'pickup_province' => 'Banten',
                'pickup_postal_code' => '15143',
                'pickup_address' => 'Jl. MH Thamrin No. 456',
                'pickup_latitude' => -6.170000,
                'pickup_longitude' => 106.630000,
                'pickup_scheduled_at' => Carbon::now()->addDays(1),
                'payment_method' => 'balance',
                'shipping_cost' => 15000.00,
                'service_fee' => 5000.00,
                'product_total' => 150000.00,
                'cod_amount' => 0.00,
                'total_amount' => 170000.00,
                'status' => 'pending',
                'courier_service' => 'JNE',
                'courier_tracking_number' => null,
                'courier_response' => null,
                'notes' => 'Mohon hati-hati dalam pengiriman',
                'requested_at' => Carbon::now(),
                'picked_up_at' => null,
                'delivered_at' => null,
                'cod_collected_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'pickup_code' => 'PU' . Carbon::now()->format('ymd') . '0002',
                'user_id' => 3, // Sales Two
                'recipient_name' => 'Jane Smith',
                'recipient_phone' => '081234567801',
                'recipient_city' => 'Bandung',
                'recipient_province' => 'Jawa Barat',
                'recipient_postal_code' => '40123',
                'recipient_address' => 'Jl. Asia Afrika No. 789',
                'recipient_latitude' => -6.914744,
                'recipient_longitude' => 107.609810,
                'pickup_name' => 'Sales Two',
                'pickup_phone' => '081234567892',
                'pickup_city' => 'Tangerang',
                'pickup_province' => 'Banten',
                'pickup_postal_code' => '15143',
                'pickup_address' => 'Jl. BSD Raya No. 321',
                'pickup_latitude' => -6.170000,
                'pickup_longitude' => 106.630000,
                'pickup_scheduled_at' => Carbon::now()->addDays(2),
                'payment_method' => 'wallet',
                'shipping_cost' => 25000.00,
                'service_fee' => 7500.00,
                'product_total' => 280000.00,
                'cod_amount' => 50000.00,
                'total_amount' => 362500.00,
                'status' => 'confirmed',
                'courier_service' => 'SiCepat',
                'courier_tracking_number' => null,
                'courier_response' => null,
                'notes' => 'Pengiriman express',
                'requested_at' => Carbon::now()->subHours(2),
                'picked_up_at' => null,
                'delivered_at' => null,
                'cod_collected_at' => null,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('pickup_requests')->insert($pickupRequests);
        
        $this->command->info('Pickup requests seeded successfully!');
    }
}