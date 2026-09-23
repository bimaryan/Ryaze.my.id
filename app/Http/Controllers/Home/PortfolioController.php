<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Setting;
use App\Models\Education;
use App\Models\Experience;
use App\Models\SkillGroup;
use App\Models\TechBadge;

class PortfolioController extends Controller
{
    private function getPortfolioData()
    {
        $portfolios = Portfolio::where('is_active', true)
            ->latest()
            ->get();

        $allTags = $portfolios
            ->pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $profile = [
            'name'        => Setting::val('profile_name')       ?? 'Bima Ryan Alfarizi',
            'title'       => Setting::val('profile_title')      ?? 'Full-Stack Developer & Software Engineer',
            'bio'         => Setting::val('profile_bio')        ?? 'Passionate developer yang gemar membangun produk digital dari nol — dari arsitektur backend yang solid hingga UI yang intuitif. Fokus pada kode yang bersih, scalable, dan berdampak nyata.',
            'location'    => Setting::val('profile_location')   ?? 'Indonesia',
            'avatar'      => Setting::val('profile_avatar'),
            'github'      => Setting::val('social_github'),
            'instagram'   => Setting::val('social_instagram'),
            'linkedin'    => Setting::val('social_linkedin'),
            'email'       => Setting::val('contact_email'),
            'whatsapp'    => Setting::val('contact_whatsapp'),
        ];

        $educations = Education::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $experiences = Experience::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $skillGroups = SkillGroup::with(['skills' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $techBadges = TechBadge::orderBy('sort_order')->get();

        return compact('portfolios', 'allTags', 'profile', 'educations', 'experiences', 'skillGroups', 'techBadges');
    }

    public function index()
    {
        $data = $this->getPortfolioData();
        return view('pages.portfolio.index', $data);
    }

    public function downloadResume()
    {
        $data = $this->getPortfolioData();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.portfolio.resume_ats', $data);
        return $pdf->download('Resume_' . str_replace(' ', '_', $data['profile']['name']) . '.pdf');
    }
}
