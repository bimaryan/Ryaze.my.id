<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sesi halaman pembayaran QRIS Dinamis per invoice. Expiry disimpan agar
     * timer pembayaran TIDAK mengulang dari awal ketika user menutup lalu
     * membuka kembali halaman QRIS untuk invoice yang sama.
     */
    public function up(): void
    {
        Schema::create('qris_payment_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qris_payment_sessions');
    }
};
