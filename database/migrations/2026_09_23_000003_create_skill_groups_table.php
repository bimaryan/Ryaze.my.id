<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_groups', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('icon')->default('fa-layer-group');
            $table->string('color')->default('#6366f1');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_groups');
    }
};
