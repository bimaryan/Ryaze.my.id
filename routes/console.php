<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('hosting:suspend-expired')->dailyAt('00:00');
Schedule::command('blog:generate --scheduled')->everyMinute();

// Load custom cron jobs from database
try {
    if (\Illuminate\Support\Facades\Schema::hasTable('hosting_crons')) {
        $crons = \App\Models\HostingCron::with('project')->where('is_active', true)->get();
        foreach ($crons as $cron) {
            if (!$cron->project) {
                continue;
            }

            // Lokasi project disimpan per subdomain (bukan project_id)
            $subdomain = explode('.', $cron->project->ryaze_domain)[0];
            $projectPath = hosting_clients_dir() . '/' . $subdomain;
            if (!file_exists($projectPath)) {
                $projectPath = storage_path('app/hosting_clients/' . $subdomain);
            }

            // ════ SECURITY: command cron harus dari allowlist & tanpa shell metachar ════
            $command = trim($cron->command);
            if (empty($command)) {
                continue;
            }

            // Deny metacharacter shell (no chaining, redirection, substitution, glob, quote-breaking)
            $unsafeMeta = [';', '&', '`', '|', '<', '>', '$', '(', ')', '{', '}', '[', ']', '*', '?', '!', '~', '#', '"', "'", '\\', "\n"];
            foreach ($unsafeMeta as $meta) {
                if (str_contains($command, $meta)) {
                    \Illuminate\Support\Facades\Log::warning('[CRON_BLOCKED] Cron command mengandung metachar dilarang: ' . $command);
                    continue 2;
                }
            }

            $allowedCronCommands = [
                'ls', 'cat', 'head', 'tail', 'wc', 'grep', 'find', 'echo', 'pwd', 'date',
                'php', 'composer', 'npm', 'npx', 'node', 'python', 'python3', 'pip', 'pip3',
                'mkdir', 'touch', 'cp', 'mv', 'rm', 'git', 'curl', 'source', 'chmod', 'chown',
                'tar', 'unzip', 'zip', 'mkdir', 'clear', 'true', 'false',
            ];

            $firstWord = explode(' ', $command)[0];
            if (! in_array($firstWord, $allowedCronCommands, true)) {
                \Illuminate\Support\Facades\Log::warning('[CRON_BLOCKED] Cron command tidak diizinkan: ' . $command);
                continue;
            }

            Schedule::exec('cd ' . escapeshellarg($projectPath) . ' && ' . $command)
                ->cron($cron->schedule_expression)
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/cron-' . $subdomain . '.log'));
        }
    }
} catch (\Throwable $exception) {
    \Illuminate\Support\Facades\Log::warning('Custom hosting crons tidak dimuat: koneksi database tidak tersedia.', [
        'message' => $exception->getMessage(),
    ]);
}
