<?php
if (!function_exists('getallheaders')) {
    function getallheaders() {
        return [];
    }
}
$port = 9007;
$url = "http://127.0.0.1:{$port}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

$response = curl_exec($ch);
if (curl_errno($ch)) {
    http_response_code(502);
    echo "Ryaze Gateway Error: App is starting or unreachable. " . curl_error($ch);
    curl_close($ch);
    exit;
}
echo "Success\n";
