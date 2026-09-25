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
        Schema::table('users', function (Blueprint $table) {
            // Make role nullable so Google OAuth users can register without choosing a role
            $table->string('role')->nullable()->change();

            // Add google_id and provider if they don't exist yet
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'provider')) {
                $table->string('provider')->nullable()->after('google_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable(false)->change();
        });
    }
};
