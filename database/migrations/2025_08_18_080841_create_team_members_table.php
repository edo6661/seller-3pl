<?php
// 1. MIGRATION - create_team_members_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->json('permissions')->nullable(); // Permissions yang diizinkan
            $table->boolean('is_active')->default(true);
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['seller_id', 'is_active']);
            $table->index(['email', 'seller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};

