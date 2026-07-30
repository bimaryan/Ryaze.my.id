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
        Schema::table('hosting_domains', function (Blueprint $table) {
            $table->string('cf_zone_id')->nullable()->after('ssl_status');
            $table->json('nameservers')->nullable()->after('cf_zone_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hosting_domains', function (Blueprint $table) {
            $table->dropColumn(['cf_zone_id', 'nameservers']);
        });
    }
};
