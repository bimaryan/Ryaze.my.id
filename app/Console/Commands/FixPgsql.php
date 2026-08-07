<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;

class FixPgsql extends Command
{
    protected $signature = 'app:fix-pgsql';
    protected $description = 'Fix PostgreSQL database permissions to hide from other users';

    public function handle()
    {
        $pgHost = config('services.panel_pgsql.host');
        $pgPort = config('services.panel_pgsql.port');
        $pgUser = config('services.panel_pgsql.user');
        $pgPass = config('services.panel_pgsql.password');

        if (empty($pgHost) || empty($pgUser) || empty($pgPass)) {
            $this->error('PANEL_PGSQL_HOST / PANEL_PGSQL_USER / PANEL_PGSQL_PASSWORD harus diatur di .env');
            return 1;
        }

        try {
            $pdo = new PDO("pgsql:host={$pgHost};port={$pgPort};dbname=postgres", $pgUser, $pgPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $dbs = $pdo->query("SELECT datname FROM pg_database")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($dbs as $db) {
                $this->info("Revoking PUBLIC access from $db...");
                $pdo->exec("REVOKE ALL ON DATABASE \"$db\" FROM PUBLIC");
            }
            $this->info("All databases hidden from PUBLIC successfully.");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
