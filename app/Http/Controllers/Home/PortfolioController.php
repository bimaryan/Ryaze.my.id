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
use App\Models\DigitalProduct;
use App\Models\DigitalPurchase;
use App\Models\Tip;
use App\Services\GitHubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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

        $products = DigitalProduct::where('is_active', true)
            ->latest()
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
            'products', 'githubUsername', 'githubStats'
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

    public function checkoutProduct($slug)
    {
        $product = DigitalProduct::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $orderId = 'PRD-' . strtoupper(uniqid());

        DigitalPurchase::create([
            'product_id' => $product->id,
            'order_id' => $orderId,
            'amount' => $product->price,
            'status' => 'pending',
        ]);

        return redirect()->route('portfolio.products.status', $orderId);
    }

    public function purchaseStatus($order_id)
    {
        $purchase = DigitalPurchase::with('product')
            ->where('order_id', $order_id)
            ->firstOrFail();

        $payUrl = $purchase->status === 'pending'
            ? pakasir_pay_url($purchase->amount, $purchase->order_id, route('portfolio.products.status', $order_id))
            : null;

        return view('pages.portfolio.purchase-status', [
            'purchase' => $purchase,
            'payUrl' => $payUrl,
        ]);
    }

    public function downloadProduct($order_id)
    {
        $purchase = DigitalPurchase::with('product')
            ->where('order_id', $order_id)
            ->firstOrFail();

        if (!$purchase->isPaid()) {
            return redirect()
                ->route('portfolio.products.status', $purchase->order_id)
                ->with('error', 'Pembayaran belum diterima. Download terkunci.');
        }

        $product = $purchase->product;
        if (!Storage::disk('local')->exists($product->file_path)) {
            return redirect()
                ->route('portfolio.products.status', $purchase->order_id)
                ->with('error', 'File tidak ditemukan. Hubungi saya jika masalah berlanjut.');
        }

        $purchase->product->increment('download_count');

        return Storage::disk('local')->download($product->file_path, $product->file_name);
    }

    public function createTip(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000|max:10000000',
        ], [
            'amount.min' => 'Minimal tip Rp 10.000.',
            'amount.max' => 'Maksimal tip Rp 10.000.000.',
        ]);

        $orderId = 'TIP-' . strtoupper(uniqid());

        Tip::create([
            'order_id' => $orderId,
            'amount' => $request->integer('amount'),
            'status' => 'pending',
        ]);

        return redirect(pakasir_pay_url(
            $request->integer('amount'),
            $orderId,
            route('portfolio.tip.success', $orderId)
        ));
    }

    public function tipSuccess($order_id)
    {
        $tip = Tip::where('order_id', $order_id)->firstOrFail();

        return view('pages.portfolio.tip-success', compact('tip'));
    }
}
