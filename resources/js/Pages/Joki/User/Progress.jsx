import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Progress() {
    const { activeOrders } = usePage().props;

    const statusBadge = (status) => {
        const styles = {
            pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            in_progress: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
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
        <DashboardLayout title="Progres Pesanan">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Pesanan Aktif</h2>
                    </div>

                    {activeOrders?.length > 0 ? (
                        <div className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {activeOrders.map((order) => (
                                <div
                                    key={order.id}
                                    onClick={() => router.get(`/user/joki/orders/${order.id}`)}
                                    className="px-5 py-4 hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors cursor-pointer"
                                >
                                    <div className="flex items-start justify-between mb-2">
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center gap-2 mb-1">
                                                <span className="text-[12px] font-mono text-[#999] dark:text-white/40">{order.order_number}</span>
                                                {statusBadge(order.status)}
                                            </div>
                                            <h3 className="text-[14px] font-semibold text-[#333] dark:text-white truncate">{order.project_name}</h3>
                                            <p className="text-[12px] text-[#666] dark:text-white/60 mt-0.5">{order.service?.name}</p>
                                        </div>
                                        <span className="text-[12px] font-semibold text-[#333] dark:text-white shrink-0 ml-3">{formatCurrency(order.amount)}</span>
                                    </div>

                                    <div className="mt-3">
                                        <div className="flex items-center justify-between mb-1">
                                            <span className="text-[11px] text-[#999] dark:text-white/40">Progres</span>
                                            <span className="text-[11px] font-bold text-[#7c3aed]">{order.progress || 0}%</span>
                                        </div>
                                        <div className="w-full h-1.5 bg-[#e5e5e5] dark:bg-[#1a1a2e]">
                                            <div
                                                className="h-full bg-[#7c3aed] transition-all duration-300"
                                                style={{ width: `${order.progress || 0}%` }}
                                            ></div>
                                        </div>
                                    </div>

                                    {order.last_update && (
                                        <p className="text-[11px] text-[#999] dark:text-white/30 mt-2">
                                            <i className="fa-solid fa-clock mr-1"></i>Update terakhir: {new Date(order.last_update).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                        </p>
                                    )}

                                    {order.deadline && (
                                        <p className="text-[11px] text-[#999] dark:text-white/30 mt-0.5">
                                            <i className="fa-solid fa-calendar mr-1"></i>Deadline: {new Date(order.deadline).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                        </p>
                                    )}
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="px-5 py-12 text-center">
                            <i className="fa-solid fa-laptop-code text-3xl text-[#e5e5e5] dark:text-white/10 mb-3 block"></i>
                            <p className="text-[13px] text-[#999] dark:text-white/40">Tidak ada pesanan aktif</p>
                            <Link href="/user/joki/create" className="mt-3 inline-block px-4 py-2 bg-[#7c3aed] text-white text-[12px] font-semibold hover:bg-[#6d28d9] transition-colors">
                                <i className="fa-solid fa-plus mr-1"></i>Buat Pesanan
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
