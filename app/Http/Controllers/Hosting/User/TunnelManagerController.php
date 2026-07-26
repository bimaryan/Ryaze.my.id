<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\Tunnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TunnelManagerController extends Controller
{
    public function index()
    {
        $tunnels = Tunnel::where('user_id', Auth::id())->latest()->get();
        return view('pages.hosting.user.tunnel.index', compact('tunnels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:63|unique:tunnels,subdomain|regex:/^[a-z0-9-]+$/i',
            'target_port' => 'required|integer|min:1|max:65535',
        ]);

        $tunnel = Tunnel::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'subdomain' => strtolower($request->subdomain),
            'target_port' => $request->target_port,
            'status' => 'inactive',
        ]);

        // Create Cloudflare DNS Record
        try {
            $apiToken = config('services.cloudflare.api_token', env('CLOUDFLARE_API_TOKEN'));
            $tunnelUrl = preg_replace('#^https?://#', '', rtrim(config('services.cloudflare.tunnel_url', env('CLOUDFLARE_TUNNEL_URL')), '/'));
            $domainName = $tunnel->subdomain . '.ryaze.my.id';
            $zoneId = config('services.cloudflare.zone_id', env('CLOUDFLARE_ZONE_ID'));

            if ($zoneId && $apiToken && $tunnelUrl) {
                $existing = \Illuminate\Support\Facades\Http::withToken($apiToken)->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                    'type' => 'CNAME',
                    'name' => $domainName
                ]);
                if ($existing->successful() && empty($existing->json('result'))) {
                    $resp = \Illuminate\Support\Facades\Http::withToken($apiToken)->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                        'type'    => 'CNAME',
                        'name'    => $domainName,
                        'content' => $tunnelUrl,
                        'proxied' => true,
                        'ttl'     => 1,
                    ]);
                    \Illuminate\Support\Facades\Log::info("Cloudflare Tunnel DNS created: " . $resp->body());
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to create CF DNS for tunnel: " . $e->getMessage());
        }

        // Create OpenResty Proxy Script
        try {
            $subdomain = $tunnel->subdomain;
            $projectDir = "/www/sites/hosting_clients/{$subdomain}";
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            
            if ($isWindows) {
                // Local dev mockup
                $projectDir = storage_path("app/hosting_clients/{$subdomain}");
                if (!file_exists($projectDir)) mkdir($projectDir, 0755, true);
            } else {
                if (!file_exists($projectDir)) exec("mkdir -p \"{$projectDir}\"");
            }

            $relayApiUrl = rtrim(env('APP_URL', 'http://ryaze.my.id'), '/') . '/api/tunnel/relay';
            $proxyScript = <<<PHP
<?php
// Ryaze Tunnel Relay Proxy
\$url = "{$relayApiUrl}?_subdomain={$subdomain}&_path=" . urlencode(\$_SERVER['REQUEST_URI']);
\$ch = curl_init();
curl_setopt(\$ch, CURLOPT_URL, \$url);
curl_setopt(\$ch, CURLOPT_CUSTOMREQUEST, \$_SERVER['REQUEST_METHOD']);
curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt(\$ch, CURLOPT_HEADER, true);
\$headers = [];
foreach (getallheaders() as \$key => \$val) {
    if (strtolower(\$key) !== 'host') {
        \$headers[] = "\$key: \$val";
    }
}
curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);
\$body = file_get_contents('php://input');
if (\$body) curl_setopt(\$ch, CURLOPT_POSTFIELDS, \$body);

\$response = curl_exec(\$ch);
\$headerSize = curl_getinfo(\$ch, CURLINFO_HEADER_SIZE);
\$status = curl_getinfo(\$ch, CURLINFO_HTTP_CODE);
curl_close(\$ch);

