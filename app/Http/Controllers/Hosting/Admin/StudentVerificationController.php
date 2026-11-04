<?php

namespace App\Http\Controllers\Hosting\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\StudentVerification;
use Illuminate\Http\Request;

class StudentVerificationController extends Controller
{
    public function index()
    {
        $verifications = StudentVerification::with('user')
            ->latest('verified_at')
            ->paginate(20);

        $settings = [
            'active' => Setting::val('student_promo_active', '0') == '1',
            'plan' => (string) Setting::val('student_promo_plan', 'pro'),
            'days' => (int) Setting::val('student_promo_days', 30),
            'api_base' => (string) Setting::val('pddikti_api_base', 'https://pddikti.kemdiktisaintek.go.id'),
        ];

        return view('pages.hosting.admin.student_verifications', compact('verifications', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'student_promo_active' => 'required|in:0,1',
            'student_promo_plan' => 'required|in:starter,pro,business',
            'student_promo_days' => 'required|integer|min:1|max:365',
            'pddikti_api_base' => 'required|url|max:500',
        ]);

        foreach ($data as $key => $value) {
            Setting::setVal($key, (string) $value);
        }

        return back()->with('success', 'Pengaturan promo mahasiswa diperbarui.');
    }
}
