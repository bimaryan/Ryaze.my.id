import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Pending() {
    const { projects } = usePage().props;

    const statusBadge = (status) => {
        const styles = {
            unpaid: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            error: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
            suspended: 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.unpaid}`}>
                {status || '-'}
            </span>
        );
    };

    return (
        <DashboardLayout title="Pending Projects">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Projects Pending</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Client</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Project Name</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Domain</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Last Deployment</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {projects?.data?.length > 0 ? projects.data.map((project) => (
                                    <tr key={project.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] text-[#333] dark:text-white">{project.user?.name || '-'}</td>
                                        <td className="px-5 py-3">
                                            <span className="text-[13px] font-semibold text-[#333] dark:text-white">{project.name}</span>
                                        </td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{project.domain || '-'}</td>
                                        <td className="px-5 py-3">{statusBadge(project.status)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {project.last_deployment_at
                                                ? new Date(project.last_deployment_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                                                : '-'}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada project pending
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {projects?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {projects.from}-{projects.to} dari {projects.total} project
                            </p>
                            <div className="flex items-center gap-1">
                                {projects.prev_page_url && (
                                    <Link href={projects.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(projects.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${projects.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${projects.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {projects.next_page_url && (
                                    <Link href={projects.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
