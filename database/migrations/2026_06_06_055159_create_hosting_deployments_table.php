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
        Schema::create('hosting_deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hosting_project_id')->constrained('hosting_projects')->onDelete('cascade');
            $table->string('commit_hash')->nullable();
            $table->string('commit_message')->nullable();
            $table->longText('build_logs')->nullable();
            $table->enum('status', ['queued', 'building', 'ready', 'failed'])->default('queued');
            $table->timestamp('deployed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_deployments');
    }
};
