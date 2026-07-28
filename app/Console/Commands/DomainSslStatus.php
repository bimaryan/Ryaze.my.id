<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DomainSslStatus extends Command
{
    protected $signature = 'domain:ssl-status {domain} {status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update SSL status for a custom domain (used by bash worker)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domainName = $this->argument('domain');
        $status = $this->argument('status');

        $domain = \App\Models\HostingDomain::where('domain_name', $domainName)->first();

        if ($domain) {
            $domain->update(['ssl_status' => $status]);
            $this->info("Domain {$domainName} SSL status updated to {$status}");
        } else {
            $this->error("Domain {$domainName} not found");
        }
    }
}
