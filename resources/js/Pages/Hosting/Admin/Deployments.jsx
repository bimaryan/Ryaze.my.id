import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router } from '@inertiajs/react';

const statusConfig = {
    success: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
    failed: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300',
    error: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300',
    queued: 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300',
    running: 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300',
    building: 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300',
};

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const day = d.getDate();
    const month = months[d.getMonth()];
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${day} ${month} ${year}, ${hours}:${minutes}`;
}

function timeAgo(dateStr) {
    if (!dateStr) return '';
    const now = new Date();
    const d = new Date(dateStr);
    const diff = Math.floor((now - d) / 1000);
    if (diff < 60) return 'Baru saja';
    if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
    return `${Math.floor(diff / 86400)} hari lalu`;
}

export default function Deployments({ deployments }) {
    const paginationLinks = deployments?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Riwayat Deployment">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-rocket text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Riwayat Deployment</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Pantau status build dan log dari seluruh project klien.</p>
                    </div>
                </div>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Project</th>
                                <th className="px-6 py-4">Klien & Info Commit</th>
                                <th className="px-6 py-4">Waktu</th>
                                <th className="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {deployments?.data?.length > 0 ? deployments.data.map((deploy) => (
                                <tr key={deploy.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-6 py-4">
                                        <p className="font-semibold text-[#333] dark:text-white">
                                            {deploy.project?.project_name ?? 'Project Dihapus'}
                                        </p>
                                        {deploy.project && (
                                            <a href={`https://${deploy.project.ryaze_domain}`} target="_blank" rel="noopener noreferrer"
                                                className="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-mono">
                                                {deploy.project.ryaze_domain}
                                            </a>
                                        )}
                                    </td>
                                    <td className="px-6 py-4">
                                        <p className="font-medium text-[#666] dark:text-white/60">{deploy.project?.client?.name ?? '—'}</p>
                                        <p className="text-xs text-[#999] dark:text-white/40 mt-0.5 truncate max-w-[250px]"
                                            title={deploy.commit_message}>
                                            <i className="fa-solid fa-code-commit mr-1"></i>
                                            {deploy.commit_message ? (deploy.commit_message.length > 45 ? deploy.commit_message.substring(0, 45) + '...' : deploy.commit_message) : 'System / Manual Deploy'}
                                        </p>
                                    </td>
                                    <td className="px-6 py-4">
                                        <p className="text-sm text-[#666] dark:text-white/60">{formatDate(deploy.created_at)}</p>
                                        <p className="text-xs text-[#999] dark:text-white/40 mt-0.5">{timeAgo(deploy.created_at)}</p>
                                    </td>
                                    <td className="px-6 py-4 text-center">
                                        <span className={`text-xs font-semibold px-2.5 py-1 rounded-full ${statusConfig[deploy.status] || 'bg-gray-100 text-gray-600'}`}>
                                            {deploy.status?.charAt(0).toUpperCase() + deploy.status?.slice(1)}
                                        </span>
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="4" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <i className="fa-solid fa-rocket text-3xl mb-3 text-slate-300 dark:text-slate-400 block"></i>
                                        <p className="text-sm">Belum ada riwayat deployment.</p>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {hasPages && (
                    <div className="px-6 py-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-center gap-1">
                        {paginationLinks.map((link, i) => (
                            <button
                                key={i}
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url, {}, { preserveState: true, replace: true })}
                                className={`px-3 py-1.5 text-[13px] font-medium rounded-lg transition ${
                                    link.active
                                        ? 'bg-[#7c3aed] text-white shadow-sm'
                                        : link.url
                                            ? 'text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5'
                                            : 'text-[#ccc] dark:text-white/20 cursor-not-allowed'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
