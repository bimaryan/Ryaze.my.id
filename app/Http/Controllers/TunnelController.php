<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Events\TunnelRequestReceived;

class TunnelController extends Controller
{
    /**
     * Handle public incoming requests and proxy them to the local client.
     */
    public function handleProxyRequest(Request $request, $subdomain, $path = '')
    {
        $requestId = (string) Str::uuid();

        // 1. Simpan request lengkap di cache; broadcast hanya metadata (anti-eavesdrop)
        $headers = $request->headers->all();
        // Remove some headers that shouldn't be proxied blindly
        unset($headers['host']);

        $body = $request->getContent();
        // Encode body to base64 to safely transmit binary or raw data over JSON/WebSocket
        $bodyBase64 = base64_encode($body);

        Cache::put("tunnel_request_{$requestId}", [
            'subdomain' => $subdomain,
            'request_id' => $requestId,
            'method' => $request->method(),
            'path' => $request->path() . ($request->getQueryString() ? '?' . $request->getQueryString() : ''),
            'headers' => $headers,
            'body' => $bodyBase64,
        ], 60);

        event(new TunnelRequestReceived(
            $subdomain,
            $requestId,
            $request->method()
        ));

        // 2. Poll the cache for a response with a timeout (e.g. 30 seconds)
        $timeout = 30; // seconds
        $startTime = time();

        while (time() - $startTime < $timeout) {
            $responseKey = "tunnel_response:{$requestId}";
            if (Cache::has($responseKey)) {
                $responseData = Cache::get($responseKey);
                Cache::forget($responseKey);

                // Return the response back to the original client
                $status = $responseData['status'] ?? 200;
                $responseHeaders = $responseData['headers'] ?? [];
                // Unset some response headers that might conflict
                unset($responseHeaders['content-encoding']);
                unset($responseHeaders['transfer-encoding']);
                
                $bodyDecoded = base64_decode($responseData['body'] ?? '');

                return response($bodyDecoded, $status)->withHeaders($responseHeaders);
            }

            // Sleep for 100ms before checking again
            usleep(100000); 
        }

        // 3. Timeout if no response received
        return response()->json([
            'error' => 'Tunnel timeout. Make sure the local client is connected and running.'
        ], 504);
    }

    /**
     * API for the local client to submit the response back to the server.
     */
    public function submitResponse(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|string',
            'status' => 'required|integer',
            'headers' => 'array',
            'body' => 'nullable|string', // base64 encoded
        ]);

        Cache::put("tunnel_response_{$validated['request_id']}", $validated, now()->addMinutes(2));

        return response()->json(['success' => true]);
    }
}
