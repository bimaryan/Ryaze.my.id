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
        Schema::create('joki_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('joki_orders')->onDelete('cascade');
            $table->string('title')->comment('Contoh: Desain Database & ERD');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'working', 'done'])->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('joki_milestones');
    }
};
