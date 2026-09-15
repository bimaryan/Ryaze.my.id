import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Index({ stats, projects, activeBilling, expiredBilling }) {
    return (
        <DashboardLayout title="Dashboard Hosting">
            <div className="space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[13px] text-[#999] dark:text-white/40 font-medium">Project Aktif</p>
                        <p className="text-2xl font-black text-[#7c3aed] mt-1">{stats?.active || 0}</p>
                    </div>
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[13px] text-[#999] dark:text-white/40 font-medium">Belum Dibayar</p>
                        <p className="text-2xl font-black text-[#f59e0b] mt-1">{stats?.unpaid || 0}</p>
                    </div>
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <p className="text-[13px] text-[#999] dark:text-white/40 font-medium">Tiket</p>
                        <p className="text-2xl font-black text-[#10b981] mt-1">{stats?.tickets || 0}</p>
                    </div>
                </div>

                {expiredBilling && (
                    <div className="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 p-5">
                        <div className="flex items-center gap-3">
                            <i className="fa-solid fa-triangle-exclamation text-red-500 text-lg"></i>
                            <div>
                                <p className="text-sm font-bold text-red-700 dark:text-red-300">Langganan Hosting Telah Berakhir!</p>
                                <p className="text-xs text-red-600 dark:text-red-400 mt-0.5">Perpanjang sekarang untuk mengaktifkan kembali project Anda.</p>
                            </div>
                        </div>
                    </div>
                )}

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Project Terbaru</h2>
                        <a href={route('user_hosting.projects')} className="text-[13px] font-medium text-[#7c3aed] hover:text-[#6d28d9] transition-colors">Lihat Semua</a>
                    </div>
                    <div className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                        {projects?.length > 0 ? projects.map((p) => (
                            <a key={p.id} href={route('user_hosting.show', { hashid: p.hashid })} className="flex items-center justify-between px-5 py-3 hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                <div className="flex items-center gap-3 min-w-0">
                                    <div className="w-2 h-2 shrink-0 rounded-full bg-green-500"></div>
                                    <div className="min-w-0">
                                        <p className="text-[13px] font-semibold text-[#333] dark:text-white truncate">{p.project_name}</p>
                                        <p className="text-[11px] text-[#999] dark:text-white/40 truncate">{p.ryaze_domain}</p>
                                    </div>
                                </div>
                                <span className={`text-[11px] font-bold uppercase px-2 py-0.5 shrink-0 ${
                                    p.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300' :
                                    p.status === 'building' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' :
                                    'bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]'
                                }`}>{p.status}</span>
                            </a>
                        )) : (
                            <div className="px-5 py-8 text-center">
                                <p className="text-sm text-[#999] dark:text-white/40">Belum ada project. Mulai deploy sekarang!</p>
                                <a href={route('user_hosting.create')} className="inline-block mt-3 px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">Deploy Baru</a>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
