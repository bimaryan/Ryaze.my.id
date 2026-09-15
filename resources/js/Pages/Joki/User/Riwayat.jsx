import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Riwayat() {
    const { historyOrders } = usePage().props;

    const statusBadge = (status) => {
        const styles = {
            completed: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
            delivered: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300'}`}>
                {(status || '-').replace('_', ' ')}
            </span>
        );
    };

    return (
        <DashboardLayout title="Riwayat Pesanan">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Riwayat Pesanan</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Order Number</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Layanan</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {historyOrders?.data?.length > 0 ? historyOrders.data.map((order) => (
                                    <tr
                                        key={order.id}
                                        onClick={() => router.get(`/user/joki/orders/${order.id}`)}
                                        className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors cursor-pointer"
                                    >
                                        <td className="px-5 py-3 text-[13px] font-mono text-[#666] dark:text-white/60">{order.order_number || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{order.service?.name || '-'}</td>
                                        <td className="px-5 py-3">{statusBadge(order.status)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(order.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="4" className="px-5 py-12 text-center">
                                            <i className="fa-solid fa-history text-3xl text-[#e5e5e5] dark:text-white/10 mb-3 block"></i>
                                            <p className="text-[13px] text-[#999] dark:text-white/40">Belum ada riwayat pesanan</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {historyOrders?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {historyOrders.from}-{historyOrders.to} dari {historyOrders.total} pesanan
                            </p>
                            <div className="flex items-center gap-1">
                                {historyOrders.prev_page_url && (
                                    <Link href={historyOrders.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(historyOrders.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${historyOrders.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${historyOrders.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {historyOrders.next_page_url && (
                                    <Link href={historyOrders.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
