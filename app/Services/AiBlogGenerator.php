<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;

class AiBlogGenerator
{
    /**
     * Membuat artikel dan gambar sampul sekaligus. Artikel tetap menjadi draft
     * kecuali pemanggil secara eksplisit meminta untuk mempublikasikannya.
     */
    public function generate(string $topic, User $author, ?ArticleCategory $category = null, bool $publish = false): Article
    {
        $topic = trim($topic);

        if ($topic === '') {
            throw new RuntimeException('Topik artikel tidak boleh kosong.');
        }

        $content = $this->generateContent($topic, $category);
        // Cover image tidak di-generate karena Groq hanya model teks.
        $coverImage = null; 

        return Article::create([
            'user_id' => $author->id,
            'category_id' => $category?->id,
            'title' => $content['title'],
            'excerpt' => $content['excerpt'],
            'body' => $content['body'],
            'cover_image' => $coverImage,
            'tags' => $content['tags'],
            'status' => $publish ? 'published' : 'draft',
            'published_at' => $publish ? now() : null,
            'meta_title' => $content['meta_title'],
            'meta_description' => $content['meta_description'],
        ]);
    }

    private function generateContent(string $topic, ?ArticleCategory $category): array
    {
        $response = Http::withToken($this->apiKey())
            ->acceptJson()
            ->timeout(180)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.text_model'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => <<<'PROMPT'
Anda adalah editor blog berbahasa Indonesia untuk Ryaze, platform hosting dan jasa pembuatan website.
Tulis artikel yang orisinal, akurat, bermanfaat, dan mudah dipahami. Jangan mengklaim fakta, harga,
atau statistik yang tidak diberikan. Jangan menulis skrip, iframe, event handler HTML, atau URL javascript.
Kembalikan HANYA JSON valid tanpa markdown dengan struktur ini:
{
  "title": "maksimal 70 karakter",
  "excerpt": "ringkasan 120-220 karakter",
  "body": "HTML artikel 800-1200 kata memakai p, h2, h3, ul, ol, li, strong dan a bila diperlukan",
  "tags": ["maksimal 6 tag"],
  "meta_title": "maksimal 70 karakter",
  "meta_description": "maksimal 160 karakter",
  "image_prompt": "prompt bahasa Inggris untuk cover artikel, tanpa teks/logo/watermark"
}
PROMPT
                    ],
                    [
                        'role' => 'user',
                        'content' => sprintf(
                            "Topik artikel: %s\nKategori: %s\nBuat artikel praktis dengan pembuka, beberapa subjudul, dan penutup.",
                            $topic,
                            $category?->name ?? 'Umum',
                        )
                    ]
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

        $response->throw();

        $text = $response->json('choices.0.message.content');
        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Respons AI tidak memuat konten artikel.');
        }

        $decoded = json_decode($this->stripCodeFence($text), true);
        if (! is_array($decoded)) {
            throw new RuntimeException('Respons AI bukan JSON artikel yang valid.');
        }

        $validator = Validator::make($decoded, [
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'body' => ['required', 'string', 'min:100', 'max:100000'],
            'tags' => ['required', 'array', 'max:15'],
            'tags.*' => ['string', 'max:100'],
            'meta_title' => ['required', 'string', 'max:200'],
            'meta_description' => ['required', 'string', 'max:500'],
            'image_prompt' => ['required', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            throw new RuntimeException('Respons AI tidak memenuhi format artikel: '.$validator->errors()->first());
        }

        $decoded['body'] = $this->sanitizeBody($decoded['body']);

        return $decoded;
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

    private function sanitizeBody(string $body): string
    {
        $body = strip_tags($body, '<p><h2><h3><ul><ol><li><strong><em><a><blockquote><br>');
        $body = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $body) ?? $body;

        return preg_replace('/(href\s*=\s*["\'])\s*javascript:/i', '$1#', $body) ?? $body;
    }
}
