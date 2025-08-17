<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            
            // User yang membuat tiket
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Tipe tiket: general (umum) atau shipment (khusus pengiriman)
            $table->enum('ticket_type', ['general', 'shipment'])->default('general');
            
            // Jika tipe shipment, bisa berdasarkan pickup_request atau tracking_number
            $table->foreignId('pickup_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('tracking_number')->nullable();
            
            // Kategori masalah
            $table->enum('category', [
                'delivery_issue',        // Masalah pengiriman
                'payment_issue',         // Masalah pembayaran
                'item_damage',          // Barang rusak
                'item_lost',            // Barang hilang
                'wrong_address',        // Alamat salah
                'courier_service',      // Masalah kurir
                'app_technical',        // Masalah teknis aplikasi
                'account_issue',        // Masalah akun
                'other'                 // Lainnya
            ]);
            
            $table->string('subject');
            $table->text('description');
            
            // Priority
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // Status tiket
            $table->enum('status', [
                'open',         // Baru dibuka
                'in_progress',  // Sedang ditangani
                'waiting_user', // Menunggu respons user
                'resolved',     // Sudah diselesaikan
                'closed'        // Ditutup
            ])->default('open');
            
            // Admin yang menangani (nullable karena bisa belum diassign)
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            // File attachment (optional)
            $table->json('attachments')->nullable();
            
            // Catatan admin internal
            $table->text('admin_notes')->nullable();
            
            // Resolution details
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['user_id', 'status']);
            $table->index(['ticket_type', 'category']);
            $table->index(['pickup_request_id']);
            $table->index(['tracking_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};