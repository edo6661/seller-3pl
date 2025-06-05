<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pickup_requests', function (Blueprint $table) {
            DB::statement("ALTER TABLE pickup_requests MODIFY payment_method ENUM('wallet', 'cod') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_requests', function (Blueprint $table) {
            DB::statement("ALTER TABLE pickup_requests MODIFY payment_method ENUM('balance', 'wallet') NOT NULL");
        });
    }
};
