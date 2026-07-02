<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$zoneId = config('services.cloudflare.zone_id', env('CLOUDFLARE_ZONE_ID'));
$apiToken = config('services.cloudflare.api_token', env('CLOUDFLARE_API_TOKEN'));
$tunnelUrl = preg_replace('#^https?://#', '', rtrim(config('services.cloudflare.tunnel_url', env('CLOUDFLARE_TUNNEL_URL')), '/'));
echo 'Zone: ' . $zoneId . "\n";
$res = Illuminate\Support\Facades\Http::withToken($apiToken)->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", ['type'=>'CNAME', 'name'=>'dev-3000.ryaze.my.id']);
print_r($res->json());
if (empty($res->json('result'))) {
    $res2 = Illuminate\Support\Facades\Http::withToken($apiToken)->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
        'type'    => 'CNAME',
        'name'    => 'dev-3000.ryaze.my.id',
        'content' => $tunnelUrl,
        'proxied' => true,
        'ttl'     => 1,
    ]);
    echo "Create: \n";
    print_r($res2->json());
}
