<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE hosting_billings MODIFY COLUMN plan ENUM('free', 'starter', 'pro', 'business') NOT NULL DEFAULT 'free'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE hosting_billings MODIFY COLUMN plan ENUM('starter', 'pro', 'business') NOT NULL DEFAULT 'starter'");
    }
};
