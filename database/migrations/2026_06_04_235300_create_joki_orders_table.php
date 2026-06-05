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
        Schema::create('joki_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            // Relasi ke tabel users (Klien)
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');

            // Relasi ke tabel users (Admin/Dev yang ngerjain), dibiarkan null dulu sebelum diambil alih
            $table->foreignId('worker_id')->nullable()->constrained('users')->nullOnDelete();

            // Relasi ke layanan
            $table->foreignId('service_id')->constrained('joki_services')->onDelete('restrict');

            // Detail Proyek
            $table->string('project_name');
            $table->text('description');
            $table->string('tech_stack')->nullable()->comment('Misal: Laravel 12, React, Tailwind');

            // Status dan Progres
            $table->enum('status', ['pending', 'progress', 'review', 'completed', 'canceled'])->default('pending');
            $table->integer('progress')->default(0)->comment('Persentase 0 sampai 100');

            // Finansial dan Waktu
            $table->integer('price')->nullable()->comment('Harga deal setelah nego');
            $table->date('deadline')->nullable();

            // Deliverables (Hasil Kerja)
            $table->string('repo_link')->nullable()->comment('Link GitHub untuk klien');
            $table->string('demo_link')->nullable()->comment('Link hosting staging/demo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('joki_orders');
    }
};
