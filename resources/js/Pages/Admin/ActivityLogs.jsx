import DashboardLayout from '../../Layouts/DashboardLayout';
import { router } from '@inertiajs/react';

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()} ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}:${String(d.getSeconds()).padStart(2,'0')}`;
}

export default function ActivityLogs({ logs }) {
    const paginationLinks = logs?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Log Aktivitas Sistem">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-list-check text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Log Aktivitas Sistem</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Pantau semua aktivitas admin dan sistem.</p>
                    </div>
                </div>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-4 py-3">Waktu</th>
                                <th className="px-4 py-3">User / Aktor</th>
                                <th className="px-4 py-3">Aksi</th>
                                <th className="px-4 py-3">Deskripsi</th>
                                <th className="px-4 py-3">IP Address</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {logs?.data?.length > 0 ? logs.data.map((log) => (
                                <tr key={log.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-4 py-3 font-medium text-[#333] dark:text-white whitespace-nowrap">
                                        {formatDate(log.created_at)}
                                    </td>
                                    <td className="px-4 py-3">
                                        {log.user ? (
                                            <div className="flex items-center gap-2">
                                                <span className="font-medium text-[#666] dark:text-white/60">{log.user.name}</span>
                                                <span className="text-[10px] bg-[#fafafa] dark:bg-white/5 text-[#666] dark:text-white/60 px-1.5 py-0.5 rounded border border-[#e5e5e5] dark:border-[#1a1a2e]">{log.user.role}</span>
                                            </div>
                                        ) : (
                                            <span className="text-[#999] dark:text-white/40 italic">Sistem (Otomatis)</span>
                                        )}
                                    </td>
                                    <td className="px-4 py-3">
                                        <span className="bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-[#a78bfa] px-2 py-1 rounded border border-[#ede9fe] dark:border-[#7c3aed]/30 text-xs font-semibold">
                                            {log.action}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3 text-[#666] dark:text-white/60">
                                        {log.description || '-'}
                                    </td>
                                    <td className="px-4 py-3 font-mono text-xs text-[#999] dark:text-white/40">
                                        {log.ip_address || '-'}
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="5" className="px-4 py-8 text-center text-[#999] dark:text-white/40">
                                        <i className="fa-solid fa-inbox text-3xl mb-2 text-slate-300 dark:text-slate-400 block"></i>
                                        Belum ada log aktivitas.
                                    </td>
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
        </DashboardLayout>
    );
}
