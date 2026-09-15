import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router } from '@inertiajs/react';
import Swal from 'sweetalert2';

const statusConfig = {
    unpaid: { class: 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300', label: 'Belum Bayar' },
    error: { class: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300', label: 'Error' },
    suspended: { class: 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300', label: 'Disuspend' },
};

export default function Pending({ projects }) {
    const paginationLinks = projects?.links || [];
    const hasPages = paginationLinks.length > 3;

    function handleActivate(project) {
        Swal.fire({
            title: 'Aktifkan Project?',
            text: `Yakin ingin mengaktifkan project ${project.project_name}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Aktifkan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                router.patch(route('admin_hosting.activate', project.hashid));
            }
        });
    }

    return (
        <DashboardLayout title="Membutuhkan Tindakan">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-amber-50 dark:bg-amber-500/20 flex items-center justify-center">
                        <i className="fa-solid fa-clock text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Membutuhkan Tindakan</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Project yang butuh aktivasi, suspend, atau perbaikan error.</p>
                    </div>
                </div>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Project & Klien</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {projects?.data?.length > 0 ? projects.data.map((project) => {
                                const badge = statusConfig[project.status] || { class: 'bg-indigo-100 text-indigo-700', label: project.status };
                                return (
                                    <tr key={project.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4">
                                            <p className="text-sm font-semibold text-[#333] dark:text-white">{project.project_name}</p>
                                            <p className="text-xs text-[#999] dark:text-white/40">{project.client?.name ?? '—'} · {project.ryaze_domain}</p>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className={`text-xs font-medium px-2.5 py-1 rounded-full ${badge.class}`}>{badge.label}</span>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <div className="flex items-center justify-center gap-2">
                                                {['unpaid', 'suspended'].includes(project.status) && (
                                                    <button
                                                        onClick={() => handleActivate(project)}
                                                        className="text-xs bg-emerald-500 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-600 transition-colors"
                                                    >
                                                        Aktifkan
                                                    </button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                );
                            }) : (
                                <tr>
                                    <td colSpan="3" className="px-6 py-10 text-center text-[#999] dark:text-white/40">
                                        Tidak ada project yang membutuhkan tindakan.
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
