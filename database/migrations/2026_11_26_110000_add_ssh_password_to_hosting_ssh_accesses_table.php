<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosting_ssh_accesses', function (Blueprint $table) {
            $table->text('ssh_password')->nullable()->after('private_key');
        });
    }

    public function down(): void
    {
        Schema::table('hosting_ssh_accesses', function (Blueprint $table) {
            $table->dropColumn('ssh_password');
        });
    }
};
