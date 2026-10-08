<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Two-Factor Authentication (TOTP).
 *
 * Alur:
 *  1. Setup  — secret dibuat, ditampilkan sebagai QR + teks, belum aktif.
 *  2. Confirm — user memasukkan kode dari authenticator, secret dikonfirmasi & aktif.
 *  3. Login challenge — setelah password benar, diminta OTP atau recovery code.
 *  4. Disable — memerlukan password + kode OTP yang valid.
 */
class TwoFactorController extends Controller
{
    /**
     * Maksimal percobaan verifikasi OTP.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lama lockout dalam menit.
     */
    private const LOCKOUT_MINUTES = 5;

    public function __construct(
        private readonly TotpService $totp
    ) {}

    /**
     * Halaman pengaturan 2FA (QR, secret manual, status).
     */
    public function setup(Request $request)
    {
        $user = $request->user();

        return view('pages.auth.two-factor-setup', [
            'user' => $user,
            'enabled' => $this->totp->hasTwoFactorEnabled($user),
            'issuer' => config('app.name', 'Ryaze Portal'),
        ]);
    }

    /**
     * Buat secret baru dan kembalikan QR + secret manual.
     *
     * Secret disimpan terenkripsi namun BELUM dikonfirmasi, jadi user yang
     * menutup halaman di tengah jalan tidak terkunci dari akunnya sendiri.
     */
    public function generate(Request $request)
    {
        $user = $request->user();

        // Ganti secret setiap kali/setup diulang.
        $request->session()->forget('2fa_recovery_plain');

        $secret = $this->totp->generateSecret();
        $user->forceFill([
            'two_factor_secret' => $this->totp->encryptSecret($secret),
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $uri = $this->totp->provisioningUri($secret, $user->email);

        return response()->json([
            'qr' => $this->totp->qrCodeDataUri($uri),
            'secret' => $secret,
            'uri' => $uri,
        ]);
    }

    /**
     * Konfirmasi secret dengan kode OTP pertama.
     */
    public function confirm(Request $request)
    {
        $user = $request->user();
        $throttleKey = '2fa-confirm|'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'code' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $secret = $user->two_factor_secret;

        // Tidak ada setup aktif → kembalikan ke halaman pengaturan.
        if (empty($secret)) {
            return back()->withErrors(['code' => 'Silakan generate QR terlebih dahulu.']);
        }

        if (! $this->totp->verify($secret, $request->input('code'))) {
            RateLimiter::hit($throttleKey, self::LOCKOUT_MINUTES * 60);

            return back()->withErrors(['code' => 'Kode OTP tidak valid.'])
                ->withInput();
        }

        [$hashed, $plain] = $this->totp->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $hashed,
        ])->save();

        RateLimiter::clear($throttleKey);

        // Kode pemulihan hanya ditampilkan sekali, di halaman konfirmasi.
        return redirect()
            ->route('two-factor.recovery-codes')
            ->with('recovery_codes', $plain);
    }

    /**
     * Halaman kode pemulihan (sekali tampil).
     */
    public function recoveryCodes(Request $request)
    {
        if (! $this->totp->hasTwoFactorEnabled($request->user())) {
            return redirect()->route('two-factor.setup');
        }

        return view('pages.auth.two-factor-recovery', [
            'codes' => $request->session()->get('recovery_codes', []),
        ]);
    }

    /**
     * Buat ulang kode pemulihan (mengganti seluruh kode lama).
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $user = $request->user();

        if (! $this->totp->hasTwoFactorEnabled($user)) {
            return redirect()->route('two-factor.setup');
        }

        $this->assertValidOtp($request, $user);

        [$hashed, $plain] = $this->totp->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => $hashed,
        ])->save();

        return redirect()
            ->route('two-factor.recovery-codes')
            ->with('recovery_codes', $plain)
            ->with('success', 'Kode pemulihan berhasil dibuat ulang. Kode lama tidak berlaku lagi.');
    }

    /**
     * Nonaktifkan 2FA. Memerlukan password + OTP valid.
     */
    public function disable(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => 'required|current_password',
            'code' => 'required|digits:6',
        ], [
            'password.current_password' => 'Password tidak sesuai.',
        ]);

        $this->assertValidOtp($request, $user);

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect()
            ->route('two-factor.setup')
            ->with('success', 'Two-Factor Authentication berhasil dinonaktifkan.');
    }

    /**
     * Halaman tantangan OTP setelah password benar.
     */
    public function challenge(Request $request)
    {
        return view('pages.auth.two-factor-challenge');
    }

    /**
     * Verifikasi OTP / recovery code pada login challenge.
     */
    public function verifyChallenge(Request $request)
    {
        $userId = $request->session()->get('2fa.user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            $request->session()->forget(['2fa.user_id', '2fa.remember']);

            return redirect()->route('login')
                ->withErrors(['email' => 'Sesi login tidak ditemukan. Silakan login ulang.']);
        }

        $request->validate([
            'code' => 'required|string|max:32',
        ]);

        $throttleKey = '2fa-verify|'.$user->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'code' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $verified = false;
        $code = trim($request->input('code'));

        // 6 digit → OTP; selain itu coba sebagai recovery code.
        if (preg_match('/^\d{6}$/', $code)) {
            $verified = $this->totp->verify($user->two_factor_secret, $code);
        } else {
            $codes = $user->two_factor_recovery_codes ?? [];

            if (! empty($codes) && in_array(hash('sha256', $code), $codes, true)) {
                $user->two_factor_recovery_codes = $this->totp->consumeRecoveryCode($codes, $code);
                $user->save();
                $verified = true;
            }
        }

        if (! $verified) {
            RateLimiter::hit($throttleKey, self::LOCKOUT_MINUTES * 60);

            return back()->withErrors([
                'code' => 'Kode tidak valid atau sudah dipakai.',
            ])->onlyInput('code');
        }

        RateLimiter::clear($throttleKey);

        $remember = (bool) $request->session()->pull('2fa.remember');
        $request->session()->forget('2fa.user_id');

        // Samakan dengan login password: regenerate session cegah fixation.
        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('two-factor.challenge'));
    }

    /**
     * Pastikan OTP valid untuk operasi sensitif (disable / regenerate).
     */
    private function assertValidOtp(Request $request, $user): void
    {
        if (! $this->totp->verify($user->two_factor_secret, (string) $request->input('code'))) {
            throw ValidationException::withMessages([
                'code' => 'Kode OTP tidak valid.',
            ]);
        }
    }
}
