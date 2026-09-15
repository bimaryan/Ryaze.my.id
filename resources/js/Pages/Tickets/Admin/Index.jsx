import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, Link, usePage } from '@inertiajs/react';

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ', ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
}

function priorityBadge(priority) {
    if (priority === 'high') return <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-500/40"><i className="fa-solid fa-fire text-[10px]"></i> Tinggi</span>;
    if (priority === 'medium') return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40">Sedang</span>;
    return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-500/40">Rendah</span>;
}

function statusBadge(status) {
    if (status === 'open') return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40">Terbuka</span>;
    if (status === 'answered') return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40">Dijawab</span>;
    return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-500/40">Tertutup</span>;
}

function timeAgo(dateStr) {
    if (!dateStr) return '-';
    const now = new Date();
    const d = new Date(dateStr);
    const diff = Math.floor((now - d) / 1000);
    if (diff < 60) return 'Baru saja';
    if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
    if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
    if (diff < 2592000) return Math.floor(diff / 86400) + ' hari lalu';
    return formatDate(dateStr);
}

export default function Index({ tickets }) {
    const paginationLinks = tickets?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Tiket Support">
            <div className="mb-1">
                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-headset text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Tiket Support</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola semua tiket support dari klien Anda.</p>
                    </div>
                </div>
            </div>

            <div className="mt-6 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Klien</th>
                                <th className="px-6 py-4">Subjek & Dept</th>
                                <th className="px-6 py-4 text-center">Prioritas</th>
                                <th className="px-6 py-4 text-center">Status</th>
                                <th className="px-6 py-4">Waktu</th>
                                <th className="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {tickets?.data?.length > 0 ? tickets.data.map((ticket) => (
                                <tr key={ticket.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-full bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] flex items-center justify-center font-bold text-sm uppercase shrink-0">
                                                {ticket.user?.name?.charAt(0)?.toUpperCase() || '?'}
                                            </div>
                                            <div className="min-w-0">
                                                <p className="font-medium text-[#333] dark:text-white truncate">{ticket.user?.name || '-'}</p>
                                                <p className="text-xs text-[#999] dark:text-white/40 truncate">{ticket.user?.email || '-'}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <p className="font-medium text-[#333] dark:text-white truncate max-w-[200px]">{ticket.subject}</p>
                                        <p className="text-xs text-[#999] dark:text-white/40 font-mono">#{ticket.hashid}</p>
                                    </td>
                                    <td className="px-6 py-4 text-center">{priorityBadge(ticket.priority)}</td>
                                    <td className="px-6 py-4 text-center">{statusBadge(ticket.status)}</td>
                                    <td className="px-6 py-4 text-xs text-[#999] dark:text-white/40 whitespace-nowrap">{timeAgo(ticket.updated_at)}</td>
                                    <td className="px-6 py-4 text-center">
                                        <Link
                                            href={`/admin/hosting/tickets/${ticket.hashid}`}
                                            className="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 hover:bg-[#7c3aed] hover:text-white transition-all duration-200 shadow-sm"
                                            title="Balas"
                                        >
                                            <i className="fa-solid fa-reply"></i>
                                        </Link>
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="6" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <i className="fa-solid fa-ticket text-3xl mb-3 text-slate-300 dark:text-slate-400 block"></i>
                                        <p className="text-sm">Belum ada tiket support.</p>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {hasPages && (
                    <div className="px-6 py-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-center gap-1">
                        {paginationLinks.map((link, i) => (
                            <button
                                key={i}
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url, {}, { preserveState: true, replace: true })}
                                className={`px-3 py-1.5 text-[13px] font-medium rounded-lg transition ${
                                    link.active
                                        ? 'bg-[#7c3aed] text-white shadow-sm'
                                        : link.url
                                            ? 'text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5'
                                            : 'text-[#ccc] dark:text-white/20 cursor-not-allowed'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
