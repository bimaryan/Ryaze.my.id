<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\InfoUpdate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SeoRenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_landing_page_renders(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Jasa Pembuatan Website');
        $head = explode('</head>', $response->getContent(), 2)[0];
        $this->assertStringContainsString('<title>Jasa Pembuatan Website &amp; Shared Hosting Indonesia | Ryaze</title>', $head);
        $this->assertStringContainsString('application/ld+json', $head);
        $this->assertStringContainsString('"@type":"WebPage"', $head);
        $this->assertStringContainsString('og-image.png', $head);
        $this->assertStringContainsString('rel="canonical" href="' . route('home') . '"', $head);
    }

    public function test_sitemap_renders(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        $this->assertStringStartsWith('text/xml', $response->headers->get('Content-Type'));
        $response->assertSee(url('/whois'), false);
    }

    public function test_legal_pages_are_public_and_have_seo_metadata(): void
    {
        foreach ([
            'privacy' => ['Legal/Privacy', 'Kebijakan Privasi'],
            'terms' => ['Legal/Terms', 'Syarat & Ketentuan'],
        ] as $routeName => [$component, $title]) {
            $response = $this->get(route($routeName));
            $response->assertOk()->assertInertia(fn (Assert $page) => $page
                ->component($component)
                ->where('seoTitle', $title . ' | Ryaze')
                ->where('canonical', route($routeName))
                ->where('ogImage', asset('og-image.png'))
                ->where('robots', 'index, follow, max-image-preview:large')
                ->has('seoDescription')
            );
            $head = explode('</head>', $response->getContent(), 2)[0];
            $this->assertStringContainsString('<title>' . e($title) . ' | Ryaze</title>', $head);
            $this->assertStringContainsString('rel="canonical" href="' . route($routeName) . '"', $head);
            $this->assertStringContainsString('name="robots" content="index, follow, max-image-preview:large"', $head);
            $this->assertFalse($response->headers->has('X-Robots-Tag'));
        }
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
            'cover_image' => 'articles/deploy.png',
            'meta_title' => 'Tutorial Deploy Laravel',
            'meta_description' => 'Langkah deploy Laravel di Ryaze.',
        ]);

        $show = $this->get('/blog/cara-deploy-laravel-di-ryaze');
        $show->assertOk();
        $head = explode('</head>', $show->getContent(), 2)[0];
        $this->assertStringContainsString('<title>Tutorial Deploy Laravel | Ryaze News</title>', $head);
        $this->assertStringContainsString('name="description" content="Langkah deploy Laravel di Ryaze."', $head);
        $this->assertStringContainsString('rel="canonical" href="' . route('blog.show', $article->slug) . '"', $head);
        $this->assertStringContainsString('property="og:image" content="' . asset('storage/articles/deploy.png') . '"', $head);
        preg_match_all('/<script[^>]*type="application\/ld\+json"[^>]*>(.*?)<\/script>/s', $head, $matches);
        $schemas = array_map(fn ($json) => json_decode($json, true, 512, JSON_THROW_ON_ERROR), $matches[1]);
        $this->assertSame(['NewsArticle', 'BreadcrumbList'], array_column($schemas, '@type'));
        $this->assertSame(asset('storage/articles/deploy.png'), $schemas[0]['image'][0]);
    }

    public function test_login_is_noindex_in_the_actual_head_and_response_headers(): void
    {
        $response = $this->get('/login');
        $response->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $head = explode('</head>', $response->getContent(), 2)[0];
        $this->assertStringContainsString('<title>Masuk | Ryaze</title>', $head);
        $this->assertStringContainsString('name="robots" content="noindex, nofollow"', $head);
    }

    public function test_search_is_noindex_and_pagination_has_a_distinct_canonical(): void
    {
        $response = $this->get('/blog?search=hosting&utm_source=test');
        $response->assertOk();
        $head = explode('</head>', $response->getContent(), 2)[0];
        $this->assertStringContainsString('name="robots" content="noindex, follow"', $head);
        $this->assertStringContainsString('rel="canonical" href="' . route('blog.index') . '"', $head);

        $response = $this->get('/blog?page=2&utm_source=test');
        $response->assertOk();
        $head = explode('</head>', $response->getContent(), 2)[0];
        $this->assertStringContainsString('rel="canonical" href="' . route('blog.index') . '?page=2"', $head);
    }

    public function test_sitemap_only_contains_published_content_and_real_public_routes(): void
    {
        $user = User::factory()->create(['role' => 'user_hosting']);
        $publicCategory = ArticleCategory::create(['name' => 'Publik', 'slug' => 'publik']);
        $hiddenCategory = ArticleCategory::create(['name' => 'Terjadwal', 'slug' => 'terjadwal']);
        foreach ([
            ['slug' => 'artikel-publik', 'category_id' => $publicCategory->id, 'status' => 'published', 'published_at' => now()->subDay()],
            ['slug' => 'artikel-terjadwal', 'category_id' => $hiddenCategory->id, 'status' => 'published', 'published_at' => now()->addDay()],
            ['slug' => 'artikel-draft', 'category_id' => $hiddenCategory->id, 'status' => 'draft', 'published_at' => null],
        ] as $attributes) {
            Article::create(array_merge([
                'user_id' => $user->id, 'title' => $attributes['slug'], 'body' => '<p>Isi artikel.</p>',
            ], $attributes));
        }
        foreach ([
            ['slug' => 'update-publik', 'is_active' => true, 'published_at' => now()->subDay()],
            ['slug' => 'update-terjadwal', 'is_active' => true, 'published_at' => now()->addDay()],
            ['slug' => 'update-nonaktif', 'is_active' => false, 'published_at' => now()->subDay()],
        ] as $attributes) {
            InfoUpdate::create(array_merge([
                'author_id' => $user->id, 'title' => $attributes['slug'], 'description' => 'Info terbaru.',
                'category' => 'announcement', 'priority' => 'normal',
            ], $attributes));
        }

        $response = $this->get('/sitemap.xml');
        $response->assertOk();
        $xml = new \DOMDocument();
        $this->assertTrue($xml->loadXML($response->getContent()));
        $response->assertSee(route('blog.show', 'artikel-publik'), false);
        $response->assertSee(route('blog.category', 'publik'), false);
        $response->assertSee(route('updates.show', 'update-publik'), false);
        $response->assertSee(route('privacy'), false);
        $response->assertSee(route('terms'), false);
        foreach (['artikel-terjadwal', 'artikel-draft', '/kategori/terjadwal', 'update-terjadwal', 'update-nonaktif', '/login', '/register'] as $excluded) {
            $response->assertDontSee($excluded, false);
        }

        $this->travel(1)->hours();
        $this->assertSame($response->getContent(), $this->get('/sitemap.xml')->getContent());
    }
}
