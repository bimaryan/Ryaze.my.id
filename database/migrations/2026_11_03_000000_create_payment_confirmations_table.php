<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_confirmations', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->index();
            $table->string('order_type');
            $table->integer('amount');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->string('channel')->default('qris_dynamic');
            $table->string('status')->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('admin_notified_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'order_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_confirmations');
    }
};
