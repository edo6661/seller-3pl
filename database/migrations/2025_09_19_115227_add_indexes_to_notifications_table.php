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
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'type', 'created_at'], 'notifications_user_type_created_idx');
            $table->index(['user_id', 'type'], 'notifications_user_type_idx');
            $table->index('created_at', 'notifications_created_at_idx');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_type_created_idx');
            $table->dropIndex('notifications_user_type_idx');
            $table->dropIndex('notifications_created_at_idx');
        });
    }
};