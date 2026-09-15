import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function UserShow() {
    const { user, jokiOrders, hostingProjects } = usePage().props;

    const roleBadge = (role) => {
        const colors = {
            superadmin: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
            admin_joki: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            admin_hosting: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300',
            user: 'bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]',
        };
        const label = (role || 'user').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${colors[role] || colors.user}`}>
                {label}
            </span>
        );
    };

    const statusBadge = (status) => {
        const map = {
            completed: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            progress: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            review: 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
            active: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${map[status] || 'bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]'}`}>
                {status}
            </span>
        );
    };

    const initials = (user?.name || 'U').split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();

    return (
        <DashboardLayout title="Detail Pengguna">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-6">
                    <div className="flex items-center gap-4">
                        <div className="w-16 h-16 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] flex items-center justify-center text-xl font-black shrink-0">
                            {initials}
                        </div>
                        <div>
                            <h2 className="text-lg font-black text-[#333] dark:text-white">{user?.name}</h2>
                            <p className="text-[13px] text-[#999] dark:text-white/40">{user?.email}</p>
                            <div className="mt-1">{roleBadge(user?.role)}</div>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 flex items-center justify-center bg-blue-50 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                                <i className="fa-solid fa-code-branch text-lg"></i>
                            </div>
                            <div>
                                <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Pesanan Joki</p>
                                <p className="text-xl font-black text-[#333] dark:text-white">{user?.client_orders_count || 0}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-5">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 flex items-center justify-center bg-green-50 text-green-600 dark:bg-green-500/20 dark:text-green-400">
                                <i className="fa-solid fa-server text-lg"></i>
                            </div>
                            <div>
                                <p className="text-[11px] text-[#999] dark:text-white/40 font-medium uppercase tracking-wider">Project Hosting</p>
                                <p className="text-xl font-black text-[#333] dark:text-white">{user?.hosting_projects_count || 0}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white">Pesanan Joki</h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Proyek</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Nomor</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {jokiOrders?.length > 0 ? jokiOrders.map((o) => (
                                    <tr key={o.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{o.project_name}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{o.order_number}</td>
                                        <td className="px-5 py-3">{statusBadge(o.status)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(o.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="4" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada pesanan joki
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h3 className="text-sm font-bold text-[#333] dark:text-white">Project Hosting</h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Project</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Domain</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {hostingProjects?.length > 0 ? hostingProjects.map((p) => (
                                    <tr key={p.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{p.project_name}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{p.ryaze_domain}</td>
                                        <td className="px-5 py-3">{statusBadge(p.status)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(p.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="4" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada project hosting
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
