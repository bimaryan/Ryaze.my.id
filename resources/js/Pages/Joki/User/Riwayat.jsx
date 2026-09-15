import DashboardLayout from '../../../Layouts/DashboardLayout';
import { Link } from '@inertiajs/react';

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

export default function Riwayat({ historyOrders }) {
    return (
        <DashboardLayout title="Riwayat Pesanan Joki">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-history text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Riwayat Pesanan Joki</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Lihat kembali riwayat dan daftar proyek joki Anda yang sudah lalu.</p>
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
                                    <th className="px-6 py-4">Nama Proyek</th>
                                    <th className="px-6 py-4">Tech Stack</th>
                                    <th className="px-6 py-4 text-center">Status Akhir</th>
                                    <th className="px-6 py-4 text-center">Tanggal Selesai</th>
                                    <th className="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {historyOrders?.length > 0 ? historyOrders.map((order, idx) => (
                                    <tr key={idx} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4 font-medium text-[#333] dark:text-white">{order.project_name}</td>
                                        <td className="px-6 py-4">
                                            <span className="bg-[#fafafa] dark:bg-white/5 text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] text-xs px-2 py-1 rounded font-medium">
                                                {order.tech_stack || '-'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            {order.status === 'completed' ? (
                                                <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40">Selesai</span>
                                            ) : (
                                                <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-500/40">Dibatalkan</span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            {formatDate(order.updated_at)}
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <Link href={`/user/joki/orders/${order.hashid}`}
                                                className="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 hover:bg-[#7c3aed] hover:text-white transition-all duration-200 shadow-sm"
                                                title="Lihat Detail">
                                                <i className="fa-solid fa-file-lines"></i>
                                            </Link>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-10 text-center text-[#999] dark:text-white/40">Anda belum memiliki riwayat proyek yang selesai.</td>
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
