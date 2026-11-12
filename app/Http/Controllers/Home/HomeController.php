<?php

namespace App\Http\Controllers\Home;

use App\Helpers\AppVersion;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\PromoEvent;
use App\Models\Setting;
use App\Models\User;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $articles = Article::published()->with(['user', 'category'])->latest('published_at')->take(3)->get();

        $homePlans = User::hostingPlans();
        $planPricing = [];
        $planPricingYearly = [];
        foreach ($homePlans as $slug => $plan) {
            $planPricing[$slug] = User::getPlanPricing($slug, 'monthly');
            $planPricingYearly[$slug] = User::getPlanPricing($slug, 'yearly');
        }

        $promos = PromoEvent::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'description' => $p->description,
                'banner_url' => $p->banner_url,
                'target_url' => $p->target_url,
            ])
            ->values();

        // Promo mahasiswa ikut ditayangkan di carousel yang sama selama aktif.
        if (Setting::val('student_promo_active', '0') == '1') {
            $promos->push([
                'id' => 'student-promo',
                'title' => 'Hosting gratis '.max(1, (int) Setting::val('student_promo_days', 30)).' hari untuk mahasiswa aktif',
                'description' => 'Verifikasi NIM dan nama Anda lewat PDDIKTI, langsung dapat paket '
                    .ucfirst((string) Setting::val('student_promo_plan', 'pro')).' tanpa kartu kredit.',
                'banner_url' => null,
                'target_url' => route('user_hosting.student_promo'),
            ]);
        }

        $seoTitle = 'Shared Hosting Indonesia, Auto Deploy Git & Jasa Pembuatan Website | Ryaze';
        $seoDescription = 'Shared hosting Indonesia untuk Laravel, Node.js, React, dan PHP dengan auto-deploy Git, SSL gratis, database MySQL, web terminal, serta jasa pembuatan website & aplikasi mulai Rp 10.000/bulan.';
        $faqItems = [
            ['q' => 'Apa itu Ryaze?', 'a' => 'Ryaze adalah platform shared hosting Indonesia dan jasa pembuatan website/aplikasi dengan fitur auto-deploy dari Git, SSL gratis Let\'s Encrypt, database MySQL, web terminal, dan panel kontrol lengkap.'],
            ['q' => 'Teknologi apa yang didukung di hosting Ryaze?', 'a' => 'Node.js, PHP, Laravel, React, Vue.js, Next.js, dan HTML statis dengan deploy otomatis dari repositori GitHub atau Git.'],
            ['q' => 'Apakah tersedia SSL gratis dan custom domain?', 'a' => 'Ya. Setiap project otomatis mendapatkan sertifikat SSL gratis via Let\'s Encrypt serta mendukung subdomain bawaan maupun custom domain (.id, .my.id, .com).'],
            ['q' => 'Apakah tersedia database MySQL?', 'a' => 'Ya. Tersedia database MySQL yang dapat dikelola melalui panel kontrol, mini phpMyAdmin, dan akses kredensial langsung.'],
            ['q' => 'Apakah bisa pesan jasa pembuatan website atau aplikasi custom?', 'a' => 'Bisa. Tim Ryaze melayani pembuatan sistem informasi, aplikasi web Laravel & React, SaaS, website perusahaan, hingga prototipe bisnis fungsional.'],
            ['q' => 'Bagaimana cara cek nama domain sebelum membeli?', 'a' => 'Gunakan alat Cek Nama Domain & WHOIS gratis di ryaze.my.id/whois untuk memeriksa ketersediaan domain, registrar, masa aktif, dan record DNS.'],
        ];
        $sameAs = array_values(array_filter([
            Setting::val('social_github'),
            Setting::val('social_instagram'),
            Setting::val('social_linkedin'),
        ]));

        return Inertia::render('Home', [
            'plans' => collect($homePlans)->map(fn ($p) => [
                'label' => $p['label'],
                'storage_mb' => $p['storage_mb'],
                'max_projects' => $p['max_projects'],
                'color' => $p['color'],
                'features' => $p['features'],
                'is_active' => $p['is_active'],
            ])->toArray(),
            'planPricing' => $planPricing,
            'planPricingYearly' => $planPricingYearly,
            'articles' => $articles->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'slug' => $a->slug,
                'excerpt' => $a->excerpt,
                'cover_image' => $a->cover_image ? asset('storage/'.$a->cover_image) : null,
                'category' => $a->category ? ['name' => $a->category->name] : null,
                'published_at' => $a->published_at?->format('d M Y'),
                'reading_time' => $a->reading_time,
                'url' => route('blog.show', $a->slug),
            ]),
            'starterPricing' => User::getPlanPricing('starter', 'monthly'),
            'starterPricingYearly' => User::getPlanPricing('starter', 'yearly'),
            'promos' => $promos,
            'siteName' => Setting::where('key', 'site_name')->value('value') ?? 'Ryaze',
            'socialLinks' => [
                'github' => Setting::val('social_github'),
                'instagram' => Setting::val('social_instagram'),
                'linkedin' => Setting::val('social_linkedin'),
            ],
            'url' => url('/'),
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
            'canonical' => url('/'),
            'ogType' => 'website',
            'ogImage' => asset('og-image.png'),
            'structuredData' => [
                array_filter([
                    '@context' => 'https://schema.org',
                    '@type' => 'Organization',
                    'name' => 'Ryaze',
                    'url' => route('home'),
                    'logo' => asset('og-image.png'),
                    'description' => $seoDescription,
                    'sameAs' => $sameAs ?: null,
                ]),
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebSite',
                    'name' => 'Ryaze',
                    'url' => route('home'),
                    'inLanguage' => 'id-ID',
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => route('blog.index').'?search={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => $seoTitle,
                    'description' => $seoDescription,
                    'url' => route('home'),
                    'inLanguage' => 'id-ID',
                    'publisher' => ['@type' => 'Organization', 'name' => 'Ryaze'],
                ],
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => array_map(fn (array $f) => [
                        '@type' => 'Question',
                        'name' => $f['q'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $f['a'],
                        ],
                    ], $faqItems),
                ],
            ],
            'favicon' => Setting::where('key', 'site_favicon')->value('value'),
            'version' => AppVersion::get(),
        ]);
    }
}