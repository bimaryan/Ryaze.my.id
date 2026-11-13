<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doku_checkout_sessions', function (Blueprint $table) {
            $table->id();
            // Nomor invoice / reference order (HST-INV-*, WLT-TOPUP-*, dll)
            $table->string('order_id', 64)->unique();
            $table->string('token_id')->nullable();
            // Batas waktu pembayaran sesi DOKU (dari response payment.expired_date)
            $table->timestamp('expired_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doku_checkout_sessions');
    }
};
