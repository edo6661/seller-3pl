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
        Schema::create('ticket_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Pesan respons
            $table->text('message');
            
            // File attachment untuk respons (optional)
            $table->json('attachments')->nullable();
            
            // Apakah respons dari admin atau user
            $table->boolean('is_admin_response')->default(false);
            
            // Status dibaca
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['support_ticket_id', 'created_at']);
            $table->index(['user_id', 'is_admin_response']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_responses');
    }
};