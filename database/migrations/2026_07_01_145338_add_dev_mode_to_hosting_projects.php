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
            $table->boolean('dev_mode')->default(false)->after('force_https');
            $table->integer('dev_port')->nullable()->after('dev_mode');
            $table->text('dev_pid')->nullable()->after('dev_port');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hosting_projects', function (Blueprint $table) {
            $table->dropColumn(['dev_mode', 'dev_port', 'dev_pid']);
        });
    }
};
