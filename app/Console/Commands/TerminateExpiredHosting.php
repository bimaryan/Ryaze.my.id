<?php

namespace App\Console\Commands;

use App\Models\HostingDatabase;
use App\Models\HostingNosqlDatabase;
use App\Models\HostingPgsqlDatabase;
use App\Models\HostingProject;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TerminateExpiredHosting extends Command
{
    protected $signature = 'hosting:terminate-suspended
                            {--days=30 : Hapus project yang sudah suspended lebih dari N hari}
                            {--dry-run : Pratinjau tanpa menghapus apapun}';

    protected $description = 'Hapus secara permanen project hosting yang sudah suspended melewati batas waktu';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        if ($days < 1) {
            $this->error('Parameter --days harus lebih dari 0.');

            return 1;
        }

        $cutoff = Carbon::now()->subDays($days);
        $projects = HostingProject::where('status', 'suspended')
            ->where('updated_at', '<', $cutoff)
            ->get();

        if ($projects->isEmpty()) {
            $this->info("Tidak ada project suspended yang melebihi {$days} hari.");

            return 0;
        }

        $this->info(($dryRun ? '[DRY-RUN] ' : '')."Ditemukan {$projects->count()} project yang akan di-terminate.");
        $this->newLine();

        $terminated = 0;

        foreach ($projects as $project) {
            $subdomain = explode('.', $project->ryaze_domain)[0];
            $projectDir = hosting_clients_dir()."/{$subdomain}";
            $user = $project->client;
            $userName = $user?->name ?? 'Unknown';

            $this->line("─── Project: {$project->project_name} ({$project->ryaze_domain}) ───");
            $this->line("  User: {$userName} | Suspended sejak: {$project->updated_at->format('d M Y H:i')}");

            if ($dryRun) {
                $this->line('  [DRY-RUN] Akan menghentikan proses background.');
                $this->line('  [DRY-RUN] Akan menghapus DNS Cloudflare.');
                $this->line("  [DRY-RUN] Akan menghapus direktori: {$projectDir}");
                $this->line('  [DRY-RUN] Akan menghapus database MySQL terkait.');
                $this->line('  [DRY-RUN] Akan menghapus database PostgreSQL terkait.');
                $this->line('  [DRY-RUN] Akan menghapus database NoSQL terkait.');
                $this->line('  [DRY-RUN] Akan menghapus relasi (cron, domain, env, deployment, project).');
                $this->newLine();
                $terminated++;

                continue;
            }

            $this->stopBackgroundProcesses($project);
            $this->deleteCloudflareDns($project->ryaze_domain);
            $this->deletePhysicalDirectory($projectDir);
            $this->deleteDatabases($project);
            $this->deleteProjectRelations($project);

            Log::info("[TerminateExpiredHosting] Project terminated: {$project->project_name} ({$project->ryaze_domain})", [
                'project_id' => $project->id,
                'user_id' => $project->user_id,
                'user_name' => $userName,
            ]);

            $terminated++;
            $this->line('  ✅ Project berhasil di-terminate.');
            $this->newLine();
        }

        $this->info(($dryRun ? '[DRY-RUN] ' : '')."Selesai. Total {$terminated} project di-terminate.");

        return 0;
    }

    private function stopBackgroundProcesses(HostingProject $project): void
    {
        if (! $project->dev_pid) {
            return;
        }

        $pid = escapeshellarg($project->dev_pid);
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            if (is_numeric($project->dev_pid)) {
                exec("kill -9 {$pid} 2>/dev/null || true");
            }
            exec("pm2 delete {$pid} 2>/dev/null || true");
            exec("pm2 delete \"prod_{$project->id}\" 2>/dev/null || true");
        }
    }

    private function deleteCloudflareDns(string $domainName): void
    {
        $apiToken = config('services.cloudflare.api_token');
        if (! $apiToken) {
            return;
        }

        try {
            $zoneName = explode('.', $domainName, 2)[1] ?? $domainName;
            $zoneId = config('services.cloudflare.zone_id');
            $zoneReq = Http::withToken($apiToken)
                ->get('https://api.cloudflare.com/client/v4/zones', ['name' => $zoneName]);
            if ($zoneReq->successful() && ! empty($zoneReq->json('result'))) {
                $zoneId = $zoneReq->json('result.0.id');
            }

            if (! $zoneId) {
                return;
            }

            $response = Http::withToken($apiToken)
                ->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                    'type' => 'CNAME',
                    'name' => $domainName,
                ]);

            if ($response->successful() && ! empty($response->json('result'))) {
                $recordId = $response->json('result.0.id');
                Http::withToken($apiToken)
                    ->delete("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records/{$recordId}");
                $this->line('  ✓ DNS Cloudflare dihapus.');
            }
        } catch (\Throwable $e) {
            $this->line('  ⚠ Gagal menghapus DNS Cloudflare: '.$e->getMessage());
        }
    }

    private function deletePhysicalDirectory(string $projectDir): void
    {
        if (! is_dir($projectDir)) {
            $this->line('  ✓ Direktori tidak ditemukan (sudah terhapus).');

            return;
        }

        try {
            File::deleteDirectory($projectDir);
            $this->line("  ✓ Direktori fisik dihapus: {$projectDir}");
        } catch (\Throwable $e) {
            $this->line('  ⚠ Gagal menghapus direktori: '.$e->getMessage());
        }
    }

    private function deleteDatabases(HostingProject $project): void
    {
        $this->deleteMysqlDatabases($project);
        $this->deletePgsqlDatabases($project);
        $this->deleteNosqlDatabases($project);
    }

    private function deleteMysqlDatabases(HostingProject $project): void
    {
        $databases = HostingDatabase::where('user_id', $project->user_id)->get();

        foreach ($databases as $db) {
            try {
                $pdo = new \PDO(
                    "mysql:host={$db->host};port={$db->port}",
                    config('database.connections.mysql.username'),
                    config('database.connections.mysql.password')
                );
                $pdo->exec("DROP DATABASE IF EXISTS `{$db->db_name}`");
                $db->delete();
                $this->line("  ✓ MySQL database '{$db->db_name}' dihapus.");
            } catch (\Throwable $e) {
                $this->line("  ⚠ Gagal menghapus MySQL database '{$db->db_name}': ".$e->getMessage());
            }
        }
    }

    private function deletePgsqlDatabases(HostingProject $project): void
    {
        $databases = HostingPgsqlDatabase::where('user_id', $project->user_id)->get();

        foreach ($databases as $db) {
            try {
                $pdo = new \PDO(
                    "pgsql:host={$db->host};port={$db->port};dbname=postgres",
                    config('database.connections.pgsql.username'),
                    config('database.connections.pgsql.password')
                );
                $pdo->exec("DROP DATABASE IF EXISTS \"{$db->db_name}\"");
                $db->delete();
                $this->line("  ✓ PostgreSQL database '{$db->db_name}' dihapus.");
            } catch (\Throwable $e) {
                $this->line("  ⚠ Gagal menghapus PostgreSQL database '{$db->db_name}': ".$e->getMessage());
            }
        }
    }

    private function deleteNosqlDatabases(HostingProject $project): void
    {
        $databases = HostingNosqlDatabase::where('user_id', $project->user_id)->get();

        foreach ($databases as $db) {
            try {
                $db->delete();
                $this->line("  ✓ NoSQL database '{$db->db_username}' dihapus.");
            } catch (\Throwable $e) {
                $this->line("  ⚠ Gagal menghapus NoSQL database '{$db->db_username}': ".$e->getMessage());
            }
        }
    }

    private function deleteProjectRelations(HostingProject $project): void
    {
        $project->crons()->delete();
        $project->domains()->delete();
        $project->environments()->delete();
        $project->deployments()->delete();
        $project->delete();
        $this->line('  ✓ Semua relasi dan record project dihapus.');
    }
}
