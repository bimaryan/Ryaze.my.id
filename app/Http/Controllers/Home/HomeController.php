<?php

namespace App\Http\Controllers\Home;

use App\Helpers\AppVersion;
use App\Http\Controllers\Controller;
use App\Models\Article;
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
        foreach ($homePlans as $slug => $plan) {
            $planPricing[$slug] = User::getPlanPricing($slug);
        }

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
            'starterPricing' => User::getPlanPricing('starter'),
            'siteName' => Setting::where('key', 'site_name')->value('value') ?? 'Ryaze',
            'socialLinks' => [
                'github' => Setting::val('social_github'),
                'instagram' => Setting::val('social_instagram'),
                'linkedin' => Setting::val('social_linkedin'),
            ],
            'url' => url('/'),
            'title' => 'Jasa Pembuatan Website & Shared Hosting Indonesia',
            'description' => 'Jasa pembuatan website, aplikasi, dan shared hosting Indonesia. Hosting murah dengan auto-deploy, SSL gratis, database MySQL, web terminal, dan panel kontrol lengkap. Mulai dari Rp 10.000/bulan.',
            'favicon' => Setting::where('key', 'site_favicon')->value('value'),
            'version' => AppVersion::get(),
        ]);
    }
}
