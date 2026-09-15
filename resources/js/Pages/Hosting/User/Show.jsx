import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function Show() {
    const { project, envContent, wafContent, diskUsage, visitorsCount, projectEmails } = usePage().props;
    const [env, setEnv] = useState(envContent || '');
    const [waf, setWaf] = useState(wafContent || '');
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

    const statusBadge = (status) => {
        const styles = {
            active: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            building: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            inactive: 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
            suspended: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            error: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.inactive}`}>
                {status || '-'}
            </span>
        );
    };

    const handleSaveEnv = () => {
        Inertia.form({ content: env }).put(`/user/hosting/projects/${project.id}/env`);
    };

    const handleSaveWaf = () => {
        Inertia.form({ content: waf }).put(`/user/hosting/projects/${project.id}/waf`);
    };

    const handleRebuild = () => {
        router.post(`/user/hosting/projects/${project.id}/rebuild`);
    };

    const handleDelete = () => {
        router.delete(`/user/hosting/projects/${project.id}`);
    };

    const diskPercent = diskUsage?.percent || 0;
    const diskColor = diskPercent > 90 ? 'bg-red-500' : diskPercent > 70 ? 'bg-yellow-500' : 'bg-[#7c3aed]';

    return (
        <DashboardLayout title={project?.name || 'Project Detail'}>
            <div className="space-y-6">
                <div className="flex items-center gap-3">
                    <Link href="/user/hosting/projects" className="text-[13px] text-[#999] dark:text-white/40 hover:text-[#7c3aed] transition-colors">
                        <i className="fa-solid fa-arrow-left mr-1"></i>Projects
                    </Link>
                    <span className="text-[#e5e5e5] dark:text-white/20">/</span>
                    <span className="text-[13px] font-bold text-[#333] dark:text-white">{project?.name}</span>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-4">
                        <p className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-2">Status</p>
                        <div className="flex items-center gap-2">
                            {statusBadge(project?.status)}
                            <span className="text-[13px] text-[#666] dark:text-white/60">{project?.status || '-'}</span>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-4">
                        <p className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-2">Domain</p>
                        <p className="text-[13px] text-[#333] dark:text-white font-medium">{project?.domain || `${project?.name}.ryaze.my.id`}</p>
                    </div>

                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-4">
                        <p className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-2">Visitors</p>
                        <p className="text-[20px] font-bold text-[#333] dark:text-white">{visitorsCount?.toLocaleString() || '0'}</p>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-4">
                    <p className="text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider mb-2">Disk Usage</p>
                    <div className="flex items-center gap-3">
                        <div className="flex-1 h-2 bg-[#e5e5e5] dark:bg-white/10 overflow-hidden">
                            <div className={`h-full ${diskColor} transition-all`} style={{ width: `${diskPercent}%` }}></div>
                        </div>
                        <span className="text-[13px] font-medium text-[#333] dark:text-white">{diskUsage?.human || '0 MB'}</span>
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h3 className="text-[13px] font-bold text-[#333] dark:text-white">Environment Variables</h3>
                        <button onClick={handleSaveEnv} className="px-3 py-1.5 bg-[#7c3aed] text-white text-[12px] font-semibold hover:bg-[#6d28d9] transition-colors">
                            Save
                        </button>
                    </div>
                    <div className="p-4">
                        <textarea
                            value={env}
                            onChange={(e) => setEnv(e.target.value)}
                            rows={10}
                            placeholder="APP_NAME=MyApp&#10;APP_KEY=base64:..."
                            className="w-full px-3 py-2 text-[12px] font-mono border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors resize-none"
                        />
                    </div>
                </div>

                {projectEmails?.length > 0 && (
                    <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                        <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-[13px] font-bold text-[#333] dark:text-white">Email Accounts</h3>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left">
                                <thead>
                                    <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                        <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Email</th>
                                        <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                    {projectEmails.map((email, i) => (
                                        <tr key={i} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                            <td className="px-5 py-3 text-[13px] text-[#333] dark:text-white">{email.email || email}</td>
                                            <td className="px-5 py-3">{statusBadge(email.status || 'active')}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                        <h3 className="text-[13px] font-bold text-[#333] dark:text-white">WAF Rules</h3>
                        <button onClick={handleSaveWaf} className="px-3 py-1.5 bg-[#7c3aed] text-white text-[12px] font-semibold hover:bg-[#6d28d9] transition-colors">
                            Save
                        </button>
                    </div>
                    <div className="p-4">
                        <textarea
                            value={waf}
                            onChange={(e) => setWaf(e.target.value)}
                            rows={6}
                            placeholder="# WAF rules..."
                            className="w-full px-3 py-2 text-[12px] font-mono border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors resize-none"
                        />
                    </div>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-4">
                    <h3 className="text-[13px] font-bold text-[#333] dark:text-white mb-4">Actions</h3>
                    <div className="flex flex-wrap items-center gap-3">
                        <button onClick={handleRebuild} className="px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                            <i className="fa-solid fa-rotate mr-1.5"></i>Rebuild
                        </button>
                        <Link href={`/user/hosting/projects/${project?.id}/logs`} className="px-4 py-2 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[13px] font-medium text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">
                            <i className="fa-solid fa-file-lines mr-1.5"></i>Logs
                        </Link>
                        <button onClick={() => setShowDeleteConfirm(true)} className="px-4 py-2 border border-red-300 text-[13px] font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                            <i className="fa-solid fa-trash mr-1.5"></i>Delete
                        </button>
                    </div>
                </div>

                {showDeleteConfirm && (
                    <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] max-w-md w-full p-6">
                            <h3 className="text-[15px] font-bold text-[#333] dark:text-white mb-2">Delete Project</h3>
                            <p className="text-[13px] text-[#666] dark:text-white/60 mb-6">This action is irreversible. All data will be permanently deleted.</p>
                            <div className="flex items-center justify-end gap-3">
                                <button onClick={() => setShowDeleteConfirm(false)} className="px-4 py-2 text-[13px] font-medium text-[#666] dark:text-white/60 hover:text-[#7c3aed] transition-colors">
                                    Cancel
                                </button>
                                <button onClick={handleDelete} className="px-4 py-2 bg-red-500 text-white text-[13px] font-semibold hover:bg-red-600 transition-colors">
                                    Delete Permanently
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
