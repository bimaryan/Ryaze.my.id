<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * hosting_domains sebelumnya hanya bisa menyimpan domain root (butuh zone
     * Cloudflare sendiri). Sekarang satu tabel juga menampung:
     *
     *   - custom    : domain root milik user, punya zone + nameserver sendiri
     *   - subdomain : subdomain dari domain root miliknya sendiri
     *                  (blog.tokoanda.com -> mewarisi zone & status induknya)
     *   - platform  : subdomain gratis dari domain Ryaze
     *                  (toko.ryaze.my.id -> memakai zone utama platform)
     *
     * Subdomain tidak punya zone sendiri, jadi `cf_zone_id` & `nameservers`
     * dikosongkan dan diambil dari domain induk / config saat dibutuhkan.
     *
     * Migration ini idempoten: aman dijalankan ulang di schema yang sudah
     * sebagian berubah.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('hosting_domains', 'type')) {
            Schema::table('hosting_domains', function (Blueprint $table) {
                $table->string('type')->default('custom')->after('domain_name');
            });
        }

        if (! Schema::hasColumn('hosting_domains', 'parent_id')) {
            Schema::table('hosting_domains', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('type');
            });
        }

        // Domain yang sudah ada pasti domain root -> custom.
        DB::table('hosting_domains')->whereNull('type')->update(['type' => 'custom']);

        $this->backfillPlatformSubdomains();

        $this->ensureIndex('hosting_domains', 'parent_id', 'hosting_domains_parent_id_index');
        $this->ensureForeignKey();
    }

    /**
     * Seiwayat ada domain subdomain platform (mis. "silk.ryz.my.id") yang
     * masuk lewat alur custom domain lama: tercatat sebagai domain root tapi
     * tanpa cf_zone_id karena memang tidak pernah punya zone sendiri.
     *
     * Row seperti itu sebenarnya platform subdomain — tandai supaya ikut
     * memakai zone utama dan bisa membuat DNS record-nya.
     */
    private function backfillPlatformSubdomains(): void
    {
        $bases = (array) config('hosting.public_bases', []);

        if ($bases === []) {
            return;
        }

        DB::table('hosting_domains')
            ->whereNull('cf_zone_id')
            ->where(function ($query) use ($bases) {
                foreach ($bases as $base) {
                    $query->orWhere('domain_name', 'LIKE', '%.'.$base);
                }
            })
            ->update(['type' => \App\Models\HostingDomain::TYPE_PLATFORM]);
    }

    public function down(): void
    {
        $this->dropForeignKey();

        Schema::table('hosting_domains', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
            $table->dropColumn(['type', 'parent_id']);
        });
    }

    private function ensureIndex(string $table, string $column, string $index): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'mysql') {
            if (! $this->indexExists($connection, $table, $index)) {
                Schema::table($table, function (Blueprint $t) use ($column) {
                    $t->index($column);
                });
            }

            return;
        }

        if (! $this->indexExists($connection, $table, $index)) {
            $connection->statement("ALTER TABLE `{$table}` ADD INDEX `{$index}` (`{$column}`)");
        }
    }

    private function indexExists($connection, string $table, string $index): bool
    {
        if ($connection->getDriverName() !== 'mysql') {
            return (bool) $connection->selectOne(
                "SELECT name FROM sqlite_master WHERE type='index' AND tbl_name = ? AND name = ?",
                [$table, $index]
            );
        }

        return (bool) $connection->selectOne(
            'SELECT INDEX_NAME FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$table, $index]
        );
    }

    /**
     * Hapus domain root harus ikut menghapus subdomain-nya di level DB juga,
     * bukan cuma di aplikasi — supaya tidak ada row yatim kalau subdomain
     * dihapus lewat jalur lain (mis. cascade dari user).
     */
    private function ensureForeignKey(): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'mysql') {
            return;
        }

        if ($this->foreignKeyExists('hosting_domains_parent_id_foreign')) {
            return;
        }

        $connection->statement(
            'ALTER TABLE `hosting_domains` ADD CONSTRAINT `hosting_domains_parent_id_foreign`
             FOREIGN KEY (`parent_id`) REFERENCES `hosting_domains` (`id`) ON DELETE CASCADE'
        );
    }

    private function dropForeignKey(): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'mysql') {
            return;
        }

        if ($this->foreignKeyExists('hosting_domains_parent_id_foreign')) {
            $connection->statement(
                'ALTER TABLE `hosting_domains` DROP FOREIGN KEY `hosting_domains_parent_id_foreign`'
            );
        }
    }

    private function foreignKeyExists(string $constraint): bool
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'mysql') {
            return false;
        }

        return (bool) $connection->selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            ['hosting_domains', $constraint]
        );
    }
};