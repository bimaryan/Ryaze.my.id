import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, useForm } from '@inertiajs/react';
import { useState } from 'react';

function formatStorage(mb) {
    if (mb >= 1024) return `${(mb / 1024).toFixed(1)} GB`;
    return `${mb} MB`;
}

export default function Storage({ users }) {
    const [editUser, setEditUser] = useState(null);
    const [expandedUsers, setExpandedUsers] = useState({});
    const { data, setData, put, processing, errors, reset } = useForm({
        storage_limit_mb: '',
    });

    const paginationLinks = users?.links || [];
    const hasPages = paginationLinks.length > 3;

    function openEdit(user) {
        setEditUser(user);
        setData('storage_limit_mb', user.hosting_storage_limit_mb || 100);
    }

    function closeEdit() {
        setEditUser(null);
        reset();
    }

    function handleEdit(e) {
        e.preventDefault();
        put(route('admin_hosting.storage.update', { hashid: editUser.hashid }), {
            onSuccess: () => closeEdit(),
        });
    }

    function toggleExpand(userId) {
        setExpandedUsers(prev => ({ ...prev, [userId]: !prev[userId] }));
    }

    return (
        <DashboardLayout title="Alokasi Penyimpanan Akun">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-teal-50 dark:bg-teal-500/20 flex items-center justify-center">
                        <i className="fa-solid fa-hard-drive text-teal-600 dark:text-teal-400"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Alokasi Penyimpanan Akun</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Daftar semua klien hosting dan batasan penyimpanannya.</p>
                    </div>
                </div>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Klien</th>
                                <th className="px-6 py-4 text-center">Jumlah Proyek</th>
                                <th className="px-6 py-4 text-right">Limit Storage Akun</th>
                                <th className="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {users?.data?.length > 0 ? users.data.map((user) => {
                                const isExpanded = expandedUsers[user.id];
                                const isAdmin = ['superadmin', 'admin_hosting'].includes(user.role);
                                return (
                                    <tr key={user.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="font-medium text-[#333] dark:text-white">{user.name}</div>
                                            <div className="text-xs text-[#999] dark:text-white/40">{user.email}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <button
                                                onClick={() => toggleExpand(user.id)}
                                                className="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-500/30 transition"
                                            >
                                                {user.hosting_projects_count} Proyek
                                                <i className={`fa-solid fa-chevron-${isExpanded ? 'up' : 'down'} text-[10px] ml-0.5`}></i>
                                            </button>
                                            {isExpanded && user.hosting_projects?.length > 0 && (
                                                <div className="mt-3 space-y-1.5 border-t border-[#e5e5e5] dark:border-[#1a1a2e] pt-3">
                                                    {user.hosting_projects.map((project) => {
                                                        const customDomainObj = project.domains?.[0];
                                                        const displayDomain = customDomainObj ? customDomainObj.domain_name : (project.custom_domain || project.ryaze_domain);
                                                        return (
                                                            <div key={project.id} className="flex flex-col">
                                                                <div className="flex items-center gap-1.5">
                                                                    <div className={`w-1.5 h-1.5 rounded-full ${project.status === 'active' ? 'bg-emerald-500' : 'bg-rose-500'}`}></div>
                                                                    <span className="text-xs font-medium text-[#333] dark:text-white">{project.name || project.project_name}</span>
                                                                </div>
                                                                <a href={`https://${displayDomain}`} target="_blank" rel="noopener noreferrer"
                                                                    className="text-[10px] text-[#999] dark:text-white/40 hover:text-indigo-600 dark:hover:text-indigo-400 truncate max-w-[200px] ml-3 transition-colors">
                                                                    <i className="fa-solid fa-link mr-1"></i>{displayDomain}
                                                                </a>
                                                            </div>
                                                        );
                                                    })}
                                                </div>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <span className="font-semibold text-[#333] dark:text-white">
                                                {isAdmin ? (
                                                    <span>&infin; <span className="text-sm">(Unlimited)</span></span>
                                                ) : (
                                                    formatStorage(user.hosting_storage_limit_mb || 0)
                                                )}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <button
                                                onClick={() => openEdit(user)}
                                                className="w-8 h-8 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm mx-auto"
                                                title="Ubah Limit Storage"
                                            >
                                                <i className="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </td>
                                    </tr>
                                );
                            }) : (
                                <tr>
                                    <td colSpan="4" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <div className="flex flex-col items-center justify-center gap-2">
                                            <i className="fa-solid fa-users text-3xl text-slate-300 dark:text-slate-400"></i>
                                            <p>Belum ada klien hosting.</p>
                                        </div>
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

            {editUser && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" onClick={closeEdit}>
                    <div className="relative w-full max-w-md m-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl" onClick={(e) => e.stopPropagation()}>
                        <div className="flex items-center justify-between p-4 md:p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-lg font-bold text-[#333] dark:text-white">Ubah Limit Storage</h3>
                            <button onClick={closeEdit}
                                className="text-[#999] dark:text-white/40 hover:text-[#333] dark:hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition">
                                <i className="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <form onSubmit={handleEdit}>
                            <div className="p-4 md:p-5 space-y-4">
                                <p className="text-sm text-[#666] dark:text-white/60">
                                    Ubah limit penyimpanan untuk klien: <strong className="text-[#333] dark:text-white">{editUser.name}</strong>
                                </p>
                                <div>
                                    <label className="block text-sm font-medium text-[#333] dark:text-white mb-1">Limit Storage Baru (MB)</label>
                                    <input
                                        type="number"
                                        value={data.storage_limit_mb}
                                        onChange={(e) => setData('storage_limit_mb', e.target.value)}
                                        required
                                        min={100}
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                                    />
                                    <p className="text-[11px] text-[#999] dark:text-white/40 mt-1">1024 MB = 1 GB. Minimal 100 MB.</p>
                                    {errors.storage_limit_mb && <p className="text-xs text-red-500 mt-1">{errors.storage_limit_mb}</p>}
                                </div>
                            </div>
                            <div className="px-4 md:px-5 py-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end gap-3">
                                <button type="button" onClick={closeEdit}
                                    className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg hover:bg-[#fafafa] dark:hover:bg-white/5 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" disabled={processing}
                                    className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition disabled:opacity-50">
                                    {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
