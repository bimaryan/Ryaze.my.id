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

            // ════ SECURITY: blokir command cron berbahaya ════
            $command = trim($cron->command);
            if (empty($command)) {
                continue;
            }

            $blockedCronPatterns = [
                '/[;&|><`]/',                       // chaining & redirection
                '/\$\(/',                           // command substitution
                '/\brm\s+-rf\s+\//',                // rm -rf /
                '/\bwget\b|\bcurl\b.*\|.*\b(sh|bash)\b/', // download → execute
                '/\bnc\b|\bnetcat\b/',              // reverse shell
                '/\bpasswd\b|\bshadow\b/',          // password files
                '/\breboot\b|\bshutdown\b/',        // system control
                '/\beval\b|\bexec\b\(\s*[\'"]?php/', // php exec
                '/\bsudo\b/',
            ];

            $isDangerous = false;
            foreach ($blockedCronPatterns as $pattern) {
                if (preg_match($pattern, $command)) {
                    $isDangerous = true;
                    break;
                }
            }

            if ($isDangerous) {
                \Illuminate\Support\Facades\Log::warning('[CRON_BLOCKED] Cron command diblokir: ' . $command);
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
