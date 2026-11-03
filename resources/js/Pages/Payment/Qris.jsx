import { Head, router } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import PublicLayout from '../../Layouts/PublicLayout';

function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID').format(amount);
}

export default function Qris({ order, amount, dynamicPayload, qrDataUri, merchant }) {
    const [isPaid, setIsPaid] = useState(false);
    const [statusText, setStatusText] = useState('Menunggu Pembayaran...');
    const [copied, setCopied] = useState(false);
    const [note, setNote] = useState('');
    const [confirmed, setConfirmed] = useState(Boolean(order?.confirmed_at));
    const [sending, setSending] = useState(false);
    const intervalRef = useRef(null);

    const checkUrl = `/payment/qris/${order?.order_id}/status`;
    const confirmUrl = `/payment/qris/${order?.order_id}/confirm`;
    const waNumber = merchant?.contact_whatsapp || '';
    const waMsg = encodeURIComponent(
        `Halo Admin Ryaze, saya telah membayar tagihan Invoice ${order?.order_id} sebesar Rp ${formatRupiah(amount)} via QRIS Dinamis. Mohon diverifikasi.`
    );

    const sendConfirmation = () => {
        if (confirmed || sending) return;
        setSending(true);
        router.post(
            confirmUrl,
            { note },
            {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setSending(false),
                onSuccess: () => setConfirmed(true),
            }
        );
    };

    // Konfirmasi juga dicatat saat user memilih jalur WA, supaya admin dapat notifikasi
    // dan invoice-nya muncul di antrean verifikasi.
    const confirmAndOpenWa = () => {
        sendConfirmation();
    };

    useEffect(() => {
        intervalRef.current = setInterval(() => {
            fetch(checkUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data && data.is_paid) {
                        clearInterval(intervalRef.current);
                        setIsPaid(true);
                        setStatusText('Pembayaran Berhasil! Mengalihkan...');
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1500);
                    }
                })
                .catch(() => {});
        }, 3000);

        return () => clearInterval(intervalRef.current);
    }, []);

    const copyAmount = () => {
        navigator.clipboard.writeText(String(amount)).then(() => {
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        });
    };

    return (
        <>
            <Head title={`Pembayaran QRIS — ${order?.title} | Ryaze`}>
                <meta name="description" content={`Halaman pembayaran QRIS Dinamis untuk ${order?.title} — Ryaze`} />
                <meta name="robots" content="noindex,nofollow" />
            </Head>

            <PublicLayout>
                <main className="bg-[#F4F8FC] dark:bg-[#0A1322] min-h-[calc(100vh-4rem)] flex items-center justify-center py-16 px-4 mt-10">
                    <div className="w-full max-w-md space-y-5">

                        {/* Flash: konfirmasi manual */}
                        {order?.flash_info && (
                            <div className="bg-[#E8F0F9] dark:bg-[#12233A] border border-[#C9D5E2] dark:border-[#1D5B96] text-[#1D5B96] dark:text-[#90BFE0] px-4 py-3 text-xs flex items-center gap-2.5">
                                <i className="fa-solid fa-circle-info text-sm"></i>
                                <span>{order.flash_info}</span>
                            </div>
                        )}

                        {/* ============ QRIS CARD ============ */}
                        <div className="bg-white dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] overflow-hidden shadow-sm dark:shadow-2xl">

                            {/* QRIS Official Top Bar (White) */}
                            <div className="bg-white px-6 py-3.5 flex items-center justify-between border-b border-slate-200 dark:border-[#2A4463]">
                                <div className="flex items-center gap-2">
                                    <span className="font-black text-xl tracking-tighter text-red-600" style={{ fontFamily: 'monospace' }}>QRIS</span>
                                    <span className="text-[9px] uppercase font-bold text-slate-500 leading-none">
                                        Pembayaran<br />Digital
                                    </span>
                                </div>
                                <div className="text-right">
                                    <span className="font-bold text-xs text-slate-800 tracking-wider">GPN</span>
                                    <div className="text-[8px] text-slate-400 font-mono">PASPORT GPN</div>
                                </div>
                            </div>

                            {/* Merchant Info */}
                            <div className="bg-[#F4F8FC] dark:bg-[#12233A] px-6 py-4 border-b border-[#E3EAF1] dark:border-[#2A4463] text-center">
                                <h2 className="text-base font-black text-[#0F1A2A] dark:text-white tracking-tight">
                                    {merchant?.merchant_name}
                                </h2>
                                <p className="font-mono text-[10px] text-[#525252] dark:text-[#8A9BAE] mt-0.5 tracking-wider">
                                    NMID: <span className="text-[#1D5B96] dark:text-[#6BA8DC] font-semibold">{merchant?.nmid}</span>{' '}
                                    &middot; {merchant?.city}
                                </p>
                            </div>

                            {/* Amount + Invoice */}
                            <div className="px-6 pt-5 pb-4 text-center border-b border-[#E3EAF1] dark:border-[#2A4463] bg-[#F4F8FC]/60 dark:bg-[#0A1322]/50">
                                <span className="font-mono text-[9px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] block mb-1">
                                    {order?.title}
                                </span>
                                <div className="text-3xl font-black text-[#0F1A2A] dark:text-white tracking-tight flex items-center justify-center gap-2">
                                    <span>Rp {formatRupiah(amount)}</span>
                                    <button
                                        type="button"
                                        onClick={copyAmount}
                                        title="Salin Nominal"
                                        className="text-[#8A9BAE] hover:text-[#1D5B96] dark:text-[#3D556E] dark:hover:text-[#6BA8DC] transition-colors text-sm"
                                    >
                                        <i className={`fa-${copied ? 'solid fa-check text-emerald-500 dark:text-emerald-400' : 'regular fa-copy'}`}></i>
                                    </button>
                                </div>
                                <div className="mt-2 inline-flex items-center gap-2 font-mono text-[10px] text-[#525252] dark:text-[#4A7A9B] bg-[#F0F6FC] dark:bg-[#12233A] px-2.5 py-1 border border-[#E3EAF1] dark:border-[#2A4463]">
                                    <span>INVOICE:</span>
                                    <span className="text-[#0F1A2A] dark:text-white font-semibold">{order?.order_id}</span>
                                </div>
                            </div>

                            {/* QR Code */}
                            <div className="p-6 flex flex-col items-center justify-center bg-white dark:bg-[#0F1D30]">
                                <div className="relative p-4 bg-white border-2 border-white shadow-xl flex items-center justify-center max-w-[280px] w-full aspect-square">
                                    <img
                                        src={qrDataUri}
                                        alt="QRIS Dinamis Ryaze"
                                        className="w-full h-full object-contain"
                                    />
                                </div>

                                {/* Download QR */}
                                <div className="mt-4 w-full max-w-[280px]">
                                    <a
                                        href={qrDataUri}
                                        download={`QRIS-${order?.order_id}.svg`}
                                        className="ui-btn ui-btn-secondary w-full"
                                    >
                                        <i className="fa-solid fa-download text-xs text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                                        <span>Unduh QR</span>
                                    </a>
                                </div>

                                {/* Live Status Indicator */}
                                {isPaid ? (
                                    <div className="mt-5 w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-[11px] font-mono">
                                        <i className="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-sm"></i>
                                        <span className="text-emerald-700 dark:text-emerald-400 font-bold uppercase tracking-wider">
                                            {statusText}
                                        </span>
                                    </div>
                                ) : (
                                    <div className="mt-5 w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-[#F4F8FC] dark:bg-[#0A1322] border border-[#E3EAF1] dark:border-[#2A4463] text-[11px] font-mono">
                                        <span className="w-2 h-2 rounded-full bg-amber-400 animate-ping shrink-0"></span>
                                        <span className="text-[#525252] dark:text-[#8A9BAE] uppercase tracking-wider">{statusText}</span>
                                    </div>
                                )}
                            </div>

                            {/* Action Buttons */}
                            <div className="px-6 py-4 bg-[#F4F8FC] dark:bg-[#12233A] border-t border-[#E3EAF1] dark:border-[#2A4463] space-y-3">
                                <textarea
                                    value={note}
                                    onChange={(e) => setNote(e.target.value)}
                                    rows={2}
                                    maxLength={500}
                                    placeholder="Tambahkan catatan untuk admin (opsional), mis. nama pengirim bank/e-wallet"
                                    className="ui-field ui-field-sm w-full resize-none"
                                />

                                {confirmed ? (
                                    <div className="ui-btn ui-btn-secondary w-full pointer-events-none opacity-80">
                                        <i className="fa-solid fa-clock-rotate-left text-xs text-[#1D5B96] dark:text-[#6BA8DC]"></i>
                                        <span>Konfirmasi Terkirim — Menunggu Verifikasi Admin</span>
                                    </div>
                                ) : (
                                    <button
                                        type="button"
                                        onClick={sendConfirmation}
                                        disabled={sending}
                                        className="ui-btn ui-btn-primary w-full"
                                    >
                                        <i className="fa-solid fa-circle-check text-xs"></i>
                                        <span>{sending ? 'Mengirim…' : 'Saya Sudah Bayar'}</span>
                                    </button>
                                )}

                                <a
                                    href={`https://wa.me/62${waNumber}?text=${waMsg}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    onClick={confirmAndOpenWa}
                                    className="ui-btn ui-btn-success w-full"
                                >
                                    <i className="fa-brands fa-whatsapp text-sm"></i>
                                    <span>Konfirmasi via WhatsApp</span>
                                </a>

                                <div className="flex items-center justify-end text-xs pt-1">
                                    <a
                                        href={order?.redirect_url || '/'}
                                        className="font-mono text-[10px] text-[#525252] hover:text-[#1D5B96] dark:text-[#4A7A9B] dark:hover:text-[#6BA8DC] uppercase tracking-wider transition-colors"
                                    >
                                        Kembali <i className="fa-solid fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {/* Petunjuk Pembayaran */}
                        <div className="bg-white dark:bg-[#0F1D30] border border-[#E3EAF1] dark:border-[#2A4463] p-5 space-y-3 text-xs">
                            <p className="font-mono text-[10px] uppercase tracking-[0.2em] text-[#1D5B96] dark:text-[#6BA8DC] font-semibold flex items-center gap-2">
                                <i className="fa-solid fa-circle-question"></i>
                                CARA PEMBAYARAN
                            </p>
                            <ol className="list-decimal list-inside space-y-2 text-[#525252] dark:text-[#8A9BAE] leading-relaxed">
                                <li>Buka aplikasi m-Banking (BCA, Mandiri, BRI, BNI) atau e-Wallet (GoPay, OVO, DANA, ShopeePay).</li>
                                <li>Pilih menu <strong className="text-[#0F1A2A] dark:text-white">Scan / Bayar QRIS</strong>.</li>
                                <li>Arahkan kamera ke kode QR di atas (atau unggah gambar jika pakai fitur Galeri).</li>
                                <li>
                                    Periksa nama penerima: <strong className="text-[#0F1A2A] dark:text-white">Ryaze, Komputer</strong> dan
                                    pastikan nominal otomatis terisi <strong className="text-[#0F1A2A] dark:text-white">Rp {formatRupiah(amount)}</strong>.
                                </li>
                                <li>Masukkan PIN Anda untuk menyelesaikan transaksi.</li>
                            </ol>
                        </div>

                        {/* Footer Note */}
                        <p className="text-center font-mono text-[10px] text-[#94A3B4] dark:text-[#3D556E] pb-2">
                            &copy; {new Date().getFullYear()} Ryaze &mdash; Pembayaran QRIS sesuai standar Bank Indonesia.
                        </p>
                    </div>
                </main>
            </PublicLayout>
        </>
    );
}
