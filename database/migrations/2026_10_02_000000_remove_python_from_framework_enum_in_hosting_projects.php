<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Sebelum enum dipersempit, project Python yang masih ada harus ditangani
        // agar tidak menjadi data yatim (nilai di luar enum baru). Pipeline deploy
        // Python sudah dihapus, jadi project ini tidak bisa dideploy ulang — tandai
        // sebagai error agar pemiliknya ter-notifikasi dan bisa dipindahkan manual.
        DB::table('hosting_projects')
            ->where('framework', 'python')
            ->update(['framework' => 'html', 'status' => 'error']);

        DB::statement("ALTER TABLE hosting_projects MODIFY COLUMN framework ENUM('react', 'nextjs', 'html', 'laravel', 'node', 'php', 'vue', 'nuxtjs') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Mengembalikan nilai 'python' ke enum. Project yang sebelumnya diubah ke
        // 'html' TIDAK dikembalikan secara otomatis karena framework aslinya tidak
        // bisa ditebak dengan pasti.
        DB::statement("ALTER TABLE hosting_projects MODIFY COLUMN framework ENUM('react', 'nextjs', 'python', 'html', 'laravel', 'node', 'php', 'vue', 'nuxtjs') NOT NULL");
    }
};
