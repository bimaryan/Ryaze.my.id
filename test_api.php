<?php
$env = parse_ini_file('.env');
$zoneId = $env['CLOUDFLARE_ZONE_ID'];
$apiToken = $env['CLOUDFLARE_API_TOKEN'];
$ch = curl_init("https://api.cloudflare.com/client/v4/zones/{$zoneId}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $apiToken", "Content-Type: application/json"]);
$res = curl_exec($ch);
echo $res;
