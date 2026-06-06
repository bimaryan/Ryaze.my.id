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
        Schema::create('hosting_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pemilik proyek
            $table->string('project_name')->unique();
            $table->enum('framework', ['react', 'nextjs', 'python', 'html', 'laravel', 'node']);
            $table->string('repo_source')->nullable(); // Link github
            $table->string('branch')->default('main');
            $table->string('ryaze_domain')->unique()->nullable(); // subdomain.ryaze.my.id
            $table->string('custom_domain')->unique()->nullable();
            $table->enum('status', ['active', 'building', 'suspended', 'error'])->default('building');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_projects');
    }
};
