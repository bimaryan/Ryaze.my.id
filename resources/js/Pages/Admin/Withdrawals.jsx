import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Withdrawals() {
    const { withdrawals } = usePage().props;

    const statusBadge = (status) => {
        const map = {
            pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
            approved: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            rejected: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
            completed: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${map[status] || map.pending}`}>
                {status}
            </span>
        );
    };

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

    return (
        <DashboardLayout title="Penarikan">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Semua Penarikan</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">User</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Jumlah</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Metode</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {withdrawals?.data?.length > 0 ? withdrawals.data.map((w) => (
                                    <tr key={w.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{w.user?.name || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{formatCurrency(w.amount)}</td>
                                        <td className="px-5 py-3">{statusBadge(w.status)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{w.method || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(w.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada penarikan
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {withdrawals?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {withdrawals.from}-{withdrawals.to} dari {withdrawals.total} penarikan
                            </p>
                            <div className="flex items-center gap-1">
                                {withdrawals.prev_page_url && (
                                    <Link href={withdrawals.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(withdrawals.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${withdrawals.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${withdrawals.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {withdrawals.next_page_url && (
                                    <Link href={withdrawals.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
