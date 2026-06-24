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
        Schema::table('hosting_projects', function (Blueprint $table) {
            $table->integer('storage_limit_mb')->default(1024)->after('force_https');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hosting_projects', function (Blueprint $table) {
            $table->dropColumn('storage_limit_mb');
        });
    }
};
