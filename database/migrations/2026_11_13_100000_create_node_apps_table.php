<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('node_apps', function (Blueprint $table) {
            $table->id();
            // Satu project = satu aplikasi Node
            $table->foreignId('hosting_project_id')->unique()->constrained('hosting_projects')->cascadeOnDelete();
            // stopped | starting | running | error
            $table->string('status', 20)->default('stopped');
            // Entry file relatif (kosong = auto-detect oleh agent)
            $table->string('entry')->nullable();
            $table->unsignedInteger('port')->nullable();
            $table->string('node_env', 20)->default('production');
            // Environment variables tambahan (key => value)
            $table->json('env_vars')->nullable();
            $table->text('last_error')->nullable();
            // Output build terakhir (npm install, dll) untuk ditampilkan di panel
            $table->text('last_build_log')->nullable();
            $table->unsignedInteger('restart_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('node_apps');
    }
};
