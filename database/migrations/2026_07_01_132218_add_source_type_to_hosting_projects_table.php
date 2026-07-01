<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosting_projects', function (Blueprint $table) {
            // 'repo' = dari git repository user, 'template' = dari starter template Ryaze
            $table->enum('source_type', ['repo', 'template'])->default('repo')->after('branch');
        });
    }

    public function down(): void
    {
        Schema::table('hosting_projects', function (Blueprint $table) {
            $table->dropColumn('source_type');
        });
    }
};
