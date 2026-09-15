import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Vouchers() {
    const { vouchers } = usePage().props;

    const statusBadge = (status) => {
        const styles = {
            active: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            inactive: 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
            expired: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.inactive}`}>
                {status || '-'}
            </span>
        );
    };

    return (
        <DashboardLayout title="Vouchers">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Semua Voucher</h2>
                        <Link
                            href="/admin/hosting/vouchers/create"
                            className="px-4 py-1.5 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors"
                        >
                            <i className="fa-solid fa-plus mr-1"></i> Buat Voucher
                        </Link>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Code</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Discount</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Uses</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Max Uses</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Expiry</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {vouchers?.data?.length > 0 ? vouchers.data.map((v) => (
                                    <tr key={v.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3">
                                            <span className="text-[13px] font-bold font-mono text-[#7c3aed]">{v.code}</span>
                                        </td>
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">
                                            {v.discount_type === 'percent' ? `${v.discount_value}%` : `Rp ${Number(v.discount_value).toLocaleString('id-ID')}`}
                                        </td>
                                        <td className="px-5 py-3">{statusBadge(v.status)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{v.used_count ?? 0}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{v.max_uses ?? '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {v.expiry_date
                                                ? new Date(v.expiry_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                                                : '-'}
                                        </td>
                                        <td className="px-5 py-3">
                                            <Link
                                                href={`/admin/hosting/vouchers/${v.hashid || v.id}/edit`}
                                                className="text-[13px] text-[#7c3aed] hover:underline font-medium"
                                            >
                                                Edit
                                            </Link>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="7" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada voucher
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {vouchers?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {vouchers.from}-{vouchers.to} dari {vouchers.total} voucher
                            </p>
                            <div className="flex items-center gap-1">
                                {vouchers.prev_page_url && (
                                    <Link href={vouchers.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(vouchers.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${vouchers.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${vouchers.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {vouchers.next_page_url && (
                                    <Link href={vouchers.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
