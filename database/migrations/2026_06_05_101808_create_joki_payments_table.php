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
        Schema::create('joki_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('joki_orders')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->string('payment_name');
            $table->integer('amount');
            $table->enum('status', ['unpaid', 'paid', 'failed'])->default('unpaid'); // Hapus pending_verification
            $table->string('snap_token')->nullable(); // TAMBAHAN UNTUK MIDTRANS
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('joki_payments');
    }
};
