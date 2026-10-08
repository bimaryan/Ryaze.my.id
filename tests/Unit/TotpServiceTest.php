<?php

namespace Tests\Unit;

use App\Services\TotpService;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

/**
 * Verifikasi bahwa implementasi TOTP kita kompatibel dengan kode yang
 * dihasilkan pustaka standar (RFC 6238), sehingga kode dari Google
 * Authenticator / Aegis benar-benar diterima.
 */
class TotpServiceTest extends TestCase
{
    private TotpService $totp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->totp = new TotpService;
    }

    public function test_generated_secret_is_valid_base32(): void
    {
        $secret = $this->totp->generateSecret();

        $this->assertMatchesRegularExpression('/^[A-Z2-7]{16}$/', $secret);
    }

    public function test_it_accepts_a_code_produced_by_the_reference_library(): void
    {
        $secret = $this->totp->generateSecret();
        $reference = new Google2FA;
        $reference->setWindow(1);

        $code = $reference->getCurrentOtp($secret);

        $this->assertTrue($this->totp->verify($secret, $code));
    }

    public function test_it_rejects_a_wrong_code(): void
    {
        $secret = $this->totp->generateSecret();

        $this->assertFalse($this->totp->verify($secret, '000000'));
    }

    public function test_it_rejects_malformed_codes_without_throwing(): void
    {
        $secret = $this->totp->generateSecret();

        $this->assertFalse($this->totp->verify($secret, ''));
        $this->assertFalse($this->totp->verify($secret, 'abc'));
        $this->assertFalse($this->totp->verify($secret, '12345'));
        $this->assertFalse($this->totp->verify($secret, '1234567'));
    }

    public function test_it_verifies_an_encrypted_secret(): void
    {
        $secret = $this->totp->generateSecret();
        $encrypted = $this->totp->encryptSecret($secret);

        // Isi database tidak boleh sama dengan secret asli.
        $this->assertNotSame($secret, $encrypted);

        $reference = new Google2FA;
        $this->assertTrue($this->totp->verify($encrypted, $reference->getCurrentOtp($secret)));
    }

    public function test_provisioning_uri_contains_the_expected_fields(): void
    {
        $secret = $this->totp->generateSecret();
        $uri = $this->totp->provisioningUri($secret, 'user@example.com', 'Ryaze');

        $this->assertStringStartsWith('otpauth://totp/', $uri);
        $this->assertStringContainsString('secret='.$secret, $uri);
        $this->assertStringContainsString('issuer=Ryaze', $uri);
    }

    public function test_qr_code_is_returned_as_an_svg_data_uri(): void
    {
        $secret = $this->totp->generateSecret();
        $uri = $this->totp->provisioningUri($secret, 'user@example.com');
        $dataUri = $this->totp->qrCodeDataUri($uri);

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $dataUri);

        $svg = base64_decode(substr($dataUri, strlen('data:image/svg+xml;base64,')));
        $this->assertStringContainsString('<svg', $svg);
    }

    public function test_recovery_codes_are_generated_and_returned_hashed(): void
    {
        [$hashed, $plain] = $this->totp->generateRecoveryCodes();

        $this->assertCount(8, $plain);
        $this->assertCount(8, $hashed);

        foreach ($plain as $i => $code) {
            // Kode jelas terbaca oleh manusia...
            $this->assertMatchesRegularExpression('/^[A-Z2-9]{4}-[A-Z2-9]{4}$/', $code);
            // ...tetapi yang tersimpan di DB adalah hash-nya.
            $this->assertSame(hash('sha256', $code), $hashed[$i]);
            $this->assertNotSame($code, $hashed[$i]);
        }
    }

    public function test_a_recovery_code_can_only_be_used_once(): void
    {
        [$hashed, $plain] = $this->totp->generateRecoveryCodes();
        $target = $plain[0];

        $remaining = $this->totp->consumeRecoveryCode($hashed, $target);
        $this->assertCount(7, $remaining);

        // Kode yang sama tidak boleh diterima dua kali.
        $again = $this->totp->consumeRecoveryCode($remaining, $target);
        $this->assertCount(7, $again);
    }

    public function test_unknown_recovery_code_leaves_the_list_untouched(): void
    {
        [$hashed] = $this->totp->generateRecoveryCodes();

        $this->assertCount(8, $this->totp->consumeRecoveryCode($hashed, 'ZZZZ-ZZZZ'));
    }

    public function test_has_two_factor_enabled_requires_a_confirmed_secret(): void
    {
        // Secret ada tapi belum dikonfirmasi → belum aktif (tidak boleh memblokir login).
        $pending = (object) [
            'two_factor_secret' => $this->totp->encryptSecret($this->totp->generateSecret()),
            'two_factor_confirmed_at' => null,
        ];
        $this->assertFalse($this->totp->hasTwoFactorEnabled($pending));

        $active = (object) [
            'two_factor_secret' => $pending->two_factor_secret,
            'two_factor_confirmed_at' => now(),
        ];
        $this->assertTrue($this->totp->hasTwoFactorEnabled($active));

        $this->assertFalse($this->totp->hasTwoFactorEnabled((object) [
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]));
    }
}
