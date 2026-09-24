<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('digital_products')->onDelete('cascade');
            $table->string('order_id')->unique();
            $table->integer('amount');
            $table->string('status')->default('pending'); // pending | paid | failed
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_purchases');
    }
};
