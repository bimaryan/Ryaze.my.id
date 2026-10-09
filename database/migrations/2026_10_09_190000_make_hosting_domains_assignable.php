<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Domain dibuat lebih dulu tanpa project (ala Vercel), lalu di-assign
     * belakangan lewat tab Domains di halaman project.
     *
     * Karena itu project_id jadi nullable, punya user_id sendiri, dan cascade
     * delete project TIDAK lagi ikut menghapus domain.
     *
     * Migration ini idempoten: dijalankan ulang di schema yang sudah sebagian
     * berubah tetap aman.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('hosting_domains', 'user_id')) {
            Schema::table('hosting_domains', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            });
        }

        // Backfill user_id dari project yang sedang terhubung.
        \App\Models\HostingDomain::query()
            ->whereNull('user_id')
            ->whereNotNull('project_id')
            ->with('project')
            ->chunk(200, function ($domains) {
                foreach ($domains as $domain) {
                    $domain->forceFill(['user_id' => $domain->project?->user_id])->saveQuietly();
                }
            });

        Schema::table('hosting_domains', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            // project_id jadi nullable: hapus project tidak ikut menghapus
            // domain, domain jadi orphan dan bisa di-assign ulang.
            $table->unsignedBigInteger('project_id')->nullable()->change();
        });

        $this->ensureIndex('hosting_domains', 'user_id', 'hosting_domains_user_id_index');

        // Ganti cascadeOnDelete -> nullOnDelete. MySQL butuh nama FK yang
        // eksplisit saat constraint sudah ada.
        $this->replaceForeignKey('hosting_domains_project_id_foreign', 'project_id');
    }

    private function ensureIndex(string $table, string $column, string $index): void
    {
        $connection = \Illuminate\Support\Facades\DB::connection();

        if ($connection->getDriverName() !== 'mysql') {
            \Illuminate\Support\Facades\Schema::table($table, function (Blueprint $t) use ($column) {
                $t->index($column);
            });

            return;
        }

        $exists = $connection->selectOne(
            'SELECT INDEX_NAME FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$table, $index]
        );

        if (! $exists) {
            $connection->statement("ALTER TABLE `{$table}` ADD INDEX `{$index}` (`{$column}`)");
        }
    }

    public function down(): void
    {
        Schema::table('hosting_domains', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });

        $this->replaceForeignKey('hosting_domains_project_id_foreign', 'project_id');

        Schema::table('hosting_domains', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    private function replaceForeignKey(string $constraint, string $column): void
    {
        $schema = \Illuminate\Support\Facades\DB::getSchemaBuilder();
        $table = 'hosting_domains';
        $connection = \Illuminate\Support\Facades\DB::connection();

        if ($connection->getDriverName() !== 'mysql') {
            return;
        }

        $exists = $connection->selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$table, $constraint]
        );

        if ($exists) {
            $connection->statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
        }

        $connection->statement(
            "ALTER TABLE `{$table}` ADD CONSTRAINT `{$constraint}`
             FOREIGN KEY (`{$column}`) REFERENCES `hosting_projects` (`id`) ON DELETE SET NULL"
        );
    }
};