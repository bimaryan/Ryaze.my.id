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
        $coverImage = $this->generateCoverImage($content['image_prompt']);

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
            ->post('https://api.openai.com/v1/responses', [
                'model' => config('services.openai.text_model'),
                'instructions' => <<<'PROMPT'
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
PROMPT,
                'input' => sprintf(
                    "Topik artikel: %s\nKategori: %s\nBuat artikel praktis dengan pembuka, beberapa subjudul, dan penutup.",
                    $topic,
                    $category?->name ?? 'Umum',
                ),
            ]);

        $response->throw();

        $text = $response->json('output.0.content.0.text') ?? $response->json('output_text');
        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Respons AI tidak memuat konten artikel.');
        }

        $decoded = json_decode($this->stripCodeFence($text), true);
        if (! is_array($decoded)) {
            throw new RuntimeException('Respons AI bukan JSON artikel yang valid.');
        }

        $validator = Validator::make($decoded, [
            'title' => ['required', 'string', 'max:70'],
            'excerpt' => ['required', 'string', 'min:80', 'max:500'],
            'body' => ['required', 'string', 'min:1200', 'max:50000'],
            'tags' => ['required', 'array', 'min:2', 'max:6'],
            'tags.*' => ['string', 'max:40'],
            'meta_title' => ['required', 'string', 'max:70'],
            'meta_description' => ['required', 'string', 'max:160'],
            'image_prompt' => ['required', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            throw new RuntimeException('Respons AI tidak memenuhi format artikel: '.$validator->errors()->first());
        }

        $decoded['body'] = $this->sanitizeBody($decoded['body']);

        return $decoded;
    }

    private function generateCoverImage(string $prompt): string
    {
        $response = Http::withToken($this->apiKey())
            ->acceptJson()
            ->timeout(180)
            ->post('https://api.openai.com/v1/images/generations', [
                'model' => config('services.openai.image_model'),
                'prompt' => "Editorial blog cover image. {$prompt} Wide landscape composition, modern and professional. No text, no letters, no logos, no watermark.",
            ]);

        $response->throw();

        $image = $response->json('data.0.b64_json');
        if (! is_string($image) || $image === '') {
            throw new RuntimeException('Respons AI tidak memuat gambar sampul.');
        }

        $binary = base64_decode($image, true);
        if ($binary === false) {
            throw new RuntimeException('Data gambar sampul AI tidak valid.');
        }

        $path = 'articles/generated/'.Str::uuid().'.png';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    private function apiKey(): string
    {
        $key = config('services.openai.api_key');

        if (! is_string($key) || $key === '') {
            throw new RuntimeException('OPENAI_API_KEY belum diatur pada konfigurasi server.');
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
