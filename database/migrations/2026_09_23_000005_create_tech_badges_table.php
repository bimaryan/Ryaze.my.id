<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tech_badges', function (Blueprint $table) {
            $table->id();
            $table->string('icon');
            $table->string('label');
            $table->string('color')->default('#6366f1');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tech_badges');
    }
};
