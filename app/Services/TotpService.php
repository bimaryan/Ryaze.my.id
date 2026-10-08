<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

/**
 * Pembungkus TOTP (RFC 6238) untuk Two-Factor Authentication.
 *
 * Semua rahasia TOTP disimpan dalam bentuk terenkripsi (Laravel Crypt) sehingga
 * kebocoran isi kolom di database tidak langsung menghasilkan 2FA yang bisa
 * dibypass. Kode pemulihan (recovery codes) disimpan sebagai hash.
 */
class TotpService
{
    /**
     * Jumlah digit kode TOTP.
     */
    private const DIGITS = 6;

    /**
     * Panjang secret Base32. Harus pangkat dua untuk kompatibilitas
     * Google Authenticator.
     */
    private const SECRET_LENGTH = 16;

    /**
     * Toleransi waktu (jumlah step 30 detik ke depan/belakang).
     * 1 berarti menerima kode milik step sebelumnya & berikutnya untuk
     * Mouvement_taburung pada jam perangkat yang sedikit meleset.
     */
    private const WINDOW = 1;

    /**
     * Jumlah kode pemulihan yang dibuat.
     */
    private const RECOVERY_CODE_COUNT = 8;

    /**
     * Generate secret TOTP baru (Base32).
     *
     * Panjang secret TETAP 16 karakter Base32. Panjang wajib pangkat dua
     * agar kompatibel dengan Google Authenticator (lihat Base32::isCharCountNotAPowerOfTwo),
     * dan 16 karakter memberi 80 bit entropy yang jauh di atas kebutuhan TOTP.
     */
    public function generateSecret(): string
    {
        return $this->google2fa()->generateSecretKey(self::SECRET_LENGTH);
    }

    /**
     * Bangun URI otpauth:// yang dipakai authenticator app untuk QR.
     */
    public function provisioningUri(string $secret, string $accountName, string $issuer = 'Ryaze Portal'): string
    {
        return $this->google2fa()->getQRCodeUrl(
            $issuer,
            $accountName,
            $secret,
            self::DIGITS
        );
    }

    /**
     * Render provisioning URI menjadi QR code SVG (disimpan di data URI).
     */
    public function qrCodeDataUri(string $provisioningUri): string
    {
        $writer = new Writer(new ImageRenderer(
            new RendererStyle(192, 1),
            new SvgImageBackEnd
        ));

        $svg = $writer->writeString($provisioningUri);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Verifikasi kode OTP 6 digit.
     *
     * Dibandingkan dengan hash_equals agar tidak bocor lewat timing attack.
     */
    public function verify(string $secret, string $code): bool
    {
        $code = trim($code);

        if (! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        // Secret disimpan terenkripsi; dibongkar di sini.
        $plainSecret = $this->decryptSecret($secret);

        if ($plainSecret === null) {
            return false;
        }

        try {
            return (bool) $this->google2fa()->verifyKey($plainSecret, $code, self::WINDOW);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Buat kode pemulihan acak, sudah di-hash dan siap disimpan.
     *
     * @return array{0: array<int, string>, 1: string} [hash, plaintext]
     */
    public function generateRecoveryCodes(): array
    {
        $plain = [];
        $hashed = [];

        for ($i = 0; $i < self::RECOVERY_CODE_COUNT; $i++) {
            $code = $this->randomCode();
            $plain[] = $code;
            $hashed[] = hash('sha256', $code);
        }

        return [$hashed, $plain];
    }

    /**
     * Konsumsi satu kode pemulihan. Mengembalikan kode tersisa (hash).
     *
     * @param  array<int, string>  $hashedCodes
     * @return array<int, string>
     */
    public function consumeRecoveryCode(array $hashedCodes, string $code): array
    {
        $candidate = hash('sha256', trim($code));

        foreach ($hashedCodes as $index => $hashed) {
            if (hash_equals($hashed, $candidate)) {
                unset($hashedCodes[$index]);

                return array_values($hashedCodes);
            }
        }

        return $hashedCodes;
    }

    /**
     * Enkripsi secret agar tidak tersimpan sebagai plaintext.
     */
    public function encryptSecret(string $secret): string
    {
        return encrypt($secret);
    }

    /**
     * 2FA dianggap aktif hanya bila secret ada DAN sudah dikonfirmasi.
     * Secret yang dibuat tapi belum dikonfirmasi tidak boleh memblokir login.
     */
    public function hasTwoFactorEnabled(?object $user): bool
    {
        return ! empty($user->two_factor_secret)
            && ! empty($user->two_factor_confirmed_at);
    }

    /**
     * Instance Google2FA.
     */
    private function google2fa(): Google2FA
    {
        $g2fa = new Google2FA;

        // Server sering sedikit di belakang jam perangkat, jadi longgarkan
        // redeem window default agar kode tidak sering ditolak.
        $g2fa->setWindow(self::WINDOW);

        return $g2fa;
    }

    /**
     * Bongkar secret terenkripsi.
     */
    private function decryptSecret(string $encrypted): ?string
    {
        try {
            return decrypt($encrypted);
        } catch (\Throwable) {
            // Secret lama yang tidak terenkripsi (jika ada) tetap bisa dipakai.
            if (preg_match('/^[A-Z2-7]+=*$/', $encrypted)) {
                return $encrypted;
            }

            return null;
        }
    }

    /**
     * Kode pemulihan format XXXX-XXXX (huruf ambigu seperti O/0/I/1 dihilangkan).
     */
    private function randomCode(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';

        for ($i = 0; $i < 8; $i++) {
            if ($i === 4) {
                $code .= '-';
            }

            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $code;
    }
}
