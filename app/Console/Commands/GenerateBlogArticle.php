<?php

namespace App\Console\Commands;

use App\Jobs\GenerateAiBlogArticle;
use App\Models\ArticleCategory;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateBlogArticle extends Command
{
    protected $signature = 'blog:generate
        {--topic= : Topik artikel. Wajib bila tidak memakai --scheduled}
        {--category= : ID kategori artikel (opsional)}
        {--publish : Langsung publikasikan artikel}
        {--scheduled : Jalankan sesuai pengaturan otomatis}';

    protected $description = 'Mengantrikan pembuatan artikel dan gambar sampul dengan AI';

    public function handle(): int
    {
        $scheduled = (bool) $this->option('scheduled');

        if ($scheduled && ! $this->shouldGenerateScheduledArticle()) {
            return self::SUCCESS;
        }

        // Scheduler berjalan setiap menit. Kunci mencegah job duplikat saat
        // pembuatan gambar masih berlangsung atau worker sedang sibuk.
        if ($scheduled && ! Cache::add('blog_ai_generation_queued', true, now()->addMinutes(10))) {
            return self::SUCCESS;
        }

        $topic = trim((string) $this->option('topic'));
        if ($topic === '' && $scheduled) {
            $topic = $this->pickScheduledTopic();
        }

        if ($topic === '') {
            if ($scheduled) {
                Cache::forget('blog_ai_generation_queued');
            }
            $this->error('Berikan --topic atau isi pengaturan topik blog AI.');
            return self::FAILURE;
        }

        $author = $this->resolveAuthor();
        if (! $author) {
            if ($scheduled) {
                Cache::forget('blog_ai_generation_queued');
            }
            $this->error('Tidak ada akun superadmin untuk menjadi penulis artikel AI.');
            return self::FAILURE;
        }

        $categoryId = $this->option('category') ?: Setting::val('blog_ai_category_id');
        $category = $categoryId ? ArticleCategory::find($categoryId) : null;
        $publish = (bool) $this->option('publish') || ($scheduled && Setting::val('blog_ai_auto_publish', '0') === '1');

        GenerateAiBlogArticle::dispatch($topic, $author->id, $category?->id, $publish, $scheduled);

        $this->info('Artikel AI telah masuk antrean'.($publish ? ' untuk dipublikasikan.' : ' sebagai draft.'));

        return self::SUCCESS;
    }

    private function shouldGenerateScheduledArticle(): bool
    {
        if (Setting::val('blog_ai_enabled', '0') !== '1') {
            return false;
        }

        $lastGeneratedAt = Setting::val('blog_ai_last_generated_at');
        if (! $lastGeneratedAt) {
            return true;
        }

        $lastGenerated = Carbon::parse($lastGeneratedAt);
        $frequency = Setting::val('blog_ai_frequency', 'daily');

        return $frequency === 'weekly'
            ? $lastGenerated->addWeek()->isPast()
            : $lastGenerated->addDay()->isPast();
    }

    private function pickScheduledTopic(): string
    {
        $topics = preg_split('/[\r\n,]+/', (string) Setting::val('blog_ai_topics', '')) ?: [];
        $topics = array_values(array_filter(array_map('trim', $topics)));

        return $topics === [] ? '' : $topics[array_rand($topics)];
    }

    private function resolveAuthor(): ?User
    {
        $configuredId = Setting::val('blog_ai_author_id');

        return $configuredId
            ? User::find($configuredId)
            : User::where('role', 'superadmin')->orderBy('id')->first();
    }
}
