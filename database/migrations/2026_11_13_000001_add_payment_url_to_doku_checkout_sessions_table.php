<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doku_checkout_sessions', function (Blueprint $table) {
            // URL halaman checkout yang dibuat DOKU, dipakai ulang selama
            // sesi masih aktif agar klik berulang tidak membuat transaksi baru.
            $table->string('payment_url')->nullable()->after('token_id');
        });
    }

    public function down(): void
    {
        Schema::table('doku_checkout_sessions', function (Blueprint $table) {
            $table->dropColumn('payment_url');
        });
    }
};
