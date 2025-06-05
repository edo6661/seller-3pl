<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'avatar' => null,
                'role' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sales One',
                'email' => 'sales1@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'phone' => '081234567891',
                'avatar' => null,
                'role' => 'seller',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sales Two',
                'email' => 'sales2@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'phone' => '081234567892',
                'avatar' => null,
                'role' => 'seller',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('users')->insert($users);
        
        $this->command->info('Users seeded successfully!');
    }
}