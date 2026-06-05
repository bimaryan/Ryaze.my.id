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
        Schema::create('joki_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('joki_orders')->onDelete('cascade');
            $table->text('revision_note')->comment('Catatan revisi dari klien');
            $table->enum('status', ['pending', 'fixing', 'resolved', 'rejected'])->default('pending');
            $table->text('admin_reply')->nullable()->comment('Tanggapan Klien/Dev');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('joki_revisions');
    }
};
