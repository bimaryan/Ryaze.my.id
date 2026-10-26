<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Info Update = pengumuman yang ditulis superadmin lalu dibaca user di
     * halaman /updates. Satu tabel dipakai untuk beberapa jenis info karena
     * bedanya cuma kategori:
     *
     *   feature      : fitur/changelog baru Ryaze
     *   maintenance  : jadwal maintenance atau downtime
     *   announcement : pengumuman umum / perubahan kebijakan
     *
     * Deskripsi boleh diisi manual atau dihasilkan AI (InfoUpdateAiWriter).
     */
    public function up(): void
    {
        Schema::create('info_updates', function (Blueprint $table) {
            $table->id();

            // Nullable: kalau akun superadmin dihapus, info update tetap utuh.
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');

            $table->string('category')->default('feature');
            $table->string('version')->nullable();
            $table->string('priority')->default('normal');

            $table->boolean('is_active')->default(true);
            // NULL = langsung tayang begitu disimpan (scheduled bila diisi).
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index(['is_active', 'published_at']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_updates');
    }
};