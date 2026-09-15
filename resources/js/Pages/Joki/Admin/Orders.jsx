import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Orders() {
    const { orders } = usePage().props;

    const statusBadge = (status) => {
        const styles = {
            pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            in_progress: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            completed: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.pending}`}>
                {(status || '-').replace('_', ' ')}
            </span>
        );
    };

    const formatCurrency = (val) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0);
    };

    return (
        <DashboardLayout title="Pesanan Joki">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Semua Pesanan</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Order Number</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Client</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Service</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Project Name</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Deadline</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {orders?.data?.length > 0 ? orders.data.map((order) => (
                                    <tr
                                        key={order.id}
                                        onClick={() => router.get(`/admin/joki/orders/${order.id}/edit`)}
                                        className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors cursor-pointer"
                                    >
                                        <td className="px-5 py-3 text-[13px] font-mono text-[#666] dark:text-white/60">{order.order_number || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#333] dark:text-white">{order.user?.name || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{order.service?.name || '-'}</td>
                                        <td className="px-5 py-3">
                                            <span className="text-[13px] font-semibold text-[#333] dark:text-white">{order.project_name || '-'}</span>
                                        </td>
                                        <td className="px-5 py-3">{statusBadge(order.status)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {order.deadline ? new Date(order.deadline).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-'}
                                        </td>
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{formatCurrency(order.amount)}</td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="7" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada pesanan
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {orders?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {orders.from}-{orders.to} dari {orders.total} pesanan
                            </p>
                            <div className="flex items-center gap-1">
                                {orders.prev_page_url && (
                                    <Link href={orders.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(orders.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${orders.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${orders.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {orders.next_page_url && (
                                    <Link href={orders.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
