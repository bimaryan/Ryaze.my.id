import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, Link } from '@inertiajs/react';

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function isPast(dateStr) {
    if (!dateStr) return false;
    return new Date(dateStr) < new Date();
}

export default function Orders({ orders }) {
    const paginationLinks = orders?.links || [];
    const hasPages = paginationLinks.length > 3;

    const statusBadge = (status, progress) => {
        const map = {
            pending: 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40',
            progress: 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-300 border border-blue-200 dark:border-blue-500/40',
            review: 'bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-300 border border-purple-200 dark:border-purple-500/40',
            completed: 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40',
        };
        const label = {
            pending: 'Pending',
            progress: `Progress (${progress}%)`,
            review: 'Review',
            completed: 'Selesai',
        };
        return (
            <span className={`px-2.5 py-1 rounded-full text-xs font-medium ${map[status] || ''}`}>
                {label[status] || status}
            </span>
        );
    };

    return (
        <DashboardLayout title="Daftar Pesanan Masuk">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-clipboard-list text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Daftar Pesanan Masuk</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola dan pantau semua pesanan joki dari klien.</p>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm text-left">
                            <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <tr>
                                    <th className="px-6 py-4">ID Pesanan</th>
                                    <th className="px-6 py-4">Klien</th>
                                    <th className="px-6 py-4">Nama Proyek</th>
                                    <th className="px-6 py-4 text-center">Status</th>
                                    <th className="px-6 py-4 text-center">Deadline</th>
                                    <th className="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {orders?.data?.length > 0 ? orders.data.map((order, idx) => (
                                    <tr key={idx} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4 font-bold text-[#7c3aed] dark:text-[#a78bfa]">{order.order_number}</td>
                                        <td className="px-6 py-4 font-medium text-[#333] dark:text-white flex items-center gap-3">
                                            <div className="w-8 h-8 rounded-full bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] flex items-center justify-center font-bold text-xs uppercase">
                                                {(order.client?.name || 'U').substring(0, 1)}
                                            </div>
                                            {order.client?.name || 'Klien Terhapus'}
                                        </td>
                                        <td className="px-6 py-4">
                                            <p className="font-medium text-[#333] dark:text-white">{order.project_name}</p>
                                            <p className="text-xs text-[#999] dark:text-white/40 mt-0.5">{order.tech_stack}</p>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            {statusBadge(order.status, order.progress)}
                                        </td>
                                        <td className={`px-6 py-4 text-center ${isPast(order.deadline) ? 'text-red-600 dark:text-red-300 font-bold' : ''}`}>
                                            {formatDate(order.deadline)}
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <Link href={`/admin/joki/orders/${order.hashid}/edit`}
                                                className="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 hover:bg-[#7c3aed] hover:text-white transition-all duration-200 shadow-sm"
                                                title="Kelola Pesanan">
                                                <i className="fa-solid fa-gear"></i>
                                            </Link>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="6" className="px-6 py-10 text-center text-[#999] dark:text-white/40">Belum ada data pesanan masuk.</td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {hasPages && (
                        <div className="px-6 py-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-center gap-1">
                            {paginationLinks.map((link, i) => (
                                <button key={i} disabled={!link.url} onClick={() => link.url && router.get(link.url, {}, { preserveState: true, replace: true })} className={`px-3 py-1.5 text-[13px] font-medium rounded-lg transition ${link.active ? 'bg-[#7c3aed] text-white shadow-sm' : link.url ? 'text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5' : 'text-[#ccc] dark:text-white/20 cursor-not-allowed'}`} dangerouslySetInnerHTML={{ __html: link.label }} />
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
