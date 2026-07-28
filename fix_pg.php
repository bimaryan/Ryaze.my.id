<?php
$pgHost = '172.18.0.12';
$pgPort = '5432';
$pgUser = 'Bimaryan';
$pgPass = '@Bimaryan2329';

try {
    $pdo = new PDO("pgsql:host={$pgHost};port={$pgPort};dbname=postgres", $pgUser, $pgPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbs = $pdo->query("SELECT datname FROM pg_database")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($dbs as $db) {
        echo "Revoking PUBLIC access from $db...\n";
        $pdo->exec("REVOKE ALL ON DATABASE \"$db\" FROM PUBLIC");
    }
    echo "All databases hidden from PUBLIC.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
