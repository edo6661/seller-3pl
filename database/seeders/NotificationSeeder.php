<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notifications = [
            // Notifikasi untuk Admin (user_id: 1)
            [
                'user_id' => 1,
                'type' => 'system',
                'title' => 'Sistem Berhasil Diperbarui',
                'message' => 'Sistem telah berhasil diperbarui ke versi terbaru. Semua fitur baru telah aktif.',
                'read_at' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'user_id' => 1,
                'type' => 'withdraw',
                'title' => 'Permintaan Penarikan Baru',
                'message' => 'Ada 3 permintaan penariran yang menunggu persetujuan Anda.',
                'read_at' => null,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            
            // Notifikasi untuk Sales One (user_id: 2)
            [
                'user_id' => 2,
                'type' => 'wallet',
                'title' => 'Top Up Berhasil',
                'message' => 'Top up saldo sebesar Rp 3.000.000 telah berhasil diproses.',
                'read_at' => Carbon::now()->subDays(20),
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'user_id' => 2,
                'type' => 'order',
                'title' => 'Pesanan Baru Diterima',
                'message' => 'Anda mendapat pesanan baru dengan kode ORDER001. Silakan segera diproses.',
                'read_at' => Carbon::now()->subDays(18),
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'user_id' => 2,
                'type' => 'withdraw',
                'title' => 'Penarikan Berhasil',
                'message' => 'Penarikan dana sebesar Rp 500.000 telah berhasil ditransfer ke rekening Anda.',
                'read_at' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'user_id' => 2,
                'type' => 'buyer_rating',
                'title' => 'Peringatan Buyer Berisiko',
                'message' => 'Buyer dengan nomor 081234567892 memiliki tingkat kegagalan tinggi. Mohon berhati-hati.',
                'read_at' => null,
                'created_at' => Carbon::now()->subHours(5),
                'updated_at' => Carbon::now()->subHours(5),
            ],
            
            // Notifikasi untuk Sales Two (user_id: 3)
            [
                'user_id' => 3,
                'type' => 'wallet',
                'title' => 'Top Up Berhasil',
                'message' => 'Top up saldo sebesar Rp 2.000.000 telah berhasil diproses.',
                'read_at' => Carbon::now()->subDays(18),
                'created_at' => Carbon::now()->subDays(22),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'user_id' => 3,
                'type' => 'order',
                'title' => 'Komisi Penjualan Diterima',
                'message' => 'Anda mendapat komisi sebesar Rp 200.000 dari penjualan ORDER002.',
                'read_at' => Carbon::now()->subDays(12),
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'user_id' => 3,
                'type' => 'withdraw',
                'title' => 'Penarikan Sedang Diproses',
                'message' => 'Permintaan penariran Anda sebesar Rp 300.000 sedang diproses oleh tim finance.',
                'read_at' => null,
                'created_at' => Carbon::now()->subHours(12),
                'updated_at' => Carbon::now()->subHours(12),
            ],
            
            
        ];

        DB::table('notifications')->insert($notifications);
        
        $this->command->info('Notifications seeded successfully!');
    }
}
