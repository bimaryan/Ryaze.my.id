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
        Schema::table('tunnels', function (Blueprint $table) {
            $table->string('custom_domain')->nullable()->unique()->after('subdomain');
            $table->string('auth_username')->nullable()->after('target_port');
            $table->string('auth_password')->nullable()->after('auth_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tunnels', function (Blueprint $table) {
            $table->dropColumn(['custom_domain', 'auth_username', 'auth_password']);
        });
    }
};
