import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, Link, usePage } from '@inertiajs/react';

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ', ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
}

function statusBadge(status) {
    if (status === 'open') return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40">Terbuka</span>;
    if (status === 'answered') return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40">Dijawab</span>;
    return <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-500/40">Tertutup</span>;
}

function departmentBadge(dept) {
    const colors = {
        Hosting: 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-300 border-blue-200 dark:border-blue-500/40',
        Billing: 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-300 border-amber-200 dark:border-amber-500/40',
        Teknis: 'bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-300 border-purple-200 dark:border-purple-500/40',
        Joki: 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/40',
    };
    const cls = colors[dept] || 'bg-slate-50 dark:bg-slate-500/10 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-500/40';
    return <span className={`px-2.5 py-1 rounded-full text-xs font-medium border ${cls}`}>{dept || '-'}</span>;
}

export default function Index({ tickets }) {
    const paginationLinks = tickets?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Tiket Saya">
            <div className="mb-1">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-life-ring text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Tiket Saya</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Lihat dan kelola tiket support Anda.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href="/user/hosting/tickets/create" className="inline-flex items-center gap-2 px-5 py-2.5 bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
                            <i className="fa-solid fa-plus text-xs"></i> Buat Tiket Baru
                        </Link>
                    </div>
                </div>
            </div>

            <div className="mt-6 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Subjek</th>
                                <th className="px-6 py-4">Departemen</th>
                                <th className="px-6 py-4 text-center">Status</th>
                                <th className="px-6 py-4">Update Terakhir</th>
                                <th className="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {tickets?.data?.length > 0 ? tickets.data.map((ticket) => (
                                <tr key={ticket.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-6 py-4">
                                        <div>
                                            <p className="font-medium text-[#333] dark:text-white truncate max-w-[250px]">{ticket.subject}</p>
                                            <p className="text-xs text-[#999] dark:text-white/40 font-mono">#{ticket.hashid}</p>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">{departmentBadge(ticket.department)}</td>
                                    <td className="px-6 py-4 text-center">{statusBadge(ticket.status)}</td>
                                    <td className="px-6 py-4 text-xs text-[#999] dark:text-white/40 whitespace-nowrap">{formatDate(ticket.updated_at)}</td>
                                    <td className="px-6 py-4 text-center">
                                        <Link
                                            href={`/user/hosting/tickets/${ticket.hashid}`}
                                            className="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 hover:bg-[#7c3aed] hover:text-white transition-all duration-200 shadow-sm"
                                            title="Lihat"
                                        >
                                            <i className="fa-regular fa-eye"></i>
                                        </Link>
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="5" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <i className="fa-solid fa-ticket text-3xl mb-3 text-slate-300 dark:text-slate-400 block"></i>
                                        <p className="text-sm">Belum ada tiket. Klik "Buat Tiket Baru" untuk memulai.</p>
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
