import DashboardLayout from '../../../Layouts/DashboardLayout';
import { Link } from '@inertiajs/react';

export default function Progress({ activeOrders }) {
    const statusBadge = (status) => {
        const map = {
            pending: 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40',
            progress: 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-300 border border-blue-200 dark:border-blue-500/40',
            review: 'bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-300 border border-purple-200 dark:border-purple-500/40',
        };
        const label = {
            pending: 'Pending',
            progress: 'In Progress',
            review: 'Butuh Review',
        };
        return (
            <span className={`px-2.5 py-1 rounded-full text-xs font-medium ${map[status] || ''}`}>
                {label[status] || status}
            </span>
        );
    };

    return (
        <DashboardLayout title="Progres Pesanan Joki">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-spinner text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Progres Pesanan Joki</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Pantau status pengerjaan proyek Anda yang sedang aktif.</p>
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
                                    <th className="px-6 py-4 whitespace-nowrap">Nama Proyek</th>
                                    <th className="px-6 py-4 whitespace-nowrap">Tech Stack</th>
                                    <th className="px-6 py-4 whitespace-nowrap w-48">Progres</th>
                                    <th className="px-6 py-4 whitespace-nowrap text-center">Status</th>
                                    <th className="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {activeOrders?.length > 0 ? activeOrders.map((order, idx) => (
                                    <tr key={idx} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4 font-medium text-[#333] dark:text-white whitespace-nowrap">
                                            {order.project_name}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className="bg-[#fafafa] dark:bg-white/5 text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e] text-xs px-2.5 py-1 rounded-md font-medium">
                                                {order.tech_stack || 'Menunggu Info'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 w-48 align-middle">
                                            <div className="flex justify-between text-xs mb-1.5">
                                                <span className="font-medium text-[#999] dark:text-white/40">Berjalan</span>
                                                <span className="font-bold text-[#7c3aed] dark:text-[#a78bfa]">{order.progress}%</span>
                                            </div>
                                            <div className="w-full bg-[#e5e5e5] dark:bg-white/10 rounded-full h-2 overflow-hidden">
                                                <div className="bg-[#7c3aed] h-2 rounded-full transition-all duration-500" style={{ width: `${order.progress}%` }}></div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-center whitespace-nowrap">
                                            {statusBadge(order.status)}
                                        </td>
                                        <td className="px-6 py-4 text-center whitespace-nowrap">
                                            <Link href={`/user/joki/orders/${order.hashid}`}
                                                className="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 hover:bg-[#7c3aed] hover:text-white transition-all duration-200 shadow-sm"
                                                title="Detail Proyek">
                                                <i className="fa-solid fa-file-lines"></i>
                                            </Link>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-12 text-center">
                                            <div className="flex flex-col items-center justify-center">
                                                <div className="w-16 h-16 mb-4 bg-[#fafafa] dark:bg-white/5 text-[#999] dark:text-white/30 rounded-full flex items-center justify-center text-2xl">
                                                    <i className="fa-solid fa-folder-open"></i>
                                                </div>
                                                <p className="text-[#999] dark:text-white/40 font-medium">Anda belum memiliki proyek yang sedang berjalan.</p>
                                                <Link href="/user/joki/create" className="mt-2 text-[#7c3aed] dark:text-[#a78bfa] hover:text-[#6d28d9] dark:hover:text-white text-sm font-semibold hover:underline">
                                                    Pesan Proyek Sekarang &rarr;
                                                </Link>
                                            </div>
                                        </td>
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
