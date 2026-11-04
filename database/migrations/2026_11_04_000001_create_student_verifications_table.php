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
        Schema::create('student_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nim')->unique();
            $table->string('pddikti_id')->nullable();
            $table->string('nama_input');
            $table->string('nama_pddikti');
            $table->string('nama_pt');
            $table->string('nama_prodi')->nullable();
            $table->string('jenjang')->nullable();
            $table->string('pddikti_status', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('verified_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_verifications');
    }
};
