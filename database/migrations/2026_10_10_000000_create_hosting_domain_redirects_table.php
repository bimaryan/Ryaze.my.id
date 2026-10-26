<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Redirect domain ala Vercel: satu domain bisa diarahkan ke URL lain,
     * dengan memilih Temporary (302/307) atau Permanent (301/308).
     *
     * Satu domain hanya boleh punya SATU aturan aktif — nginx cukup membaca
     * satu file `.redirects/<host>` berisi "<kode> <url tujuan>".
     */
    public function up(): void
    {
        if (Schema::hasTable('hosting_domain_redirects')) {
            return;
        }

        Schema::create('hosting_domain_redirects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hosting_domain_id')
                ->constrained('hosting_domains')
                ->cascadeOnDelete();
            $table->string('target', 255);
            // 301 & 308 = permanent, 302 & 307 = temporary.
            $table->unsignedSmallInteger('status_code')->default(308);
            $table->timestamps();

            $table->unique('hosting_domain_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosting_domain_redirects');
    }
};