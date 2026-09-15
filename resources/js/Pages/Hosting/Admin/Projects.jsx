import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, Link } from '@inertiajs/react';
import { useState } from 'react';
import Swal from 'sweetalert2';

const statusConfig = {
    active: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
    building: 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300',
    unpaid: 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300',
    suspended: 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300',
    error: 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300',
};

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function confirmAction(message, callback) {
    Swal.fire({
        title: 'Konfirmasi Tindakan',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Lanjutkan!',
    }).then((result) => {
        if (result.isConfirmed) callback();
    });
}

export default function Projects({ projects }) {
    const paginationLinks = projects?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Master Data Project">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i className="fa-solid fa-box-open text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Master Data Project</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola seluruh project hosting dari semua pengguna.</p>
                    </div>
                </div>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Project</th>
                                <th className="px-6 py-4">Klien</th>
                                <th className="px-6 py-4">Domain</th>
                                <th className="px-6 py-4 text-center">Status</th>
                                <th className="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {projects?.data?.length > 0 ? projects.data.map((project) => (
                                <tr key={project.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-6 py-4">
                                        <p className="font-semibold text-[#333] dark:text-white">{project.project_name}</p>
                                        <p className="text-xs text-[#999] dark:text-white/40 uppercase">{project.framework}</p>
                                    </td>
                                    <td className="px-6 py-4">
                                        <p className="font-medium text-[#666] dark:text-white/60">{project.client?.name ?? '—'}</p>
                                    </td>
                                    <td className="px-6 py-4">
                                        {(() => {
                                            const activeDomain = project.domains?.find(d => d.ssl_status === 'active');
                                            const displayUrl = activeDomain ? activeDomain.domain_name : project.ryaze_domain;
                                            return (
                                                <a href={`https://${displayUrl}`} target="_blank" rel="noopener noreferrer"
                                                    className="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-mono">{displayUrl}</a>
                                            );
                                        })()}
                                    </td>
                                    <td className="px-6 py-4 text-center">
                                        <span className={`text-xs font-semibold px-2.5 py-1 rounded-full ${statusConfig[project.status] || 'bg-gray-100 text-gray-600'}`}>
                                            {project.status?.charAt(0).toUpperCase() + project.status?.slice(1)}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-center">
                                        <div className="flex justify-center gap-2">
                                            <Link
                                                href={route('user_hosting.show', { hashid: project.hashid })}
                                                className="w-8 h-8 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm"
                                                title="Kelola"
                                            >
                                                <i className="fa-solid fa-gear"></i>
                                            </Link>

                                            {['unpaid', 'suspended', 'error'].includes(project.status) && (
                                                <button
                                                    onClick={() => confirmAction(`Aktifkan project ${project.project_name}?`, () => {
                                                        router.patch(route('admin_hosting.activate', { hashid: project.hashid }));
                                                    })}
                                                    className="w-8 h-8 rounded-lg flex items-center justify-center text-emerald-600 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-600 hover:text-white transition-all duration-200 shadow-sm"
                                                    title="Aktifkan"
                                                >
                                                    <i className="fa-solid fa-play"></i>
                                                </button>
                                            )}

                                            {project.status === 'active' && (
                                                <button
                                                    onClick={() => confirmAction(`Suspend project ${project.project_name}?`, () => {
                                                        router.patch(route('admin_hosting.suspend', { hashid: project.hashid }));
                                                    })}
                                                    className="w-8 h-8 rounded-lg flex items-center justify-center text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-600 hover:text-white transition-all duration-200 shadow-sm"
                                                    title="Suspend"
                                                >
                                                    <i className="fa-solid fa-pause"></i>
                                                </button>
                                            )}

                                            <button
                                                onClick={() => Swal.fire({
                                                    title: 'Hapus PERMANEN?',
                                                    text: `Project ${project.project_name} akan dihapus permanen!`,
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#ef4444',
                                                    cancelButtonColor: '#94a3b8',
                                                    confirmButtonText: 'Ya, Hapus!',
                                                    cancelButtonText: 'Batal',
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        router.delete(route('admin_hosting.destroy', { hashid: project.hashid }));
                                                    }
                                                })}
                                                className="w-8 h-8 rounded-lg flex items-center justify-center text-red-600 dark:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm"
                                                title="Hapus"
                                            >
                                                <i className="fa-regular fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="5" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <i className="fa-solid fa-box-open text-3xl mb-3 text-slate-300 dark:text-slate-400 block"></i>
                                        <p className="text-sm">Belum ada project hosting.</p>
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
