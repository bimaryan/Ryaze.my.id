<?php

namespace App\Jobs;

use App\Models\ArticleCategory;
use App\Models\Setting;
use App\Models\User;
use App\Services\AiBlogGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateAiBlogArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    public function __construct(
        public string $topic,
        public int $authorId,
        public ?int $categoryId = null,
        public bool $publish = false,
        public bool $scheduled = false,
    ) {
    }

    public function handle(AiBlogGenerator $generator): void
    {
        $author = User::findOrFail($this->authorId);
        $category = $this->categoryId ? ArticleCategory::find($this->categoryId) : null;

        $article = $generator->generate($this->topic, $author, $category, $this->publish);

        if ($this->scheduled) {
            Setting::setVal('blog_ai_last_generated_at', now()->toIso8601String());
        }

        Log::info('Artikel AI berhasil dibuat.', [
            'article_id' => $article->id,
            'topic' => $this->topic,
            'scheduled' => $this->scheduled,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Pembuatan artikel AI gagal.', [
            'topic' => $this->topic,
            'scheduled' => $this->scheduled,
            'message' => $exception->getMessage(),
        ]);
    }
}
