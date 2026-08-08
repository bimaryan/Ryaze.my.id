<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DomainNginxStatus extends Command
{
    protected $signature = 'domain:nginx-status {domain} {status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update custom nginx config status for a hosting project (used by bash worker)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domainName = $this->argument('domain');
        $status = $this->argument('status');

        // Pesan error ditulis worker ke storage/app/nginx/errors/{domain}.txt
        $errorFile = storage_path('app/nginx/errors/' . $domainName . '.txt');
        $error = null;
        if (file_exists($errorFile)) {
            $error = trim(file_get_contents($errorFile));
            if ($error === '' || $status !== 'failed') {
                @unlink($errorFile);
                if ($status !== 'failed') {
                    $error = null;
                }
            }
        }

        $project = \App\Models\HostingProject::where('ryaze_domain', $domainName)->first();

        if ($project) {
            $project->update([
                'nginx_status'      => $status,
                'nginx_error'       => $error,
                'nginx_applied_at'  => $status === 'applied' ? now() : ($status === 'pending' ? null : $project->nginx_applied_at),
            ]);
            $this->info("Domain {$domainName} nginx config status updated to {$status}");
        } else {
            $this->error("Domain {$domainName} not found");
        }
    }
}
