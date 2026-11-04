<?php

namespace App\Http\Controllers\Hosting\User;

use App\Http\Controllers\Controller;
use App\Models\HostingBilling;
use App\Models\HostingPayment;
use App\Models\Setting;
use App\Models\StudentVerification;
use App\Models\User;
use App\Services\PddiktiService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class StudentPromoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $promoActive = $this->promoActive();
        $planSlug = $this->promotedPlan();
        $planConfig = $planSlug ? User::hostingPlans()[$planSlug] : null;
        $days = (int) Setting::val('student_promo_days', 30);
        $verification = StudentVerification::where('user_id', $user->id)->first();

        return view('pages.hosting.user.student_promo', compact(
            'promoActive', 'planSlug', 'planConfig', 'days', 'verification'
        ));
    }

    public function verify(Request $request)
    {
        $user = Auth::user();

        if (in_array($user->role, ['superadmin', 'admin_hosting'])) {
            abort(403, 'Promo ini hanya untuk mahasiswa.');
        }

        $limiterKey = 'student-promo-verify:'.$user->id;
        if (RateLimiter::tooManyAttempts($limiterKey, 5)) {
            return back()->with('error', 'Terlalu banyak percobaan. Coba lagi dalam 1 menit.');
        }
        RateLimiter::hit($limiterKey, 60);

        $request->validate([
            'nim' => 'required|string|max:30',
            'nama' => 'required|string|min:3|max:150',
        ]);

        if (! $this->promoActive()) {
            return back()->with('error', 'Promo mahasiswa saat ini sedang tidak aktif.');
        }

        if (StudentVerification::where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Anda sudah pernah mengklaim promo mahasiswa. Promonya hanya boleh diklaim 1x per akun.');
        }

        $planSlug = $this->promotedPlan();
        if (! $planSlug) {
            return back()->with('error', 'Konfigurasi promo bermasalah. Hubungi admin melalui tiket bantuan.');
        }

        $currentPlan = $user->getActivePlan();
        if ($currentPlan !== null && $currentPlan !== 'free') {
            return back()->with('error', 'Maaf, promo ini hanya untuk akun yang belum memiliki paket berlangganan aktif.');
        }

        $nim = PddiktiService::normalizeNim($request->input('nim'));
        $namaInput = trim((string) $request->input('nama'));

        if (StudentVerification::nimTaken($nim)) {
            return back()->with('error', 'NIM ini sudah pernah digunakan oleh akun lain. Satu NIM hanya boleh dipakai 1 akun.');
        }

        $result = app(PddiktiService::class)->verify($nim, $namaInput);

        if (! $result['ok']) {
            return back()->withInput(['nim' => $nim])->with('error', $result['message']);
        }

        $days = max(1, (int) Setting::val('student_promo_days', 30));

        try {
            DB::transaction(function () use ($user, $result, $planSlug, $days, $nim, $namaInput, $request) {
                StudentVerification::create([
                    'user_id' => $user->id,
                    'nim' => $nim,
                    'pddikti_id' => $result['record']['pddikti_id'],
                    'nama_input' => $namaInput,
                    'nama_pddikti' => $result['record']['nama_pddikti'],
                    'nama_pt' => $result['record']['nama_pt'],
                    'nama_prodi' => $result['record']['nama_prodi'],
                    'jenjang' => $result['record']['jenjang'],
                    'pddikti_status' => $result['record']['pddikti_status'],
                    'ip_address' => $request->ip(),
                    'verified_at' => now(),
                ]);

                $this->grantFreePlan($user, $planSlug, $days, $nim);
            });
        } catch (QueryException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) === 19 || str_contains($e->getMessage(), 'Duplicate entry')) {
                return back()->with('error', 'NIM ini sudah pernah digunakan oleh akun lain. Satu NIM hanya boleh dipakai 1 akun.');
            }

            throw $e;
        }

        return redirect()
            ->route('user_hosting.student_promo')
            ->with('success', "Selamat! Paket {$planSlug} gratis berhasil diaktifkan selama {$days} hari untuk mahasiswa aktif.");
    }

    private function promoActive(): bool
    {
        return Setting::val('student_promo_active', '0') == '1';
    }

    /**
     * Plan yang dipromosikan (default: pro). Hanya plan paid & aktif yang valid.
     */
    private function promotedPlan(): ?string
    {
        $planSlug = trim((string) Setting::val('student_promo_plan', 'pro'));

        if (! in_array($planSlug, ['starter', 'pro', 'business'], true)) {
            return null;
        }

        return (User::hostingPlans()[$planSlug]['is_active'] ?? true) ? $planSlug : null;
    }

    /**
     * Aktifkan langganan gratis — meniru pola aktivasi instan pada
     * DashboardController::subscribe() untuk harga Rp 0.
     */
    private function grantFreePlan(User $user, string $planSlug, int $days, ?string $nim = null): void
    {
        $planLimits = User::getPlanLimits($planSlug);

        $activeBilling = $user->hostingBillings()->where('status', 'active')->latest()->first();
        $oldPlanBaseStorage = $activeBilling
            ? (User::getPlanLimits($activeBilling->plan)['storage_mb'] ?? 1024)
            : 1024;
        $currentLimit = $user->hosting_storage_limit_mb ?? 1024;
        $extraStorage = max(0, $currentLimit - $oldPlanBaseStorage);

        $newDueDate = now()->addDays($days);

        if ($activeBilling) {
            HostingBilling::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('id', '!=', $activeBilling->id)
                ->update(['status' => 'canceled']);

            $activeBilling->update([
                'plan_name' => 'Paket '.ucfirst($planSlug).' (Promo Mahasiswa)',
                'plan' => $planSlug,
                'amount' => 0,
                'billing_cycle' => 'monthly',
                'status' => 'active',
                'next_due_date' => $newDueDate,
            ]);
        } else {
            HostingBilling::create([
                'user_id' => $user->id,
                'hosting_project_id' => null,
                'plan_name' => 'Paket '.ucfirst($planSlug).' (Promo Mahasiswa)',
                'plan' => $planSlug,
                'amount' => 0,
                'billing_cycle' => 'monthly',
                'status' => 'active',
                'next_due_date' => $newDueDate,
            ]);
        }

        $user->update(['hosting_storage_limit_mb' => ($planLimits['storage_mb'] ?? 1024) + $extraStorage]);

        HostingPayment::where('user_id', $user->id)
            ->where('invoice_number', 'like', 'HST-INV-%')
            ->where('status', 'unpaid')
            ->delete();

        HostingPayment::create([
            'user_id' => $user->id,
            'hosting_project_id' => null,
            'invoice_number' => 'HST-INV-'.strtoupper(uniqid()),
            'amount' => 0,
            'status' => 'paid',
            'payment_method' => 'Promo Mahasiswa',
            'paid_at' => now(),
            'notes' => json_encode([
                'plan' => $planSlug,
                'billing_cycle' => 'monthly',
                'promo' => 'mahasiswa',
                'nim' => $nim,
            ]),
        ]);
    }
}
