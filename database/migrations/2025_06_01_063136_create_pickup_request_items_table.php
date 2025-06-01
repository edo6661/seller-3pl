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
        Schema::create('pickup_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pickup_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('weight_per_pcs', 8, 2);
            $table->decimal('price_per_pcs', 15, 2);
            $table->decimal('total_weight', 8, 2);
            $table->decimal('total_price', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_request_items');
    }
};
