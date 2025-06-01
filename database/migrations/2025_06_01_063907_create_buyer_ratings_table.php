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
        Schema::create('buyer_ratings', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number'); 
            $table->string('name')->nullable(); 
            $table->integer('total_orders')->default(0);
            $table->integer('successful_orders')->default(0);
            $table->integer('cancelled_orders')->default(0);
            $table->integer('failed_cod_orders')->default(0);
            $table->decimal('success_rate', 5, 2)->default(100); 
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->text('notes')->nullable(); 
            $table->timestamps();
            
            $table->index('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_ratings');
    }
};
