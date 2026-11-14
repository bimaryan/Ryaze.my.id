<?php

namespace App\Console\Commands;

use App\Models\NodeApp;
use App\Services\NodeAppService;
use Illuminate\Console\Command;

class MonitorNodeApps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apps:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkron status & auto-restart aplikasi Node yang crash (via runtime agent)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = app(NodeAppService::class);

        if (!NodeAppService::available()) {
            $this->warn('Node runtime tidak tersedia (NODE_RUNTIME_URL belum dikonfigurasi).');

            return self::SUCCESS;
        }

        // Hanya aplikasi yang seharusnya jalan (running/starting) yang dipantau.
        // Aplikasi stopped/error yang sengaja di-stop user tidak di-restart.
        $apps = NodeApp::whereIn('status', ['running', 'starting'])
            ->with('project:id,user_id,ryaze_domain')
            ->get();

        $healed = 0;
        $failed = 0;

        foreach ($apps as $app) {
            $before = $app->status;
            $service->syncStatus($app, autoHeal: true);
            $after = $app->fresh();

            if ($before !== 'running' && $after->status === 'running') {
                $healed++;
                $this->info("Healed: {$after->project->ryaze_domain} ({$after->status})");
            }

            if ($after->status === 'error') {
                $failed++;
                $service->notifyOwner(
                    $after,
                    'Aplikasi Node ' . $after->project->ryaze_domain . ' tidak dapat dijalankan otomatis. Cek tab Log di halaman Aplikasi.',
                    'error',
                );
            }
        }

        if ($healed || $failed) {
            $this->info("Selesai: {$healed} aplikasi dibangkitkan ulang, {$failed} error.");
        } else {
            $this->info('Semua aplikasi Node dalam kondisi normal.');
        }

        return self::SUCCESS;
    }
}
