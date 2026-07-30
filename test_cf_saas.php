<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiToken = env('CLOUDFLARE_API_TOKEN');
$zoneId = env('CLOUDFLARE_ZONE_ID');
$testDomain = 'test-saas-'.time().'.my.id';

echo "Testing Cloudflare for SaaS API...\n";
echo "Target Zone ID: {$zoneId}\n";

$res = \Illuminate\Support\Facades\Http::withToken($apiToken)
    ->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/custom_hostnames", [
        'hostname' => $testDomain,
        'ssl' => [
            'method' => 'http',
            'type' => 'dv'
        ]
    ]);

if ($res->successful()) {
    echo "SUCCESS: Custom Hostname API works!\n";
    $id = $res->json('result.id');
    // Cleanup
    \Illuminate\Support\Facades\Http::withToken($apiToken)
        ->delete("https://api.cloudflare.com/client/v4/zones/{$zoneId}/custom_hostnames/{$id}");
    echo "Cleaned up test domain.\n";
} else {
    echo "ERROR: Failed to add Custom Hostname.\n";
    echo $res->body() . "\n";
}
