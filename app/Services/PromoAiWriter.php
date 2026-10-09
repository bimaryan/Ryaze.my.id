<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

/**
 * Membuat copywriting promo event (judul + deskripsi) memakai Groq.
 * Dipakai dari form admin Promo Event lewat endpoint generate-description.
 */
class PromoAiWriter
{
    /**
     * Hasilkan judul dan deskripsi promo berdasarkan brief singkat.
     *
     * @param  string  $title  Judul promo saat ini (boleh dikosongkan).
     * @param  string|null  $brief  Catatan singkat dari admin (tone, sudut pandang, dll).
     * @param  bool  $withTitle  True bila admin juga ingin judul dibuat AI.
     * @return array{title: string|null, description: string}
     */
    public function generate(string $title, ?string $brief = null, bool $withTitle = false): array
    {
        $title = trim($title);
        $brief = trim((string) $brief);

        if ($title === '' && $brief === '') {
            throw new RuntimeException('Isi judul promo atau catatan singkat dulu sebelum meminta bantuan AI.');
        }

        $response = Http::withToken($this->apiKey())
            ->acceptJson()
            ->timeout(60)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.text_model'),
                'messages' => [
                    [
                        'role' => 'system',
                        // Sengaja ringkas: system prompt panjang bikin model ini gagal
                        // menghasilkan JSON ("Failed to generate JSON" dari Groq).
                        'content' => 'Anda copywriter berbahasa Indonesia untuk Ryaze (shared hosting & jasa pembuatan website). '
                            .'Balas HANYA JSON valid tanpa markdown: {"title":"judul promo singkat, maksimal 70 karakter","description":"1-2 kalimat ajakan, maksimal 160 karakter"}. '
                            .'Jangan mengarang harga, diskon, bonus, garansi, atau gimmick yang tidak disebut. '
                            .'Jangan pakai markdown, hashtag, bullet, atau kata seperti "terbaik" dan "paling murah".',
                    ],
                    [
                        'role' => 'user',
                        'content' => sprintf(
                            "Judul promo: %s\nCatatan admin: %s\nTulis deskripsi yang relevan dengan judul tersebut.",
                            $title !== '' ? $title : '(belum ada judul)',
                            $brief !== '' ? $brief : '(tidak ada catatan tambahan)',
                        ),
                    ],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

        $response->throw();

        $text = $response->json('choices.0.message.content');
        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Respons AI tidak memuat isi deskripsi.');
        }

        $decoded = json_decode($this->stripCodeFence($text), true);
        if (! is_array($decoded)) {
            throw new RuntimeException('Respons AI bukan JSON yang valid. Coba ulangi.');
        }

        $validator = Validator::make($decoded, [
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            throw new RuntimeException('Respons AI tidak memenuhi format: '.$validator->errors()->first());
        }

        $description = $this->sanitize($decoded['description']);
        if ($description === '') {
            throw new RuntimeException('Deskripsi yang dihasilkan AI kosong.');
        }

        return [
            'title' => $withTitle && ! empty($decoded['title'])
                ? $this->sanitize($decoded['title'])
                : null,
            'description' => $description,
        ];
    }

    private function apiKey(): string
    {
        $key = config('services.groq.api_key');

        if (! is_string($key) || $key === '') {
            throw new RuntimeException('GROQ_API_KEY belum diatur pada konfigurasi server.');
        }

        return $key;
    }

    private function stripCodeFence(string $text): string
    {
        return preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($text)) ?? trim($text);
    }

    private function sanitize(string $text): string
    {
        // Buang markdown, tag, dan sisa newline supaya aman dipakai di card promo.
        $text = preg_replace('/[*_`#]+/u', '', $text) ?? $text;
        $text = strip_tags($text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}