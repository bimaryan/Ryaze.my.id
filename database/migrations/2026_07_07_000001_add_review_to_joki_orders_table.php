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
        Schema::table('joki_orders', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('status')->comment('1 to 5 stars');
            $table->text('review')->nullable()->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('joki_orders', function (Blueprint $table) {
            $table->dropColumn(['rating', 'review']);
        });
    }
};
