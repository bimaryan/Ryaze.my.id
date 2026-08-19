<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConsultationSession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsultationController extends Controller
{
    private function getSystemPrompt($token)
    {
        return "Kamu adalah AI Konsultan Proyek dari 'Ryaze' divisi 'Jasa Pembuatan Sistem / Joki'. 
Tugasmu adalah menyapa pengguna dengan ramah, menanyakan kebutuhan proyek mereka (fitur utama, dll), dan membantu mereka mematangkan ide.
Gunakan bahasa Indonesia yang profesional namun asik (gaya startup).
Jika pengguna sudah menjelaskan kebutuhannya dengan cukup jelas, tawarkan mereka untuk langsung memulai proyek dengan membuat akun.
Jika kamu merasa kebutuhan sudah jelas dan user siap mendaftar, BERIKAN tautan pendaftaran tepat dengan format markdown berikut di akhir pesanmu:
[DAFTAR SEKARANG](/register?consultation_token={$token})
JANGAN berikan tautan itu jika user belum siap atau masih bertanya-tanya. Jaga balasanmu tetap ringkas dan tidak bertele-tele.";
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'token' => 'nullable|string'
        ]);

        $token = $request->input('token');
        $userMessage = $request->input('message');

        if (!$token) {
            $token = Str::random(40);
            $session = ConsultationSession::create([
                'token' => $token,
                'chat_history' => []
            ]);
        } else {
            $session = ConsultationSession::where('token', $token)->first();
            if (!$session) {
                return response()->json(['error' => 'Sesi tidak valid atau telah kedaluwarsa.'], 404);
            }
        }

        $history = $session->chat_history ?? [];
        
        // Prepare messages for Groq (OpenAI format)
        $messages = [
            [
                'role' => 'system',
                'content' => $this->getSystemPrompt($token)
            ]
        ];
        
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                'content' => $msg['content']
            ];
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        // Append to history for saving later
        $history[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        $payload = [
            'model' => 'llama-3.1-70b-versatile',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 800
        ];

        $apiKey = env('GROQ_API_KEY');
        
        if (!$apiKey) {
            // Fallback for development if API key not set
            $aiResponse = "Maaf, sistem AI Konsultan sedang tidak dapat diakses saat ini (API Key GROQ belum diatur). Silakan hubungi admin atau langsung [DAFTAR SEKARANG](/register).";
            
            $history[] = [
                'role' => 'assistant',
                'content' => $aiResponse
            ];
            
            $session->update(['chat_history' => $history]);
            
            return response()->json([
                'token' => $token,
                'reply' => $aiResponse
            ]);
        }

        try {
            $response = Http::withToken($apiKey)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://api.groq.com/openai/v1/chat/completions", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $aiResponse = $data['choices'][0]['message']['content'] ?? "Maaf, saya tidak mengerti. Bisa diulangi?";
            } else {
                Log::error('Groq API Error: ' . $response->body());
                $aiResponse = "Maaf, saya sedang mengalami gangguan sistem. Silakan coba lagi nanti atau langsung hubungi tim kami via WhatsApp.";
            }
        } catch (\Exception $e) {
            Log::error('Groq Exception: ' . $e->getMessage());
            $aiResponse = "Terjadi kesalahan internal. Silakan coba lagi nanti.";
        }

        $history[] = [
            'role' => 'assistant',
            'content' => $aiResponse
        ];

        $session->update(['chat_history' => $history]);

        return response()->json([
            'token' => $token,
            'reply' => $aiResponse
        ]);
    }

    public function history(Request $request)
    {
        $token = $request->query('token');
        if (!$token) {
            return response()->json(['history' => []]);
        }

        $session = ConsultationSession::where('token', $token)->first();
        if (!$session) {
            return response()->json(['history' => []]);
        }

        return response()->json([
            'history' => $session->chat_history ?? []
        ]);
    }
}
