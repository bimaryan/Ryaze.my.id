<?php

namespace App\Services;

use App\Models\InfoUpdate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

/**
 * Menyusun judul + deskripsi Info Update memakai Groq.
 * Dipakai dari form admin Info Update lewat endpoint generate-description.
 *
 * Sengaja mengikuti gaya PromoAiWriter: system prompt pendek dan output JSON
 * supaya model openai/gpt-oss-120b tidak gagal parsing ("Failed to generate
 * JSON" dari Groq kalau promptnya terlalu naratif).
 */
class InfoUpdateAiWriter
{
    /**
     * @param  string  $title     Judul draft (boleh kosong — AI akan membuat judul).
     * @param  string  $category  Category update, menentukan nada tulisan.
     * @param  string  $brief     Catatan admin: detail teknis, tanggal, dampaknya.
     * @param  bool    $withTitle True bila admin juga ingin judul dibuat AI.
     * @return array{title: string|null, description: string}
     */
    public function generate(
        string $title,
        string $category,
        ?string $brief = null,
        bool $withTitle = false
    ): array {
        $title = trim($title);
        $brief = trim((string) $brief);

        if ($title === '' && $brief === '') {
            throw new RuntimeException('Isi judul atau catatan singkat dulu sebelum meminta bantuan AI.');
        }

        $category = in_array($category, InfoUpdate::CATEGORIES, true)
            ? $category
            : InfoUpdate::CATEGORY_FEATURE;

        $response = Http::withToken($this->apiKey())
            ->acceptJson()
            ->timeout(60)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.text_model'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt($category),
                    ],
                    [
                        'role' => 'user',
                        'content' => sprintf(
                            "Judul: %s\nKategori: %s\nCatatan admin: %s\nTulis deskripsi yang relevan dengan judul tersebut.",
                            $title !== '' ? $title : '(belum ada judul)',
                            InfoUpdate::CATEGORY_META[$category]['label'],
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
            'description' => ['required', 'string', 'max:2000'],
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

    /**
     * Nadanya dibedakan per kategori supaya feature tidak terbaca seperti pengumuman,
     * dan maintenance selalu menyebut dampak ke user.
     */
    private function systemPrompt(string $category): string
    {
        $tone = match ($category) {
            InfoUpdate::CATEGORY_MAINTENANCE =>
                'Fokus pada impacting: jam/what yang tidak bisa dipakai selama maintenance, dan tindakan yang perlu dilakukan user.',
            InfoUpdate::CATEGORY_ANNOUNCEMENT =>
                'Nada resmi dan netral. Sampaikan perubahan kebijakan atau ketetapan secara lugas tanpa berlebihan.',
            default =>
                'Nada hangat namun faktual. Jelaskan manfaat fitur bagi user, bukan detail teknis internal.',
        };

        return 'Anda editor teknis Ryaze (shared hosting & jasa pembuatan website) yang menulis info update berbahasa Indonesia. '
            .$tone.' '
            .'Balas HANYA JSON valid tanpa markdown: {"title":"judul ringkas, maksimal 70 karakter","description":"2-4 kalimat, maksimal 600 karakter"}. '
            .'Jangan mengarang angka, harga, tanggal, atau fitur yang tidak disebut. '
            .'Jangan pakai markdown, hashtag, bullet, maupun kata seperti "terbaik" dan "paling murah".';
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

    /**
     * Deskripsi disimpan sebagai teks polos supaya aman dirender apa adanya
     * di halaman publik dan di panel admin.
     */
    private function sanitize(string $text): string
    {
        $text = preg_replace('/[*_`#]+/u', '', $text) ?? $text;
        $text = strip_tags($text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}