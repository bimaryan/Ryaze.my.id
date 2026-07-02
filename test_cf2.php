<?php
$env = parse_ini_file('.env');
$zoneId = $env['CLOUDFLARE_ZONE_ID'];
$apiToken = $env['CLOUDFLARE_API_TOKEN'];
$tunnelUrl = preg_replace('#^https?://#', '', rtrim($env['CLOUDFLARE_TUNNEL_URL'], '/'));

$ch = curl_init("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records?type=CNAME&name=dev-3000.ryaze.my.id");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $apiToken", "Content-Type: application/json"]);
$res = curl_exec($ch);
echo "GET: $res\n";

$data = json_decode($res, true);
if (empty($data['result'])) {
    $ch2 = curl_init("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records");
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, ["Authorization: Bearer $apiToken", "Content-Type: application/json"]);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode([
        'type' => 'CNAME',
        'name' => 'dev-3000.ryaze.my.id',
        'content' => $tunnelUrl,
        'proxied' => true,
        'ttl' => 1
    ]));
    $res2 = curl_exec($ch2);
    echo "POST: $res2\n";
}
