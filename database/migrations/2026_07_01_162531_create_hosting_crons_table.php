<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosting_crons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('hosting_projects')->cascadeOnDelete();
            $table->string('command');
            $table->string('schedule_expression')->default('* * * * *');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosting_crons');
    }
};
