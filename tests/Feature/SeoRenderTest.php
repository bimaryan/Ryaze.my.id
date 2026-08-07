<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Jasa Pembuatan Website');
        $response->assertSee('application/ld+json', false);
        $response->assertSee('FAQPage', false);
        $response->assertSee('og-image.png', false);
    }

    public function test_sitemap_renders(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        $this->assertStringStartsWith('text/xml', $response->headers->get('Content-Type'));
    }

    public function test_blog_pages_render(): void
    {
        $user = User::factory()->create(['role' => 'user_hosting']);
        $category = ArticleCategory::create(['name' => 'Tips Hosting', 'slug' => 'tips-hosting']);
        $article = Article::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Cara Deploy Laravel di Ryaze',
            'slug' => 'cara-deploy-laravel-di-ryaze',
            'excerpt' => 'Panduan deploy Laravel.',
            'body' => '<p>Panduan lengkap deploy Laravel di hosting Ryaze.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $show = $this->get('/blog/cara-deploy-laravel-di-ryaze');
        $show->assertOk();
        $show->assertSee('BreadcrumbList', false);
        $show->assertSee('application/ld+json', false);
    }
}
