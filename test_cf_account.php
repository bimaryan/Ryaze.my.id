<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiToken = env('CLOUDFLARE_API_TOKEN');
$zoneId = env('CLOUDFLARE_ZONE_ID');

$res = Illuminate\Support\Facades\Http::withToken($apiToken)->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}");
$data = $res->json();
$accountId = $data['result']['account']['id'] ?? null;
echo "Account ID: " . $accountId . "\n";
