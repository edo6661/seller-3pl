<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_top_up_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('request_code')->unique();
            $table->decimal('amount', 15, 2);
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('qr_code_url')->nullable();
            $table->enum('status', ['pending', 'waiting_payment', 'waiting_approval', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->string('payment_proof_path')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('request_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_top_up_requests');
    }
};