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
        $crons = \App\Models\HostingCron::where('is_active', true)->get();
        foreach ($crons as $cron) {
            $projectPath = 'C:\\www\\sites\\hosting_clients\\' . $cron->project_id;
            if (!file_exists($projectPath)) {
                $projectPath = storage_path('app/hosting_clients/' . $cron->project_id);
            }

            Schedule::exec('cd ' . escapeshellarg($projectPath) . ' && ' . $cron->command)
                ->cron($cron->schedule_expression)
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/cron-' . $cron->project_id . '.log'));
        }
    }
} catch (\Throwable $exception) {
    \Illuminate\Support\Facades\Log::warning('Custom hosting crons tidak dimuat: koneksi database tidak tersedia.', [
        'message' => $exception->getMessage(),
    ]);
}
