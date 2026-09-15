import { usePage, Link } from '@inertiajs/react';
import DashboardLayout from '../../../Layouts/DashboardLayout';

export default function ApkBuilder() {
    const { builds } = usePage().props;

    const statusBadge = (status) => {
        const styles = {
            completed: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            building: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            pending: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
            failed: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${styles[status] || styles.pending}`}>
                {status || '-'}
            </span>
        );
    };

    return (
        <DashboardLayout title="Web to APK">
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-[13px] font-bold text-[#333] dark:text-white">APK Builds</h2>
                        <p className="text-[12px] text-[#999] dark:text-white/40 mt-1">Convert your website into an Android APK.</p>
                    </div>
                    <Link href="/user/hosting/apk/create" className="px-4 py-2 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                        <i className="fa-solid fa-plus mr-1.5"></i>New Build
                    </Link>
                </div>

                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Build Name</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">URL</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Created</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {(builds?.data || builds || []).length > 0 ? (builds?.data || builds).map((build) => (
                                    <tr key={build.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{build.app_name || build.name}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60 truncate max-w-[200px]">{build.website_url || '-'}</td>
                                        <td className="px-5 py-3">{statusBadge(build.status)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(build.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                        <td className="px-5 py-3 text-right">
                                            <Link href={`/user/hosting/apk/${build.id}`} className="text-[12px] text-[#7c3aed] hover:text-[#6d28d9] font-medium transition-colors">
                                                {build.status === 'completed' ? 'Download' : 'View'}
                                            </Link>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-12 text-center">
                                            <i className="fa-brands fa-android text-3xl text-[#e5e5e5] dark:text-white/10 mb-3 block"></i>
                                            <p className="text-[13px] text-[#999] dark:text-white/40 mb-3">No builds yet</p>
                                            <Link href="/user/hosting/apk/create" className="text-[13px] text-[#7c3aed] hover:text-[#6d28d9] font-medium transition-colors">
                                                Create your first APK
                                            </Link>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {builds?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Showing {builds.from}-{builds.to} of {builds.total}
                            </p>
                            <div className="flex items-center gap-1">
                                {builds.prev_page_url && (
                                    <Link href={builds.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {builds.next_page_url && (
                                    <Link href={builds.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
