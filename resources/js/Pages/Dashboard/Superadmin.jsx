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

function formatRp(n) {
    if (!n) return 'Rp 0';
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

export default function Superadmin({
    totalUsers, activeJokiOrders, totalJokiOrders, activeHosting, totalHosting,
    jokiRevenueMonth, jokiRevenueMonthCount, jokiRevenueTotal,
    hostingRevenueMonth, hostingRevenueMonthCount, hostingRevenueTotal,
    totalRevenueMonth, totalRevenueTotal, totalDatabases, totalStorageMB,
    recentUsers, recentJokiOrders, recentHostingProjects,
    chartUserRoles, chartUserRegistrations, chartRevenue,
}) {
    return (
        <DashboardLayout title="Superadmin Dashboard">
            <div className="space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard icon="fa-solid fa-users" label="Total Pengguna" value={totalUsers} color="bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]" />
                    <StatCard icon="fa-solid fa-code-branch" label="Pesanan Joki" value={`${activeJokiOrders} / ${totalJokiOrders}`} color="bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400" />
                    <StatCard icon="fa-solid fa-server" label="Project Hosting" value={`${activeHosting} / ${totalHosting}`} color="bg-green-50 text-green-600 dark:bg-green-500/20 dark:text-green-400" />
                    <StatCard icon="fa-solid fa-chart-line" label="Pendapatan Bulan Ini" value={formatRp(totalRevenueMonth)} color="bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400" />
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <StatCard icon="fa-solid fa-wallet" label="Revenue Joki (Bulan)" value={formatRp(jokiRevenueMonth)} color="bg-purple-50 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400" />
                    <StatCard icon="fa-solid fa-cloud" label="Revenue Hosting (Bulan)" value={formatRp(hostingRevenueMonth)} color="bg-cyan-50 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-400" />
                    <StatCard icon="fa-solid fa-database" label="Total Database" value={totalDatabases} color="bg-rose-50 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400" />
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Pengguna Terbaru</h3>
                        </div>
                        <div className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {recentUsers?.map((u) => (
                                <div key={u.id} className="flex items-center justify-between px-5 py-3">
                                    <div>
                                        <p className="text-[13px] font-semibold text-[#333] dark:text-white">{u.name}</p>
                                        <p className="text-[11px] text-[#999] dark:text-white/40">{u.email}</p>
                                    </div>
                                    <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]">{u.role?.replace('_', ' ')}</span>
                                </div>
                            ))}
                            {!recentUsers?.length && <p className="px-5 py-6 text-center text-sm text-[#999] dark:text-white/40">Belum ada data</p>}
                        </div>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Pesanan Joki Terbaru</h3>
                        </div>
                        <div className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {recentJokiOrders?.map((o) => (
                                <div key={o.id} className="flex items-center justify-between px-5 py-3">
                                    <div>
                                        <p className="text-[13px] font-semibold text-[#333] dark:text-white">{o.project_name}</p>
                                        <p className="text-[11px] text-[#999] dark:text-white/40">{o.client_name} &middot; {o.order_number}</p>
                                    </div>
                                    <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${
                                        o.status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300' :
                                        o.status === 'progress' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' :
                                        'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300'
                                    }`}>{o.status}</span>
                                </div>
                            ))}
                            {!recentJokiOrders?.length && <p className="px-5 py-6 text-center text-sm text-[#999] dark:text-white/40">Belum ada data</p>}
                        </div>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] lg:col-span-2">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white">Project Hosting Terbaru</h3>
                        </div>
                        <div className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {recentHostingProjects?.map((p) => (
                                <div key={p.id} className="flex items-center justify-between px-5 py-3">
                                    <div>
                                        <p className="text-[13px] font-semibold text-[#333] dark:text-white">{p.project_name}</p>
                                        <p className="text-[11px] text-[#999] dark:text-white/40">{p.client_name} &middot; {p.ryaze_domain}</p>
                                    </div>
                                    <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${
                                        p.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300' :
                                        p.status === 'building' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' :
                                        'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300'
                                    }`}>{p.status}</span>
                                </div>
                            ))}
                            {!recentHostingProjects?.length && <p className="px-5 py-6 text-center text-sm text-[#999] dark:text-white/40">Belum ada data</p>}
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
