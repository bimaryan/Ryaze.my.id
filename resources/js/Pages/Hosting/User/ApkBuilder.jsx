import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, Link } from '@inertiajs/react';
import { useState } from 'react';
import Swal from 'sweetalert2';

const statusConfig = {
    completed: { text: 'Selesai', color: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300', icon: 'fa-circle-check', animate: '' },
    building: { text: 'Membangun', color: 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300', icon: 'fa-spinner fa-spin', animate: '' },
    failed: { text: 'Gagal', color: 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300', icon: 'fa-circle-xmark', animate: '' },
    pending: { text: 'Antrian', color: 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300', icon: 'fa-clock', animate: '' },
};

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

export default function ApkBuilder({ builds }) {
    const buildList = Array.isArray(builds) ? builds : (builds?.data || []);
    const paginationLinks = builds?.links || [];
    const hasPages = paginationLinks.length > 3;

    function handleDelete(build) {
        Swal.fire({
            title: `Hapus build "${build.app_name}"?`,
            text: 'Build dan file APK akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('user_hosting.apk.destroy', build.id));
            }
        });
    }

    return (
        <DashboardLayout title="Web to APK">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-brands fa-android text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Web to APK Builder</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Konversi website menjadi aplikasi Android.</p>
                    </div>
                    <div className="ml-auto">
                        <Link href={route('user_hosting.apk.create')}
                            className="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            + Buat APK Baru
                        </Link>
                    </div>
                </div>
            </div>

            <div className="mt-6">
                {buildList.length === 0 ? (
                    <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] p-12 text-center">
                        <div className="w-16 h-16 bg-slate-100 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i className="fa-brands fa-android"></i>
                        </div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Belum ada build</h3>
                        <p className="text-[#999] dark:text-white/40 mb-6 text-sm">Buat APK pertama dari website Anda.</p>
                        <Link href={route('user_hosting.apk.create')}
                            className="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                            + Buat APK Baru
                        </Link>
                    </div>
                ) : (
                    <div className="bg-white dark:bg-[#0d0d18] rounded-2xl shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm text-left">
                                <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <tr>
                                        <th className="px-6 py-4">Nama Aplikasi</th>
                                        <th className="px-6 py-4">URL</th>
                                        <th className="px-6 py-4">Status</th>
                                        <th className="px-6 py-4">Dibuat</th>
                                        <th className="px-6 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                    {buildList.map(build => {
                                        const status = statusConfig[build.status] || statusConfig.pending;
                                        return (
                                            <tr key={build.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-2">
                                                        <i className="fa-brands fa-android text-emerald-500"></i>
                                                        <span className="font-bold text-[#333] dark:text-white text-sm">{build.app_name}</span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <a href={build.app_url} target="_blank" rel="noopener noreferrer"
                                                        className="text-xs text-[#7c3aed] dark:text-[#a78bfa] hover:underline font-mono truncate max-w-[200px] block">
                                                        {build.app_url}
                                                    </a>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span className={`inline-flex items-center text-[11px] font-bold uppercase tracking-wider ${status.color} px-2.5 py-1 rounded-full ${status.animate}`}>
                                                        <i className={`fa-solid ${status.icon} mr-1.5`}></i>
                                                        {status.text}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-xs text-[#999] dark:text-white/40">
                                                    {formatDate(build.created_at)}
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center justify-center gap-1">
                                                        {build.status === 'building' || build.status === 'pending' ? (
                                                            <Link href={route('user_hosting.apk.progress', build.id)}
                                                                className="w-8 h-8 rounded-lg flex items-center justify-center text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition" title="Lihat Progres">
                                                                <i className="fa-solid fa-spinner fa-spin text-sm"></i>
                                                            </Link>
                                                        ) : build.status === 'completed' ? (
                                                            <a href={route('user_hosting.apk.download', build.id)}
                                                                className="w-8 h-8 rounded-lg flex items-center justify-center text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition" title="Download APK">
                                                                <i className="fa-solid fa-download text-sm"></i>
                                                            </a>
                                                        ) : null}
                                                        <button onClick={() => handleDelete(build)}
                                                            className="w-8 h-8 rounded-lg flex items-center justify-center text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition" title="Hapus">
                                                            <i className="fa-solid fa-trash text-sm"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                        {hasPages && (
                            <div className="px-6 py-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <div className="flex items-center justify-center gap-1">
                                    {paginationLinks.map((link, i) => (
                                        <button key={i} onClick={() => link.url && router.get(link.url)} disabled={!link.url}
                                            className={`px-3 py-1.5 text-xs font-medium rounded-lg transition ${link.active ? 'bg-[#7c3aed] text-white' : link.url ? 'text-[#666] dark:text-white/60 hover:bg-[#fafafa] dark:hover:bg-white/[0.02]' : 'text-[#ccc] dark:text-white/20 cursor-not-allowed'}`}
                                            dangerouslySetInnerHTML={{ __html: link.label }} />
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
