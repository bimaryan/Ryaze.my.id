import DashboardLayout from '../../../Layouts/DashboardLayout';
import { Link } from '@inertiajs/react';

export default function Billing({ payments }) {
    const getStatus = (status) => {
        const map = {
            paid: { class: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300', label: 'LUNAS' },
            verified: { class: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300', label: 'LUNAS' },
            pending: { class: 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300', label: 'PENDING' },
            pending_verification: { class: 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300', label: 'MENUNGGU VERIFIKASI' },
            rejected: { class: 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300', label: 'GAGAL' },
            failed: { class: 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300', label: 'GAGAL' },
        };
        return map[status] || { class: 'bg-[#fafafa] dark:bg-white/5 text-[#666] dark:text-white/60', label: status?.toUpperCase() || '-' };
    };

    return (
        <DashboardLayout title="Riwayat Tagihan">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-file-invoice-dollar text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Riwayat Tagihan</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Daftar lengkap transaksi dan status pembayaran pesanan joki Anda.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href="/user/joki/dashboard" className="inline-flex justify-center items-center bg-[#fafafa] dark:bg-white/5 border border-[#e5e5e5] dark:border-[#1a1a2e] hover:bg-[#f5f0ff] dark:hover:bg-white/10 text-[#666] dark:text-white/60 px-4 py-2 rounded-lg text-sm font-medium transition">
                            &larr; Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm text-left">
                            <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <tr>
                                    <th className="px-6 py-4">Nomor Pesanan</th>
                                    <th className="px-6 py-4">Pembayaran</th>
                                    <th className="px-6 py-4">Jumlah</th>
                                    <th className="px-6 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {payments?.length > 0 ? payments.map((payment, idx) => {
                                    const st = getStatus(payment.status);
                                    return (
                                        <tr key={idx} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                            <td className="px-6 py-4 font-semibold text-[#333] dark:text-white">
                                                {payment.order?.order_number || '-'}
                                            </td>
                                            <td className="px-6 py-4">{payment.payment_name}</td>
                                            <td className="px-6 py-4 font-mono">Rp {Number(payment.amount || 0).toLocaleString('id-ID')}</td>
                                            <td className="px-6 py-4 text-center">
                                                <span className={`text-xs font-bold px-2 py-1 rounded-full ${st.class}`}>
                                                    {st.label}
                                                </span>
                                            </td>
                                        </tr>
                                    );
                                }) : (
                                    <tr>
                                        <td colSpan="4" className="px-6 py-10 text-center text-[#999] dark:text-white/40">Belum ada riwayat tagihan.</td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
