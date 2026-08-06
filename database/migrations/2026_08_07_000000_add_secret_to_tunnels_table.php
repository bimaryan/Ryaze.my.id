<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tunnels', function (Blueprint $table) {
            $table->string('secret', 64)->nullable()->after('subdomain');
        });

        // Backfill secret untuk tunnel yang sudah ada
        \Illuminate\Support\Facades\DB::table('tunnels')->whereNull('secret')->orderBy('id')->chunkById(100, function ($tunnels) {
            foreach ($tunnels as $tunnel) {
                \Illuminate\Support\Facades\DB::table('tunnels')->where('id', $tunnel->id)->update([
                    'secret' => Str::random(32),
                ]);
            }
        });

        Schema::table('tunnels', function (Blueprint $table) {
            $table->string('secret', 64)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('tunnels', function (Blueprint $table) {
            $table->dropColumn('secret');
        });
    }
};