http_response_code(\$status);
\$headerStr = substr(\$response, 0, \$headerSize);
\$bodyStr = substr(\$response, \$headerSize);
foreach (explode("\\n", str_replace("\\r\\n", "\\n", \$headerStr)) as \$line) {
    if (strpos(\$line, ':') !== false && stripos(\$line, 'Transfer-Encoding') === false) {
        \$isSetCookie = stripos(\$line, 'Set-Cookie:') === 0;
        header(\$line, !\$isSetCookie);
    }
}
echo \$bodyStr;
PHP;
            if ($isWindows) {
                file_put_contents("{$projectDir}/index.php", $proxyScript);
            } else {
                file_put_contents("/tmp/tunnel_{$subdomain}_index.php", $proxyScript);
                exec("mv /tmp/tunnel_{$subdomain}_index.php \"{$projectDir}/index.php\"");
                exec("chown -R www-data:www-data \"{$projectDir}\" 2>/dev/null");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to create OpenResty proxy script for tunnel: " . $e->getMessage());
        }

        return redirect()->route('user_hosting.tunnels.index')->with('success', 'Tunnel berhasil dibuat.');
    }

    public function destroy($id)
    {
        $tunnel = Tunnel::where('user_id', Auth::id())->findOrFail($id);
        
        // Delete Cloudflare DNS Record
        try {
            $apiToken = config('services.cloudflare.api_token', env('CLOUDFLARE_API_TOKEN'));
            $domainName = $tunnel->subdomain . '.ryaze.my.id';
            $zoneId = config('services.cloudflare.zone_id', env('CLOUDFLARE_ZONE_ID'));

            if ($zoneId && $apiToken) {
                $existing = \Illuminate\Support\Facades\Http::withToken($apiToken)->get("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records", [
                    'type' => 'CNAME',
                    'name' => $domainName
                ]);
                if ($existing->successful() && !empty($existing->json('result'))) {
                    $recordId = $existing->json('result.0.id');
                    \Illuminate\Support\Facades\Http::withToken($apiToken)->delete("https://api.cloudflare.com/client/v4/zones/{$zoneId}/dns_records/{$recordId}");
                    \Illuminate\Support\Facades\Log::info("Cloudflare Tunnel DNS deleted: " . $domainName);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to delete CF DNS for tunnel: " . $e->getMessage());
        }

        // Delete OpenResty Proxy Script
        try {
            $subdomain = $tunnel->subdomain;
            $projectDir = "/www/sites/hosting_clients/{$subdomain}";
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            
            if ($isWindows) {
                $projectDir = storage_path("app/hosting_clients/{$subdomain}");
                if (file_exists($projectDir)) {
                    if (file_exists("{$projectDir}/index.php")) unlink("{$projectDir}/index.php");
                    rmdir($projectDir);
                }
            } else {
                if (file_exists($projectDir)) exec("rm -rf \"{$projectDir}\"");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to delete OpenResty proxy script for tunnel: " . $e->getMessage());
        }

        $tunnel->delete();

        return redirect()->route('user_hosting.tunnels.index')->with('success', 'Tunnel berhasil dihapus.');
    }

    public function downloadClient($id)
    {
        $tunnel = Tunnel::where('user_id', Auth::id())->findOrFail($id);
        
$clientCode = <<<'PHP'
<?php
/**
 * Ryaze Local Tunnel Client
 * Run this script via CLI: php ryaze-tunnel.php
 */

$subdomain = '{SUBDOMAIN}';
$targetPort = {PORT};
$serverUrl = '{SERVER_URL}';
$websocketUrl = '{WEBSOCKET_URL}';
$appKey = '{APP_KEY}';

echo "========================================\n";
echo "🚀 Ryaze Tunnel Client Started\n";
echo "🌐 Public URL: https://{$subdomain}.ryaze.my.id\n";
echo "🏠 Local Target: http://127.0.0.1:{$targetPort}\n";
echo "========================================\n\n";

if (!extension_loaded('curl')) die("Error: cURL extension is required.\n");

function processRequest($data, $targetPort, $serverUrl) {
    echo "[" . date('H:i:s') . "] ⚡ Request: {$data['method']} {$data['path']}\n";
    $ch = curl_init();
    $url = "http://127.0.0.1:{$targetPort}{$data['path']}";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $data['method']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $headers = [];
    foreach ($data['headers'] as $key => $values) {
        if (strtolower($key) !== 'host') {
            foreach ($values as $value) $headers[] = "$key: $value";
        }
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($data['body']) curl_setopt($ch, CURLOPT_POSTFIELDS, base64_decode($data['body']));
    
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $headerStr = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    $responseHeaders = [];
    foreach (explode("\r\n", $headerStr) as $line) {
        if (strpos($line, ':') !== false) {
            $parts = explode(':', $line, 2);
            $key = trim($parts[0]);
            $val = trim($parts[1]);
            if (isset($responseHeaders[$key])) {
                if (!is_array($responseHeaders[$key])) {
                    $responseHeaders[$key] = [$responseHeaders[$key]];
                }
                $responseHeaders[$key][] = $val;
            } else {
                $responseHeaders[$key] = $val;
            }
        }
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$serverUrl}/api/tunnel/response");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'request_id' => $data['requestId'],
        'status' => $status,
        'headers' => $responseHeaders,
        'body' => base64_encode($body)
    ]));
    curl_exec($ch);
    curl_close($ch);
    echo "[" . date('H:i:s') . "] ✅ Response: {$status}\n";
}

function writeWebSocketFrame($sock, $payload, $opcode = 1) {
    $length = strlen($payload);
    $header = chr(128 | $opcode);
    if ($length <= 125) $header .= chr(128 | $length);
    elseif ($length >= 126 && $length <= 65535) $header .= chr(128 | 126) . pack('n', $length);
    else $header .= chr(128 | 127) . pack('J', $length);
    $mask = openssl_random_pseudo_bytes(4);
    $header .= $mask;
    $maskedPayload = '';
    for ($i = 0; $i < $length; $i++) $maskedPayload .= $payload[$i] ^ $mask[$i % 4];
    fwrite($sock, $header . $maskedPayload);
}

function readWebSocketFrame($sock) {
    $header = fread($sock, 2);
    if (empty($header)) return false;
    $opcode = ord($header[0]) & 0x0F;
    $payloadLength = ord($header[1]) & 0x7F;
    if ($payloadLength === 126) $payloadLength = current(unpack('n', fread($sock, 2)));
    elseif ($payloadLength === 127) $payloadLength = current(unpack('J', fread($sock, 8)));
    $payload = '';
    while (strlen($payload) < $payloadLength) {
        $chunk = fread($sock, $payloadLength - strlen($payload));
        if ($chunk === false) break;
        $payload .= $chunk;
    }
    return ['opcode' => $opcode, 'payload' => $payload];
}

$host = parse_url($websocketUrl, PHP_URL_HOST);
$port = parse_url($websocketUrl, PHP_URL_PORT) ?: (parse_url($websocketUrl, PHP_URL_SCHEME) === 'wss' ? 443 : 80);
$path = "/app/{$appKey}?protocol=7&client=js&version=8.4.0-rc2&flash=false";
$scheme = parse_url($websocketUrl, PHP_URL_SCHEME) === 'wss' ? 'ssl://' : 'tcp://';

while (true) {
    $sock = @fsockopen($scheme . $host, $port, $errno, $errstr, 10);
    if (!$sock) {
        echo "[" . date('H:i:s') . "] Reconnecting in 5 seconds...\n";
        sleep(5);
        continue;
    }
    stream_set_timeout($sock, 300);

    $key = base64_encode(openssl_random_pseudo_bytes(16));
    $header = "GET $path HTTP/1.1\r\nHost: $host:$port\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Key: $key\r\nSec-WebSocket-Version: 13\r\n\r\n";
    fwrite($sock, $header);

    // Parse HTTP Headers properly so we don't consume WS frames
    while (!feof($sock)) {
        $line = fgets($sock);
        if ($line === "\r\n") break;
    }

    echo "[" . date('H:i:s') . "] Connected to Ryaze Tunnel Server.\nWaiting for requests...\n";

    // Pusher protocol: subscribe to channel
    $subscribeMsg = json_encode(['event' => 'pusher:subscribe', 'data' => ['channel' => 'tunnel.' . $subdomain]]);
    writeWebSocketFrame($sock, $subscribeMsg);

    while (!feof($sock)) {
        $frame = readWebSocketFrame($sock);
        if ($frame === false) break;
        if ($frame['opcode'] == 9) { // Ping
            writeWebSocketFrame($sock, $frame['payload'], 10); // Pong
            continue;
        }
        if ($frame['opcode'] == 8) break; // Close
        if ($frame['opcode'] == 1 || $frame['opcode'] == 2) {
            $msg = json_decode($frame['payload'], true);
            if ($msg) {
                if ($msg['event'] === 'App\Events\TunnelRequestReceived') {
                    $data = json_decode($msg['data'], true);
                    // Process request async-like or blocking (for simple script blocking is fine)
                    processRequest($data, $targetPort, $serverUrl);
                } elseif ($msg['event'] === 'pusher:ping') {
                    writeWebSocketFrame($sock, json_encode(['event' => 'pusher:pong', 'data' => []]));
                }
            }
        }
    }
    
    echo "[" . date('H:i:s') . "] Connection closed. Reconnecting...\n";
    if (is_resource($sock)) fclose($sock);
    sleep(2);
}
PHP;
        
        $serverUrl = rtrim(env('APP_URL', 'http://ryaze.my.id'), '/');
        $websocketUrl = str_replace(['http://', 'https://'], ['ws://', 'wss://'], $serverUrl);
        $appKey = env('REVERB_APP_KEY');
        
        $clientCode = str_replace(
            ['{SUBDOMAIN}', '{PORT}', '{SERVER_URL}', '{WEBSOCKET_URL}', '{APP_KEY}'],
            [$tunnel->subdomain, $tunnel->target_port, $serverUrl, $websocketUrl, $appKey],
            $clientCode
        );

        return response($clientCode)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="ryaze-tunnel-' . $tunnel->subdomain . '.php"');
    }

    public function relay(Request $request)
    {
        $subdomain = $request->query('_subdomain');
        $path = $request->query('_path', '/');
        
        $requestId = Str::uuid()->toString();
        $method = $request->method();
        $headers = $request->headers->all();
        $body = $request->getContent();

        // Broadcast to WebSocket Client
        event(new \App\Events\TunnelRequestReceived(
            $subdomain,
            $requestId,
            $method,
            $path,
            $headers,
            base64_encode($body)
        ));

        // Long Polling (Wait for response from PHP CLI Client)
        $cacheKey = "tunnel_response:{$requestId}";
        $maxAttempts = 300; // 30 seconds (100ms * 300)
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                $response = \Illuminate\Support\Facades\Cache::pull($cacheKey);
                
                $httpResponse = response(base64_decode($response['body']), $response['status']);
                foreach ($response['headers'] as $key => $val) {
                    if (strtolower($key) !== 'transfer-encoding') {
                        if (is_array($val)) {
                            foreach ($val as $v) {
                                $httpResponse->header($key, $v, false);
                            }
                        } else {
                            $httpResponse->header($key, $val);
                        }
                    }
                }
                
                return $httpResponse;
            }
            usleep(100000); // 100ms
        }

        return response("Gateway Timeout: Tunnel client offline or took too long to respond.", 504);
    }

    public function response(Request $request)
    {
        $request->validate([
            'request_id' => 'required|string',
            'status' => 'required|integer',
            'headers' => 'array',
            'body' => 'nullable|string'
        ]);

        $cacheKey = "tunnel_response:{$request->request_id}";
        \Illuminate\Support\Facades\Cache::put($cacheKey, $request->all(), 60);

        return response()->json(['status' => 'ok']);
    }
}

