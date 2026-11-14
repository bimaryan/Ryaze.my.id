<?php

namespace App\Services;

use App\Models\HostingProject;
use App\Models\NodeApp;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Control plane aplikasi Node milik klien.
 *
 * Semua operasi (build/start/stop/restart/status/log) didelegasikan ke agent
 * HTTP di container runtime `ryaze-node`. Panel PHP tidak pernah men-spawn
 * proses Node. Lihat docker/node-agent/agent.js untuk kontrak endpoint.
 */
class NodeAppService
{
    /**
     * Apakah runtime Node tersedia? (URL & token terisi di .env)
     */
    public static function available(): bool
    {
        return (bool) config('services.node_runtime.url') && (bool) config('services.node_runtime.token');
    }

    public function client(int $timeoutSeconds = 60): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'X-Agent-Token' => (string) config('services.node_runtime.token'),
        ])->timeout($timeoutSeconds)->acceptJson();
    }

    /**
     * Ambil atau buat record aplikasi untuk sebuah project.
     */
    public function appFor(HostingProject $project, bool $create = true): ?NodeApp
    {
        $app = NodeApp::where('hosting_project_id', $project->id)->first();

        if (!$app && $create) {
            $app = NodeApp::create([
                'hosting_project_id' => $project->id,
                'status' => 'stopped',
                'entry' => null,
                'node_env' => 'production',
            ]);
        }

        return $app;
    }

    /**
     * Path project di sisi container agent (volume bersama).
     */
    public function projectDir(HostingProject $project): string
    {
        $subdomain = explode('.', $project->ryaze_domain)[0];

        return rtrim(hosting_clients_dir(), '/\\') . '/' . $subdomain;
    }

    /**
     * Build aplikasi (npm install dsb) di container node.
     *
     * @param  array<int, string>|null  $commands
     * @return array{ok: bool, output?: string, error?: string}
     */
    public function build(NodeApp $app, ?array $commands = null): array
    {
        if (!self::available()) {
            return ['ok' => false, 'error' => 'Node runtime tidak tersedia (NODE_RUNTIME_URL / NODE_RUNTIME_TOKEN belum diisi).'];
        }

        $project = $app->project;

        try {
            $response = $this->client((int) config('services.node_runtime.build_timeout', 900))
                ->post(rtrim(config('services.node_runtime.url'), '/') . '/build', array_filter([
                    'project' => $this->projectDir($project),
                    'commands' => $commands,
                ]));
        } catch (\Throwable $e) {
            Log::error('NodeApp build error: ' . $e->getMessage(), ['app_id' => $app->id]);
            return ['ok' => false, 'error' => 'Tidak dapat menghubungi node runtime: ' . $e->getMessage()];
        }

        $data = $response->json() ?: [];

        return [
            'ok' => (bool) ($data['ok'] ?? false),
            'output' => $data['output'] ?? null,
            'error' => $data['error'] ?? (!$response->successful() ? 'Build gagal (HTTP ' . $response->status() . ')' : null),
        ];
    }

    /**
     * Start / restart / stop aplikasi, dan sinkronkan status DB dengan hasil agent.
     *
     * @return array{ok: bool, error?: string, data?: array}
     */
    public function start(NodeApp $app): array
    {
        if (!self::available()) {
            return ['ok' => false, 'error' => 'Node runtime tidak tersedia.'];
        }

        $app->update(['status' => 'starting', 'last_error' => null]);
        $project = $app->project;

        try {
            $response = $this->client()->post(rtrim(config('services.node_runtime.url'), '/') . '/start', [
                'project' => $this->projectDir($project),
                'entry' => $app->entry ?: null,
                'env_vars' => $app->env_vars ?: null,
                'node_env' => $app->node_env ?: 'production',
            ]);
        } catch (\Throwable $e) {
            $error = 'Tidak dapat menghubungi node runtime: ' . $e->getMessage();
            $app->update(['status' => 'error', 'last_error' => $error]);
            Log::error('NodeApp start error: ' . $e->getMessage(), ['app_id' => $app->id]);

            return ['ok' => false, 'error' => $error];
        }

        $data = $response->json() ?: [];

        if (!($data['ok'] ?? false)) {
            $error = $data['error'] ?? 'Start gagal (HTTP ' . $response->status() . ')';
            $app->update(['status' => 'error', 'last_error' => $error]);

            return ['ok' => false, 'error' => $error];
        }

        $app->update([
            'status' => ($data['listening'] ?? true) ? 'running' : 'error',
            'port' => $data['port'] ?? null,
            'started_at' => now(),
            'restart_count' => 0,
            'last_error' => $data['warning'] ?? null,
        ]);

        return ['ok' => true, 'data' => $data];
    }

    public function stop(NodeApp $app): array
    {
        if (!self::available()) {
            return ['ok' => false, 'error' => 'Node runtime tidak tersedia.'];
        }

        $project = $app->project;

        try {
            $response = $this->client()->post(rtrim(config('services.node_runtime.url'), '/') . '/stop', [
                'project' => $this->projectDir($project),
            ]);
        } catch (\Throwable $e) {
            Log::error('NodeApp stop error: ' . $e->getMessage(), ['app_id' => $app->id]);

            return ['ok' => false, 'error' => 'Tidak dapat menghubungi node runtime: ' . $e->getMessage()];
        }

        if (!($response->json('ok') ?? false)) {
            return ['ok' => false, 'error' => $response->json('error') ?? 'Stop gagal (HTTP ' . $response->status() . ')'];
        }

        $app->update(['status' => 'stopped', 'port' => null, 'started_at' => null, 'last_error' => null]);

        return ['ok' => true];
    }

    /**
     * Sinkronkan status DB dengan kondisi riil di agent (dipakai apps:monitor).
     */
    public function syncStatus(NodeApp $app, bool $autoHeal = true): NodeApp
    {
        if (!self::available()) {
            return $app;
        }

        $project = $app->project;

        try {
            $response = $this->client(30)->get(rtrim(config('services.node_runtime.url'), '/') . '/status', [
                'project' => $this->projectDir($project),
            ]);
        } catch (\Throwable $e) {
            return $app; // runtime unreachable — jangan mengubah status sembarangan
        }

        $data = $response->json() ?: [];
        if (!($data['ok'] ?? false)) {
            return $app;
        }

        $running = (bool) ($data['running'] ?? false);
        $newStatus = $running ? 'running' : ($app->status === 'error' ? 'error' : 'stopped');
        $updates = [
            'port' => $data['port'] ?? $app->port,
        ];

        if ($newStatus !== $app->status) {
            if ($autoHeal && !$running && $app->status === 'running') {
                // Aplikasi crash → bangkitkan lagi, batasi usaha agar tidak loop tak terbatas
                if ($app->restart_count < 3) {
                    $app->increment('restart_count');
                    $app->update($updates);
                    $this->restart($app);

                    return $app->fresh();
                }

                $newStatus = 'error';
                $app->update(['last_error' => 'Aplikasi crash berulang. Cek tab Logs lalu jalankan ulang setelah diperbaiki.']);
            }

            if ($newStatus === 'running') {
                $updates['started_at'] = $app->started_at ?? now();
                $updates['restart_count'] = 0;
            }

            $updates['status'] = $newStatus;
            $app->update($updates);
        } else {
            $app->update($updates);
        }

        return $app->fresh();
    }

    public function restart(NodeApp $app): array
    {
        if (!self::available()) {
            return ['ok' => false, 'error' => 'Node runtime tidak tersedia.'];
        }

        $project = $app->project;

        try {
            $response = $this->client()->post(rtrim(config('services.node_runtime.url'), '/') . '/restart', [
                'project' => $this->projectDir($project),
                'entry' => $app->entry ?: null,
                'env_vars' => $app->env_vars ?: null,
                'node_env' => $app->node_env ?: 'production',
            ]);
        } catch (\Throwable $e) {
            Log::error('NodeApp restart error: ' . $e->getMessage(), ['app_id' => $app->id]);

            return ['ok' => false, 'error' => 'Tidak dapat menghubungi node runtime: ' . $e->getMessage()];
        }

        $data = $response->json() ?: [];

        if (!($data['ok'] ?? false)) {
            $app->update(['status' => 'error', 'last_error' => $data['error'] ?? 'Restart gagal (HTTP ' . $response->status() . ')']);

            return ['ok' => false, 'error' => $data['error'] ?? 'Restart gagal'];
        }

        $app->update([
            'status' => ($data['listening'] ?? true) ? 'running' : 'error',
            'port' => $data['port'] ?? null,
            'started_at' => now(),
            'last_error' => $data['warning'] ?? null,
        ]);

        return ['ok' => true, 'data' => $data];
    }

    public function logs(NodeApp $app, int $lines = 200): ?string
    {
        if (!self::available()) {
            return null;
        }

        try {
            $response = $this->client(30)->get(rtrim(config('services.node_runtime.url'), '/') . '/logs', [
                'project' => $this->projectDir($app->project),
                'lines' => $lines,
            ]);
        } catch (\Throwable $e) {
            return null;
        }

        return $response->json('logs');
    }

    /**
     * Build lalu start (alur deploy penuh dari AutoDeployProject).
     */
    public function deploy(NodeApp $app): array
    {
        $build = $this->build($app);
        if (!$build['ok']) {
            $app->update(['status' => 'error', 'last_error' => $build['error'] ?? 'Build gagal']);

            return ['ok' => false, 'error' => $build['error'] ?? 'Build gagal', 'output' => $build['output'] ?? null];
        }

        return $this->start($app);
    }

    /**
     * Notifikasi ke pemilik project.
     */
    public function notifyOwner(NodeApp $app, string $message, string $type = 'info'): void
    {
        $user = $app->project->user;
        $user?->notify(new SystemNotification($message, $type));
    }
}
