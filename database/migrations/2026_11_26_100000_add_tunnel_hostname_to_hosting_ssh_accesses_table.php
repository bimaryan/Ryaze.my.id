<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosting_ssh_accesses', function (Blueprint $table) {
            $table->string('tunnel_hostname', 150)->nullable()->after('host_port');
        });
    }

    public function down(): void
    {
        Schema::table('hosting_ssh_accesses', function (Blueprint $table) {
            $table->dropColumn('tunnel_hostname');
        });
    }
};
