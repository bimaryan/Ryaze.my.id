<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;

class QrisService
{
    /**
     * Payload QRIS Statis default milik Ryaze, Komputer (DANA / NMID ID1026627992070).
     */
    public const DEFAULT_BASE_PAYLOAD = '00020101021126570011ID.DANA.WWW011893600915303652180502090365218050303UMI51440014ID.CO.QRIS.WWW0215ID10266279920700303UMI5204737253033605802ID5915Ryaze, Komputer6009Indramayu6105452816304FE23';

    /**
     * Dapatkan payload base statis (bisa dari config, file, atau default).
     */
    public function getBasePayload(): string
    {
        $configured = config('services.qris.base_payload');
        if (!empty($configured)) {
            return trim($configured);
        }

        return self::DEFAULT_BASE_PAYLOAD;
    }

    /**
     * Hitung Checksum CRC16-CCITT (Polinomial 0x1021, Init 0xFFFF).
     */
    public function calculateCrc16(string $data): string
    {
        $crc = 0xFFFF;
        $length = strlen($data);

        for ($i = 0; $i < $length; $i++) {
            $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /**
     * Parse raw string EMVCo menjadi array Tag => Value.
     *
     * @return array<string, string>
     */
    public function parseTlv(string $raw): array
    {
        $tags = [];
        $offset = 0;
        $length = strlen($raw);

        while ($offset < $length) {
            if ($offset + 4 > $length) {
                break;
            }

            $tag = substr($raw, $offset, 2);
            $len = (int) substr($raw, $offset + 2, 2);
            $val = substr($raw, $offset + 4, $len);

            $tags[$tag] = $val;
            $offset += 4 + $len;
        }

        return $tags;
    }

    /**
     * Susun array Tag => Value menjadi payload string EMVCo (diurutkan berdasarkan tag).
     *
     * @param array<string, string> $tags
     */
    public function buildTlv(array $tags): string
    {
        // Tag 63 dihandle khusus di akhir bersama CRC
        unset($tags['63']);

        ksort($tags, SORT_STRING);

        $result = '';
        foreach ($tags as $tag => $val) {
            $len = str_pad((string) strlen($val), 2, '0', STR_PAD_LEFT);
            $result .= $tag . $len . $val;
        }

        // Tambah Tag 63 (CRC) dengan panjang 04
        $result .= '6304';
        $crc = $this->calculateCrc16($result);

        return $result . $crc;
    }

    /**
     * Generate payload QRIS Dinamis berdasarkan nominal dan referensi invoice.
     *
     * @param int|float $amount Nominal transaksi dalam Rupiah (e.g. 50000)
     * @param string|null $orderId Nomor invoice / referensi transaksi (opsional)
     * @param string|null $basePayload Override base static QRIS jika ada
     */
    public function generateDynamicPayload(int|float $amount, ?string $orderId = null, ?string $basePayload = null): string
    {
        $base = $basePayload ?: $this->getBasePayload();
        $tags = $this->parseTlv($base);

        // 1. Ubah Tag 01 (Point of Initiation Method) menjadi '12' (Dinamis)
        $tags['01'] = '12';

        // 2. Format Tag 54 (Transaction Amount)
        // Format nominal: integer jika tidak ada desimal (standar QRIS Indonesia e.g. "15000")
        $amountInt = (int) round($amount);
        $amountStr = (string) $amountInt;
        $tags['54'] = $amountStr;

        // 3. (Opsional) Tambahkan Tag 62 (Additional Data Field) jika orderId ada
        // Subtag 01 = Bill Number / Invoice
        if (!empty($orderId)) {
            // Batasi panjang order ID agar tidak melebihi kapasitas EMVCo
            $cleanOrderId = substr(preg_replace('/[^A-Za-z0-9\-_]/', '', $orderId), 0, 25);
            $subtag = '01' . str_pad((string) strlen($cleanOrderId), 2, '0', STR_PAD_LEFT) . $cleanOrderId;
            $tags['62'] = $subtag;
        }

        return $this->buildTlv($tags);
    }

    /**
     * Render payload menjadi SVG string menggunakan BaconQrCode.
     */
    public function generateQrSvg(string $payload, int $size = 280): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return $writer->writeString($payload);
    }

    /**
     * Render payload menjadi Data URI (SVG base64) siap pakai di atribut src <img>.
     */
    public function generateQrDataUri(string $payload, int $size = 280): string
    {
        $svg = $this->generateQrSvg($payload, $size);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Ambil metadata merchant dari payload.
     *
     * @return array{merchant_name: string, city: string, postal_code: string, nmid: string}
     */
    public function getMerchantInfo(?string $basePayload = null): array
    {
        $base = $basePayload ?: $this->getBasePayload();
        $tags = $this->parseTlv($base);

        $nmid = '';
        if (isset($tags['51'])) {
            // Tag 51 berisi subtag 02 (NMID, panjang 15 char diawali ID)
            if (preg_match('/0215(ID\d{13})/', $tags['51'], $matches)) {
                $nmid = $matches[1];
            }
        }

        return [
            'merchant_name' => $tags['59'] ?? 'Ryaze, Komputer',
            'city'          => $tags['60'] ?? 'Indramayu',
            'postal_code'   => $tags['61'] ?? '45281',
            'nmid'          => $nmid ?: 'ID1026627992070',
        ];
    }
}
