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

export default function AdminHosting({ stats, chartNewProjects, chartProjectStatus, chartBillings }) {
    return (
        <DashboardLayout title="Admin Hosting Dashboard">
            <div className="space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard icon="fa-solid fa-server" label="Total Project" value={stats?.total_projects || 0} color="bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]" />
                    <StatCard icon="fa-solid fa-check-circle" label="Project Aktif" value={stats?.active_projects || 0} color="bg-green-50 text-green-600 dark:bg-green-500/20 dark:text-green-400" />
                    <StatCard icon="fa-solid fa-users" label="Total Klien" value={stats?.total_clients || 0} color="bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400" />
                    <StatCard icon="fa-solid fa-database" label="Total Database" value={stats?.total_databases || 0} color="bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400" />
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <StatCard icon="fa-solid fa-warning" label="Membutuhkan Tindakan" value={stats?.action_required || 0} color="bg-red-50 text-red-600 dark:bg-red-500/20 dark:text-red-400" />
                    <StatCard icon="fa-solid fa-hammer" label="Sedang Building" value={stats?.building_now || 0} color="bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400" />
                    <StatCard icon="fa-solid fa-file-invoice" label="Tagihan Pending" value={stats?.pending_billing || 0} color="bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400" />
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                    <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Project Baru (6 Bulan Terakhir)</h3>
                    <div className="flex items-end gap-2 h-40">
                        {chartNewProjects?.series?.map((val, i) => {
                            const max = Math.max(...(chartNewProjects.series || [1]));
                            const h = max > 0 ? (val / max) * 100 : 0;
                            return (
                                <div key={i} className="flex-1 flex flex-col items-center gap-1">
                                    <span className="text-[10px] font-bold text-[#333] dark:text-white">{val}</span>
                                    <div className="w-full bg-[#7c3aed] transition-all" style={{ height: `${Math.max(h, 4)}%` }}></div>
                                    <span className="text-[9px] text-[#999] dark:text-white/40 text-center leading-tight">{chartNewProjects.labels?.[i]?.split(' ')[0]}</span>
                                </div>
                            );
                        })}
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                    <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4">Status Project</h3>
                    <div className="flex flex-wrap gap-3">
                        {chartProjectStatus?.labels?.map((label, i) => (
                            <div key={i} className="flex items-center gap-2 px-3 py-2 bg-[#fafafa] dark:bg-white/[0.02] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <span className={`w-2 h-2 rounded-full ${
                                    label === 'active' ? 'bg-green-500' :
                                    label === 'building' ? 'bg-blue-500' :
                                    label === 'error' ? 'bg-red-500' :
                                    label === 'suspended' ? 'bg-amber-500' : 'bg-[#999]'
                                }`}></span>
                                <span className="text-[13px] font-medium text-[#666] dark:text-white/60 capitalize">{label}</span>
                                <span className="text-[13px] font-bold text-[#333] dark:text-white">{chartProjectStatus.series?.[i]}</span>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
