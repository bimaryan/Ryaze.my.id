<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'history.*.role' => 'required|in:user,assistant',
            'history.*.content' => 'required|string|max:2000'
        ]);

        $apiKey = config('services.groq.api_key');
        $model = config('services.groq.text_model');

        if (!$apiKey) {
            return response()->json(['error' => 'API Key not configured'], 500);
        }

        $systemPrompt = "Anda adalah Ryaze Assistant, asisten virtual yang ramah dan membantu untuk Ryaze (ryaze.my.id). Ryaze adalah platform layanan Cloud Hosting modern dengan fitur Auto Deploy (mendukung Node.js, PHP, Python, React, Vue), Web Terminal, File Manager, Database (MySQL, PostgreSQL, Redis), serta layanan Jasa Joki (Tugas/Skripsi) IT yang profesional. Berikan jawaban yang ringkas, membantu, sopan, dan dalam bahasa Indonesia. Jangan menggunakan markdown rumit, gunakan teks polos atau list bullet sederhana.";

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ]
        ];

        // Append history (limit to last 10 messages)
        $history = $request->input('history', []);
        $history = array_slice($history, -10);
        
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $request->input('message')
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? 'Maaf, saya tidak dapat merespons saat ini.';
                
                return response()->json([
                    'reply' => $reply
                ]);
            } else {
                Log::error('Groq API Error: ' . $response->body());
                return response()->json([
                    'error' => 'Gagal terhubung ke AI. Silakan coba lagi.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Groq Exception: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan sistem.'
            ], 500);
        }
    }
}
