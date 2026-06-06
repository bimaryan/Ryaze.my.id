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
        Schema::create('hosting_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hosting_project_id')->constrained('hosting_projects')->onDelete('cascade');
            $table->string('plan_name');
            $table->integer('amount');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->date('next_due_date');
            $table->enum('status', ['active', 'past_due', 'canceled'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_billings');
    }
};
