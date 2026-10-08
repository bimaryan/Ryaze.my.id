<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TotpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

/**
 * Alur end-to-end 2FA: setup → confirm → login challenge → disable.
 *
 * Login diuji sampai sesi benar-benar ter-authenticate, karena bug paling
 * berbahaya di 2FA adalah "password benar tapi 2FA dilewati".
 */
class TwoFactorAuthTest extends TestCase
{
    use RefreshDatabase;

    private TotpService $totp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->totp = new TotpService;

        // Login mewajibkan Cloudflare Turnstile. Tanpa fake, proses akan
        // memanggil API Turnstile sungguhan dan selalu gagal di test.
        Http::fake([
            'challenges.cloudflare.com/*' => Http::response(['success' => true]),
        ]);
    }

    private function makeUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Bima',
            'email' => 'bima@example.com',
            'password' => Hash::make('RahasiaKuat123!'),
            'role' => 'user_hosting',
            'status' => 'active',
            'referral_code' => 'TESTREF01',
        ], $attributes));
    }

    /**
     * Pasang 2FA yang sudah terkonfirmasi, lalu kembalikan secret plaintext
     * untuk dipakai membuat OTP di test.
     */
    private function enableTwoFactor(User $user): string
    {
        $secret = $this->totp->generateSecret();
        [$hashed] = $this->totp->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => $this->totp->encryptSecret($secret),
            'two_factor_recovery_codes' => $hashed,
            'two_factor_confirmed_at' => now(),
        ])->save();

        return $secret;
    }

    private function currentOtp(string $secret): string
    {
        $g2fa = new Google2FA;
        $g2fa->setWindow(1);

        return $g2fa->getCurrentOtp($secret);
    }

    // ── Setup ────────────────────────────────────────────────────────────────

    public function test_setup_page_requires_authentication(): void
    {
        $this->get(route('two-factor.setup'))->assertRedirect(route('login'));
    }

    public function test_generate_creates_an_unconfirmed_secret(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->postJson(route('two-factor.generate'));

        $response->assertOk();
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $response->json('qr'));

        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
        // Belum dikonfirmasi → 2FA belum boleh aktif.
        $this->assertNull($user->two_factor_confirmed_at);
    }

    public function test_confirm_activates_two_factor_and_returns_recovery_codes(): void
    {
        $user = $this->makeUser();
        $secret = $this->totp->generateSecret();

        $user->forceFill([
            'two_factor_secret' => $this->totp->encryptSecret($secret),
        ])->save();

        $this->actingAs($user)
            ->post(route('two-factor.confirm'), ['code' => $this->currentOtp($secret)])
            ->assertRedirect(route('two-factor.recovery-codes'))
            ->assertSessionHas('recovery_codes');

        $user->refresh();
        $this->assertNotNull($user->two_factor_confirmed_at);
        $this->assertCount(8, $user->two_factor_recovery_codes);
    }

    public function test_confirm_rejects_an_invalid_code(): void
    {
        $user = $this->makeUser();
        $secret = $this->totp->generateSecret();

        $user->forceFill(['two_factor_secret' => $this->totp->encryptSecret($secret)])->save();

        $this->actingAs($user)
            ->post(route('two-factor.confirm'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $user->refresh();
        $this->assertNull($user->two_factor_confirmed_at, 'Kode salah tidak boleh mengaktifkan 2FA.');
    }

    // ── Login challenge ──────────────────────────────────────────────────────

    public function test_login_with_two_factor_redirects_to_challenge_and_is_not_authenticated(): void
    {
        $user = $this->makeUser();
        $this->enableTwoFactor($user);

        $response = $this->post(route('login.process'), [
            'email' => $user->email,
            'password' => 'RahasiaKuat123!',
            'cf-turnstile-response' => 'test-token',
        ]);

        $response->assertRedirect(route('two-factor.challenge'));

        // Sesi belum boleh login penuh sebelum 2FA diverifikasi.
        $this->assertGuest();
        $this->assertSame($user->id, session('2fa.user_id'));
    }

    public function test_correct_otp_completes_the_login(): void
    {
        $user = $this->makeUser();
        $secret = $this->enableTwoFactor($user);

        $this->post(route('login.process'), [
            'email' => $user->email,
            'password' => 'RahasiaKuat123!',
            'cf-turnstile-response' => 'test-token',
        ]);

        $this->post(route('two-factor.challenge.verify'), [
            'code' => $this->currentOtp($secret),
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_otp_keeps_the_user_logged_out(): void
    {
        $user = $this->makeUser();
        $this->enableTwoFactor($user);

        $this->post(route('login.process'), [
            'email' => $user->email,
            'password' => 'RahasiaKuat123!',
            'cf-turnstile-response' => 'test-token',
        ]);

        $this->post(route('two-factor.challenge.verify'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_a_recovery_code_can_be_used_to_log_in_and_is_then_burned(): void
    {
        $user = $this->makeUser();
        $secret = $this->enableTwoFactor($user);
        [$hashed, $plain] = $this->totp->generateRecoveryCodes();
        $user->forceFill(['two_factor_recovery_codes' => $hashed])->save();

        $this->post(route('login.process'), [
            'email' => $user->email,
            'password' => 'RahasiaKuat123!',
            'cf-turnstile-response' => 'test-token',
        ]);

        $this->post(route('two-factor.challenge.verify'), ['code' => $plain[0]]);
        $this->assertAuthenticatedAs($user);

        // Kode tersebut harus hangus, tidak bisa dipakai ulang.
        $user->refresh();
        $this->assertCount(7, $user->two_factor_recovery_codes);
        $this->assertNotContains(hash('sha256', $plain[0]), $user->two_factor_recovery_codes);
    }

    public function test_challenge_verification_is_rate_limited(): void
    {
        $user = $this->makeUser();
        $this->enableTwoFactor($user);

        $this->post(route('login.process'), [
            'email' => $user->email,
            'password' => 'RahasiaKuat123!',
            'cf-turnstile-response' => 'test-token',
        ]);

        // 5 percobaan gagal, lalu ke-6 harus diblokir.
        foreach (range(1, 5) as $i) {
            $this->post(route('two-factor.challenge.verify'), ['code' => '00000'.$i]);
        }

        $this->post(route('two-factor.challenge.verify'), ['code' => '999999'])
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_challenge_cannot_be_skipped_without_a_pending_login(): void
    {
        $this->post(route('two-factor.challenge.verify'), ['code' => '123456'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_without_two_factor_logs_in_normally(): void
    {
        $user = $this->makeUser();

        $this->post(route('login.process'), [
            'email' => $user->email,
            'password' => 'RahasiaKuat123!',
            'cf-turnstile-response' => 'test-token',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_unconfirmed_secret_does_not_block_login(): void
    {
        // Setup sudah dimulai tapi user belum verifikasi OTP-nya.
        // User ini TIDAK boleh terjebak di halaman challenge.
        $user = $this->makeUser();
        $user->forceFill([
            'two_factor_secret' => $this->totp->encryptSecret($this->totp->generateSecret()),
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->post(route('login.process'), [
            'email' => $user->email,
            'password' => 'RahasiaKuat123!',
            'cf-turnstile-response' => 'test-token',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    // ── Disable ──────────────────────────────────────────────────────────────

    public function test_disable_requires_password_and_a_valid_otp(): void
    {
        $user = $this->makeUser();
        $secret = $this->enableTwoFactor($user);

        // Password salah → ditolak, 2FA tetap aktif.
        $this->actingAs($user)
            ->delete(route('two-factor.disable'), [
                'password' => 'PasswordSalah',
                'code' => $this->currentOtp($secret),
            ])
            ->assertSessionHasErrors('password');

        $this->assertNotNull($user->fresh()->two_factor_confirmed_at);

        // OTP salah → ditolak, 2FA tetap aktif.
        $this->actingAs($user)
            ->delete(route('two-factor.disable'), [
                'password' => 'RahasiaKuat123!',
                'code' => '000000',
            ])
            ->assertSessionHasErrors('code');

        $this->assertNotNull($user->fresh()->two_factor_confirmed_at);
    }

    public function test_disable_with_valid_credentials_turns_two_factor_off(): void
    {
        $user = $this->makeUser();
        $secret = $this->enableTwoFactor($user);

        $this->actingAs($user)
            ->delete(route('two-factor.disable'), [
                'password' => 'RahasiaKuat123!',
                'code' => $this->currentOtp($secret),
            ])
            ->assertRedirect(route('two-factor.setup'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
    }

    public function test_after_disable_login_no_longer_challenges(): void
    {
        $user = $this->makeUser();
        $secret = $this->enableTwoFactor($user);

        $this->actingAs($user)
            ->delete(route('two-factor.disable'), [
                'password' => 'RahasiaKuat123!',
                'code' => $this->currentOtp($secret),
            ]);

        $this->post(route('logout'));

        $this->post(route('login.process'), [
            'email' => $user->email,
            'password' => 'RahasiaKuat123!',
            'cf-turnstile-response' => 'test-token',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }
}
