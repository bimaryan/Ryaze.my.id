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
        Schema::create('hosting_environments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hosting_project_id')->constrained('hosting_projects')->onDelete('cascade');
            $table->string('env_key');
            $table->text('env_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_environments');
    }
};
