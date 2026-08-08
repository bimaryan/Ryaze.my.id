<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosting_projects', function (Blueprint $table) {
            // Konfigurasi nginx custom untuk subdomain project (dieksekusi worker di server)
            $table->longText('nginx_custom')->nullable()->after('source_type');
            // status: pending | applied | failed | reset
            $table->string('nginx_status', 20)->nullable()->after('nginx_custom');
            $table->text('nginx_error')->nullable()->after('nginx_status');
            $table->timestamp('nginx_applied_at')->nullable()->after('nginx_error');
        });
    }

    public function down(): void
    {
        Schema::table('hosting_projects', function (Blueprint $table) {
            $table->dropColumn(['nginx_custom', 'nginx_status', 'nginx_error', 'nginx_applied_at']);
        });
    }
};
