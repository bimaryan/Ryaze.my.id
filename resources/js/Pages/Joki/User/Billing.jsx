import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Billing() {
    const { payments } = usePage().props;

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

    const statusBadge = (status) => {
        const styles = {
            paid: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            failed: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
            unpaid: 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.unpaid}`}>
                {status || '-'}
            </span>
        );
    };

    return (
        <DashboardLayout title="Tagihan">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Riwayat Tagihan</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Invoice</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Order</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Amount</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Method</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {payments?.data?.length > 0 ? payments.data.map((p) => (
                                    <tr key={p.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] font-mono text-[#666] dark:text-white/60">{p.invoice_number || p.id}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">
                                            {p.order?.order_number || '-'}
                                        </td>
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{formatCurrency(p.amount)}</td>
                                        <td className="px-5 py-3">{statusBadge(p.status)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{p.method || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(p.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="6" className="px-5 py-12 text-center">
                                            <i className="fa-solid fa-file-invoice-dollar text-3xl text-[#e5e5e5] dark:text-white/10 mb-3 block"></i>
                                            <p className="text-[13px] text-[#999] dark:text-white/40">Belum ada tagihan</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {payments?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {payments.from}-{payments.to} dari {payments.total} tagihan
                            </p>
                            <div className="flex items-center gap-1">
                                {payments.prev_page_url && (
                                    <Link href={payments.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(payments.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${payments.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${payments.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {payments.next_page_url && (
                                    <Link href={payments.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
