<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('period');
            $table->string('role');
            $table->string('company');
            $table->string('type')->default('Full-time');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->default('fa-briefcase');
            $table->string('color')->default('#6366f1');
            $table->json('tags')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
