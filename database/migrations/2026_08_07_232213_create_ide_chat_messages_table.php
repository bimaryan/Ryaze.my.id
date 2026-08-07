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
        Schema::create('ide_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ide_chat_id')->constrained()->cascadeOnDelete();
            $table->string('role'); // user | assistant
            $table->longText('content');
            $table->timestamps();

            $table->index(['ide_chat_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ide_chat_messages');
    }
};
