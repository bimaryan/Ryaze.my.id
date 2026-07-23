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
        Schema::create('apk_builds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('app_name');
            $table->string('app_url');
            $table->string('package_name');
            $table->string('icon_path')->nullable();
            $table->enum('status', ['pending', 'building', 'success', 'failed'])->default('pending');
            $table->string('apk_path')->nullable();
            $table->longText('log_output')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apk_builds');
    }
};
