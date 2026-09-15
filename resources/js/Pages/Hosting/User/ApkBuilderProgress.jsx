import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function ApkBuilderProgress() {
    const { build } = usePage().props;

    const statusConfig = {
        pending: { color: 'text-yellow-500', bg: 'bg-yellow-100 dark:bg-yellow-500/20', icon: 'fa-solid fa-clock', label: 'Pending' },
        building: { color: 'text-blue-500', bg: 'bg-blue-100 dark:bg-blue-500/20', icon: 'fa-solid fa-spinner fa-spin', label: 'Building' },
        completed: { color: 'text-green-500', bg: 'bg-green-100 dark:bg-green-500/20', icon: 'fa-solid fa-check-circle', label: 'Completed' },
        failed: { color: 'text-red-500', bg: 'bg-red-100 dark:bg-red-500/20', icon: 'fa-solid fa-times-circle', label: 'Failed' },
    };

    const current = statusConfig[build?.status] || statusConfig.pending;

    return (
        <DashboardLayout title="Build Progress">
            <div className="max-w-2xl mx-auto space-y-6">
                <div className="flex items-center gap-3">
                    <Link href="/user/hosting/apk" className="text-[13px] text-[#999] dark:text-white/40 hover:text-[#7c3aed] transition-colors">
                        <i className="fa-solid fa-arrow-left mr-1"></i>Back
                    </Link>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] p-6">
                    <div className="flex items-center gap-4 mb-6">
                        <div className={`w-12 h-12 ${current.bg} flex items-center justify-center`}>
                            <i className={`${current.icon} text-xl ${current.color}`}></i>
                        </div>
                        <div>
                            <h2 className="text-[16px] font-bold text-[#333] dark:text-white">{build?.app_name || build?.name || 'APK Build'}</h2>
                            <p className="text-[13px] text-[#666] dark:text-white/60">{current.label}</p>
                        </div>
                    </div>

                    <div className="space-y-4 mb-6">
                        <div className="flex items-center justify-between py-2 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <span className="text-[12px] text-[#999] dark:text-white/40">Website URL</span>
                            <span className="text-[13px] text-[#333] dark:text-white font-medium">{build?.website_url || '-'}</span>
                        </div>
                        <div className="flex items-center justify-between py-2 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <span className="text-[12px] text-[#999] dark:text-white/40">Status</span>
                            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${current.bg} ${current.color}`}>{build?.status || '-'}</span>
                        </div>
                        <div className="flex items-center justify-between py-2 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <span className="text-[12px] text-[#999] dark:text-white/40">Created</span>
                            <span className="text-[13px] text-[#333] dark:text-white">
                                {build?.created_at ? new Date(build.created_at).toLocaleString('id-ID') : '-'}
                            </span>
                        </div>
                        {build?.completed_at && (
                            <div className="flex items-center justify-between py-2 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <span className="text-[12px] text-[#999] dark:text-white/40">Completed</span>
                                <span className="text-[13px] text-[#333] dark:text-white">
                                    {new Date(build.completed_at).toLocaleString('id-ID')}
                                </span>
                            </div>
                        )}
                    </div>

                    {build?.logs && (
                        <div className="mb-6">
                            <h3 className="text-[13px] font-bold text-[#333] dark:text-white mb-2">Build Logs</h3>
                            <div className="bg-[#1a1a2e] dark:bg-black/30 p-4 max-h-64 overflow-y-auto">
                                <pre className="text-[11px] font-mono text-green-400 whitespace-pre-wrap">{build.logs}</pre>
                            </div>
                        </div>
                    )}

                    {build?.status === 'building' && (
                        <div className="mb-6">
                            <div className="flex items-center gap-3 mb-2">
                                <div className="flex-1 h-2 bg-[#e5e5e5] dark:bg-white/10 overflow-hidden">
                                    <div className="h-full bg-[#7c3aed] animate-pulse" style={{ width: '60%' }}></div>
                                </div>
                                <span className="text-[12px] text-[#999] dark:text-white/40">Building...</span>
                            </div>
                            <p className="text-[12px] text-[#666] dark:text-white/50">This may take a few minutes. Do not close this page.</p>
                        </div>
                    )}

                    <div className="flex items-center gap-3">
                        {build?.status === 'completed' && build?.download_url && (
                            <a
                                href={build.download_url}
                                className="px-5 py-2 bg-green-500 text-white text-[13px] font-semibold hover:bg-green-600 transition-colors"
                            >
                                <i className="fa-solid fa-download mr-1.5"></i>Download APK
                            </a>
                        )}
                        {build?.status === 'failed' && (
                            <Link
                                href="/user/hosting/apk/create"
                                className="px-5 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors"
                            >
                                <i className="fa-solid fa-rotate mr-1.5"></i>Try Again
                            </Link>
                        )}
                        <Link href="/user/hosting/apk" className="px-5 py-2 text-[13px] font-medium text-[#666] dark:text-white/60 hover:text-[#7c3aed] transition-colors">
                            Back to Builds
                        </Link>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
