<?php

namespace App\Console\Commands;

use App\Models\HostingProject;
use App\Models\NodeApp;
use App\Services\NodeAppService;
use Illuminate\Console\Command;

class MigrateLegacyNodeApps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apps:migrate-legacy-node {--start : Langsung start aplikasi yang tadinya berjalan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi project framework node lama (ryaze-start manual) ke sistem Aplikasi Node baru';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = app(NodeAppService::class);

        if (!NodeAppService::available()) {
            $this->error('Node runtime tidak tersedia. Isi NODE_RUNTIME_URL & NODE_RUNTIME_TOKEN di .env dulu.');

            return self::FAILURE;
        }

        $projects = HostingProject::where('framework', 'node')
            ->whereDoesntHave('nodeApp')
            ->get();

        if ($projects->isEmpty()) {
            $this->info('Tidak ada project node yang perlu dimigrasi.');

            return self::SUCCESS;
        }

        $this->info("Menemukan {$projects->count()} project node lama.");

        foreach ($projects as $project) {
            $subdomain = explode('.', $project->ryaze_domain)[0];
            $projectDir = rtrim((string) config('hosting.client_dir', '/www/sites/hosting_clients'), '/\\') . "/{$subdomain}";

            // Apakah proses lama masih hidup (ditulis NodeServerService ke .port)?
            $wasRunning = false;
            $portFile = "{$projectDir}/.port";
            if (file_exists($portFile)) {
                $port = (int) trim((string) @file_get_contents($portFile));
                if ($port > 0) {
                    $connection = @fsockopen('127.0.0.1', $port, $errno, $errstr, 1);
                    if ($connection) {
                        fclose($connection);
                        $wasRunning = true;
                    }
                }
            }

            $app = NodeApp::create([
                'hosting_project_id' => $project->id,
                'status' => $wasRunning && $this->option('start') ? 'starting' : 'stopped',
                'entry' => null,
                'node_env' => 'production',
            ]);

            $this->line("- {$project->ryaze_domain}: " . ($wasRunning ? 'proses lama HIDUP' : 'tidak berjalan'));

            if ($wasRunning && $this->option('start')) {
                $result = $service->start($app);
                $this->line($result['ok']
                    ? "  ✓ Migrasi sukses — jalan di port " . ($result['data']['port'] ?? '?') . ' (proses di runtime container)'
                    : "  ✗ Start gagal: " . ($result['error'] ?? '?') . " — start manual dari panel");
            }
        }

        $this->info('Selesai. Project yang belum jalan bisa di-start dari halaman Aplikasi.');

        return self::SUCCESS;
    }
}
