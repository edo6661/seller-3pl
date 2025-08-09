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
        Schema::create('pickup_requests', function (Blueprint $table) {
            $table->id();
            $table->string('pickup_code')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')
              ->constrained('user_addresses')
              ->onDelete('cascade');
            $table->string('pickup_name');
            $table->string('pickup_phone');
            $table->string('pickup_city');
            $table->string('pickup_province');
            $table->string('pickup_postal_code');
            $table->text('pickup_address');
            $table->decimal('pickup_latitude', 10, 8)->nullable();
            $table->decimal('pickup_longitude', 11, 8)->nullable();
            
            $table->enum('payment_method', ['balance', 'wallet']);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('service_fee', 15, 2)->default(0);
            $table->decimal('product_total', 15, 2)->default(0);
            $table->decimal('cod_amount', 15, 2)->default(0); 
            $table->decimal('total_amount', 15, 2)->default(0);
            
            $table->enum('status', [
                'pending', 'confirmed', 'pickup_scheduled', 'picked_up', 
                'in_transit', 'delivered', 'failed', 'cancelled'
            ])->default('pending');
            
            
            $table->string('courier_service')->nullable(); 
            $table->string('courier_tracking_number')->nullable();
            $table->json('courier_response')->nullable(); 
            
            $table->text('notes')->nullable();
            
            $table->timestamp('requested_at');
            $table->timestamp('pickup_scheduled_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cod_collected_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_requests');
    }
};
