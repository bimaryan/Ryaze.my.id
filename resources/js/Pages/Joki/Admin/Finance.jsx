import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, Link } from '@inertiajs/react';

function formatRupiah(num) {
    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}, ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
}

export default function Finance({ payments, totalRevenue }) {
    return (
        <DashboardLayout title="Keuangan Joki">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-wallet text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Keuangan Joki</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Laporan pendapatan dan riwayat pembayaran lunas.</p>
                    </div>
                    <div className="ml-auto">
                        <div className="inline-flex items-center px-4 py-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-500/30 rounded-lg font-bold text-sm">
                            <i className="fa-solid fa-wallet me-2"></i>
                            Total Pendapatan: {formatRupiah(totalRevenue)}
                        </div>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm text-left">
                            <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <tr>
                                    <th className="px-6 py-4">Waktu Lunas</th>
                                    <th className="px-6 py-4">Pesanan</th>
                                    <th className="px-6 py-4">Klien</th>
                                    <th className="px-6 py-4">Layanan</th>
                                    <th className="px-6 py-4 text-right">Jumlah Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {payments?.data?.length > 0 ? payments.data.map((payment, idx) => (
                                    <tr key={idx} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4 text-xs text-[#999] dark:text-white/40 font-mono">
                                            {formatDate(payment.paid_at)}
                                        </td>
                                        <td className="px-6 py-4 font-bold text-[#333] dark:text-white">
                                            <Link href={`/admin/joki/orders/${payment.order?.hashid}/edit`}
                                                className="text-[#7c3aed] dark:text-[#a78bfa] hover:text-[#6d28d9] dark:hover:text-[#a78bfa]">
                                                {payment.order?.order_number}
                                            </Link>
                                        </td>
                                        <td className="px-6 py-4">
                                            {payment.order?.client?.name ?? 'Unknown'}
                                        </td>
                                        <td className="px-6 py-4 text-xs font-semibold text-[#999] dark:text-white/40 uppercase">
                                            {payment.order?.service?.name ?? '-'}
                                        </td>
                                        <td className="px-6 py-4 text-right font-mono font-medium text-emerald-600 dark:text-emerald-300">
                                            + {formatRupiah(payment.amount)}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-10 text-center text-[#999] dark:text-white/40">Belum ada pendapatan yang tercatat.</td>
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
