import DashboardLayout from '../../Layouts/DashboardLayout';

function StatCard({ icon, label, value, color }) {
    return (
        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
            <div className="flex items-center gap-3">
                <div className={`w-10 h-10 flex items-center justify-center ${color}`}>
                    <i className={`${icon} text-lg`}></i>
                </div>
                <div>
                    <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">{label}</p>
                    <p className="text-xl font-black text-[#333] dark:text-white">{value}</p>
                </div>
            </div>
        </div>
    );
}

export default function AdminJoki({ pendingOrders, progressOrders, reviewOrders, completedOrders, queueOrders, chartOrderStatus, chartNewOrders, chartCompletedOrders }) {
    return (
        <DashboardLayout title="Admin Joki Dashboard">
            <div className="space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard icon="fa-solid fa-clock" label="Menunggu" value={pendingOrders || 0} color="bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400" />
                    <StatCard icon="fa-solid fa-spinner" label="Dikerjakan" value={progressOrders || 0} color="bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400" />
                    <StatCard icon="fa-solid fa-eye" label="Review" value={reviewOrders || 0} color="bg-purple-50 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400" />
                    <StatCard icon="fa-solid fa-check-circle" label="Selesai Bulan Ini" value={completedOrders || 0} color="bg-green-50 text-green-600 dark:bg-green-500/20 dark:text-green-400" />
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white">Antrean Pesanan</h3>
                    </div>
                    <div className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                        {queueOrders?.map((o) => (
                            <div key={o.id} className="flex items-center justify-between px-5 py-3 hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                <div>
                                    <p className="text-[13px] font-semibold text-[#333] dark:text-white">{o.project_name}</p>
                                    <p className="text-[11px] text-[#999] dark:text-white/40">{o.client_name} &middot; {o.order_number}</p>
                                </div>
                                <div className="flex items-center gap-3">
                                    {o.deadline && <span className="text-[11px] text-[#999] dark:text-white/40">{o.deadline}</span>}
                                    <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${
                                        o.status === 'progress' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' :
                                        o.status === 'review' ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300' :
                                        'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300'
                                    }`}>{o.status}</span>
                                </div>
                            </div>
                        ))}
                        {!queueOrders?.length && <p className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">Tidak ada pesanan dalam antrean</p>}
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Status Pesanan</h3>
                        <div className="flex flex-wrap gap-3">
                            {chartOrderStatus?.labels?.map((label, i) => (
                                <div key={i} className="flex items-center gap-2 px-3 py-2 bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <span className={`w-2 h-2 rounded-full ${
                                        label === 'completed' ? 'bg-green-500' :
                                        label === 'progress' ? 'bg-blue-500' :
                                        label === 'review' ? 'bg-purple-500' :
                                        label === 'pending' ? 'bg-amber-500' : 'bg-[#999]'
                                    }`}></span>
                                    <span className="text-[13px] font-medium text-[#666] dark:text-white/60 capitalize">{label}</span>
                                    <span className="text-[13px] font-bold text-[#333] dark:text-white">{chartOrderStatus.series?.[i]}</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Pesanan Baru (6 Bulan)</h3>
                        <div className="flex items-end gap-2 h-32">
                            {chartNewOrders?.series?.map((val, i) => {
                                const max = Math.max(...(chartNewOrders.series || [1]));
                                const h = max > 0 ? (val / max) * 100 : 0;
                                return (
                                    <div key={i} className="flex-1 flex flex-col items-center gap-1">
                                        <span className="text-[10px] font-bold text-[#333] dark:text-white">{val}</span>
                                        <div className="w-full bg-[#7c3aed] transition-all" style={{ height: `${Math.max(h, 4)}%` }}></div>
                                        <span className="text-[9px] text-[#999] dark:text-white/40 text-center leading-tight">{chartNewOrders.labels?.[i]?.split(' ')[0]}</span>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
