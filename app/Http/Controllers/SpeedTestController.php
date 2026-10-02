<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SpeedTestController extends Controller
{
    public function index(): View
    {
        return view('pages.speed-test');
    }

    public function ping(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function download(Request $request): Response
    {
        $size = (int) $request->get('size', 10); // MB, default 10MB
        $size = min(max($size, 1), 100); // clamp 1-100 MB

        $data = random_bytes($size * 1024 * 1024);

        return response($data, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => strlen($data),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $contentLength = (int) $request->header('Content-Length', 0);

        // Baca body untuk memaksa upload complete (tidak disimpan)
        $request->getContent();

        return response()->json([
            'status' => 'ok',
            'received_bytes' => $contentLength,
            'received_mb' => round($contentLength / (1024 * 1024), 2),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}