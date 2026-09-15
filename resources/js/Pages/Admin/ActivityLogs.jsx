import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function ActivityLogs() {
    const { logs } = usePage().props;

    return (
        <DashboardLayout title="Log Aktivitas">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Semua Log Aktivitas</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">User</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Aksi</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Model</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {logs?.data?.length > 0 ? logs.data.map((log) => (
                                    <tr key={log.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{log.user?.name || '-'}</td>
                                        <td className="px-5 py-3">
                                            <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]">
                                                {log.action || '-'}
                                            </span>
                                        </td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{log.model_type || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(log.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="4" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada log aktivitas
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {logs?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {logs.from}-{logs.to} dari {logs.total} log
                            </p>
                            <div className="flex items-center gap-1">
                                {logs.prev_page_url && (
                                    <Link href={logs.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(logs.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${logs.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${logs.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {logs.next_page_url && (
                                    <Link href={logs.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
