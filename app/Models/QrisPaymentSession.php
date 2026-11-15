<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Sesi halaman pembayaran QRIS Dinamis per invoice.
 *
 * Expiry disimpan agar timer pembayaran tidak mengulang dari awal ketika
 * user me-refresh atau membuka kembali halaman QRIS untuk invoice yang sama.
 * Sesi baru hanya dibuat ketika sesi lama sudah kedaluwarsa (ekuivalen dengan
 * tombol "Perbarui QR").
 */
class QrisPaymentSession extends Model
{
    protected $fillable = ['order_number', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
