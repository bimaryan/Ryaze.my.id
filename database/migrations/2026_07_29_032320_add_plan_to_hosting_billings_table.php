<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosting_billings', function (Blueprint $table) {
            $table->enum('plan', ['starter', 'pro', 'business'])->default('starter')->after('plan_name');
        });
    }

    public function down(): void
    {
        Schema::table('hosting_billings', function (Blueprint $table) {
            $table->dropColumn('plan');
        });
    }
};
