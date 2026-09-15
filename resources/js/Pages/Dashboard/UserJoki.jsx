import DashboardLayout from '../../Layouts/DashboardLayout';

export default function UserJoki({ activeOrders, stats }) {
    return (
        <DashboardLayout title="Dashboard Joki">
            <div className="space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Dikerjakan</p>
                        <p className="text-2xl font-black text-blue-500 mt-1">{stats?.progress || 0}</p>
                    </div>
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Selesai</p>
                        <p className="text-2xl font-black text-green-500 mt-1">{stats?.completed || 0}</p>
                    </div>
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Menunggu</p>
                        <p className="text-2xl font-black text-amber-500 mt-1">{stats?.pending || 0}</p>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white">Pesanan Aktif</h3>
                        <a href={route('user_joki.create')} className="text-[13px] font-medium text-[#7c3aed] hover:text-[#6d28d9] transition-colors">+ Buat Pesanan</a>
                    </div>
                    <div className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                        {activeOrders?.length > 0 ? activeOrders.map((o) => (
                            <a key={o.id} href={route('user_joki.detail', o.hashid)} className="flex items-center justify-between px-5 py-3 hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                <div>
                                    <p className="text-[13px] font-semibold text-[#333] dark:text-white">{o.project_name}</p>
                                    <p className="text-[11px] text-[#999] dark:text-white/40">{o.service_name} &middot; {o.order_number}</p>
                                </div>
                                <div className="flex items-center gap-3">
                                    {o.deadline && <span className="text-[11px] text-[#999] dark:text-white/40">{o.deadline}</span>}
                                    <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${
                                        o.status === 'progress' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' :
                                        o.status === 'review' ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300' :
                                        'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300'
                                    }`}>{o.status}</span>
                                </div>
                            </a>
                        )) : (
                            <div className="px-5 py-8 text-center">
                                <p className="text-sm text-[#999] dark:text-white/40">Belum ada pesanan aktif</p>
                                <a href={route('user_joki.create')} className="inline-block mt-3 px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">Buat Pesanan</a>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
