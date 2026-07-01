<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosting_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('hosting_projects')->cascadeOnDelete();
            $table->string('domain_name')->unique();
            $table->string('ssl_status')->default('pending'); // pending, active, failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosting_domains');
    }
};
