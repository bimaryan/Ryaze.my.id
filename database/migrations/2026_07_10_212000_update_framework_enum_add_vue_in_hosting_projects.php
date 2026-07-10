<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE hosting_projects MODIFY COLUMN framework ENUM('react', 'nextjs', 'python', 'html', 'laravel', 'node', 'php', 'vue', 'nuxtjs') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this might cause data loss if there are 'vue' rows, so we just remove 'vue' from enum definition
        DB::statement("ALTER TABLE hosting_projects MODIFY COLUMN framework ENUM('react', 'nextjs', 'python', 'html', 'laravel', 'node', 'php') NOT NULL");
    }
};
