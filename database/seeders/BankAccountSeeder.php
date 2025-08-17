<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BankAccount;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $bankAccounts = [
            [
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'PT. COMPANY NAME',
                'is_active' => true,
            ],
            [
                'bank_name' => 'BNI',
                'account_number' => '0987654321',
                'account_name' => 'PT. COMPANY NAME',
                'is_active' => true,
            ],
            [
                'bank_name' => 'Mandiri',
                'account_number' => '1122334455',
                'account_name' => 'PT. COMPANY NAME',
                'is_active' => true,
            ],
        ];

        foreach ($bankAccounts as $account) {
            BankAccount::create($account);
        }
    }
}