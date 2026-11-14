<?php

namespace App\Jobs;

use App\Models\NodeApp;
use App\Services\NodeAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Build + start aplikasi Node di runtime container (dijalankan di background
 * karena npm install bisa berlangsung menit-menit).
 */
class DeployNodeApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800; // 30 menit

    public int $tries = 1;

    public function __construct(public NodeApp $app) {}

    public function handle(): void
    {
        $service = app(NodeAppService::class);
        $result = $service->deploy($this->app);

        $this->app->update([
            'last_build_log' => $result['output'] ?? ($result['error'] ?? null),
        ]);

        if ($result['ok']) {
            $this->service_notify($service, 'Aplikasi Node ' . ($this->app->project->ryaze_domain) . ' berhasil di-build & di-start.', 'success');
        } else {
            $this->service_notify($service, 'Deploy aplikasi Node ' . ($this->app->project->ryaze_domain) . ' gagal: ' . ($result['error'] ?? 'tidak diketahui') . '. Cek tab Log Aplikasi.', 'error');
            Log::warning('DeployNodeApp gagal', ['app_id' => $this->app->id, 'error' => $result['error'] ?? null]);
        }
    }

    private function service_notify(NodeAppService $service, string $message, string $type): void
    {
        $service->notifyOwner($this->app, $message, $type);
    }
}
