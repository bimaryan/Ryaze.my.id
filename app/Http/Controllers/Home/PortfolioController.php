<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Setting;
use App\Models\Education;
use App\Models\Experience;
use App\Models\SkillGroup;
use App\Models\TechBadge;
use App\Models\Testimonial;
use App\Models\Certification;
use App\Models\ContactMessage;
use App\Services\GitHubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $certifications = Certification::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $githubUsername = '';
        if (!empty($profile['github'])) {
            $parts = explode('/', parse_url($profile['github'], PHP_URL_PATH));
            $githubUsername = end($parts) ?: '';
        }
        $githubStats = $githubUsername ? app(GitHubService::class)->getStats($githubUsername) : null;

        return compact(
            'portfolios', 'allTags', 'profile', 'educations', 'experiences',
            'skillGroups', 'techBadges', 'testimonials', 'certifications',
            'githubUsername', 'githubStats'
        );
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

    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'cf-turnstile-response' => 'required',
        ], [
            'cf-turnstile-response.required' => 'Mohon selesaikan tantangan CAPTCHA.',
        ]);

        $response = Http::asForm()->timeout(8)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $request->input('cf-turnstile-response'),
            'remoteip' => $request->ip(),
        ]);

        if (!$response->json('success')) {
            return back()->withErrors(['cf-turnstile-response' => 'CAPTCHA tidak valid atau kadaluarsa.'])->withInput();
        }

        ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return redirect()->route('portfolio.index')->with('success', 'Pesan terkirim! Terima kasih telah menghubungi saya.');
    }
}
