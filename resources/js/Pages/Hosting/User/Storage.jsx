import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Storage() {
    const { items, total_used, total_human, limit_bytes, limit_human, percent } = usePage().props;

    const pct = percent || 0;
    const barColor = pct > 90 ? 'bg-red-500' : pct > 70 ? 'bg-yellow-500' : 'bg-[#7c3aed]';

    return (
        <DashboardLayout title="Storage">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                    <h2 className="text-[13px] font-bold text-[#333] dark:text-white mb-4">Storage Overview</h2>
                    <div className="flex items-center gap-4 mb-3">
                        <div className="flex-1 h-3 bg-[#e5e5e5] dark:bg-white/10 overflow-hidden">
                            <div className={`h-full ${barColor} transition-all`} style={{ width: `${pct}%` }}></div>
                        </div>
                        <span className="text-[14px] font-bold text-[#333] dark:text-white">{pct}%</span>
                    </div>
                    <div className="flex items-center justify-between text-[12px] text-[#999] dark:text-white/40">
                        <span>Used: {total_human || '0 MB'}</span>
                        <span>Limit: {limit_human || 'Unlimited'}</span>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Project Storage</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Project</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Usage</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Percent</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {items?.data?.length > 0 ? items.data.map((item) => {
                                    const itemPct = limit_bytes > 0 ? ((item.bytes || 0) / limit_bytes * 100) : 0;
                                    return (
                                        <tr key={item.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                            <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{item.name || item.project?.name}</td>
                                            <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{item.human || '-'}</td>
                                            <td className="px-5 py-3">
                                                <div className="flex items-center gap-2">
                                                    <div className="w-20 h-1.5 bg-[#e5e5e5] dark:bg-white/10 overflow-hidden">
                                                        <div className="h-full bg-[#7c3aed]" style={{ width: `${Math.min(itemPct, 100)}%` }}></div>
                                                    </div>
                                                    <span className="text-[11px] text-[#999] dark:text-white/40">{itemPct.toFixed(1)}%</span>
                                                </div>
                                            </td>
                                            <td className="px-5 py-3 text-right">
                                                <Link href={`/user/hosting/storage/${item.project?.id || item.id}`} className="text-[12px] text-[#7c3aed] hover:text-[#6d28d9] font-medium transition-colors">
                                                    Details
                                                </Link>
                                            </td>
                                        </tr>
                                    );
                                }) : (
                                    <tr>
                                        <td colSpan="4" className="px-5 py-12 text-center">
                                            <i className="fa-solid fa-hard-drive text-3xl text-[#e5e5e5] dark:text-white/10 mb-3 block"></i>
                                            <p className="text-[13px] text-[#999] dark:text-white/40">No storage data</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {items?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Showing {items.from}-{items.to} of {items.total}
                            </p>
                            <div className="flex items-center gap-1">
                                {items.prev_page_url && (
                                    <Link href={items.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(items.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${items.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${items.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {items.next_page_url && (
                                    <Link href={items.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
