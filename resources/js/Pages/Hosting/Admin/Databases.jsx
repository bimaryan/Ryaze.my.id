import DashboardLayout from '../../../Layouts/DashboardLayout';
import { router, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import Swal from 'sweetalert2';

const dbTypeConfig = {
    mysql: { class: 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-300 border border-blue-200 dark:border-blue-500/40', label: 'MySQL' },
    pgsql: { class: 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/40', label: 'PostgreSQL' },
    redis: { class: 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-300 border border-rose-200 dark:border-rose-500/40', label: 'Redis' },
};

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

function DbRow({ db, type, pmaUrl }) {
    const [showPassword, setShowPassword] = useState(false);
    const config = dbTypeConfig[type] || dbTypeConfig.mysql;

    function handleDelete() {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Semua data di dalam database ini akan hilang permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('admin_hosting.databases.destroy', { hashid: db.hashid }));
            }
        });
    }

    return (
        <div className="flex items-center justify-between p-3 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl hover:border-indigo-300 hover:shadow-sm transition-all">
            <div>
                <div className="flex items-center gap-2">
                    <div className="font-mono font-semibold text-[#333] dark:text-white">{db.db_name}</div>
                    <span className={`text-[10px] px-1.5 py-0.5 rounded font-bold ${config.class}`}>{config.label}</span>
                </div>
                <div className="text-[11px] text-[#999] dark:text-white/40 mt-1">
                    <i className="fa-regular fa-calendar mr-1"></i> {formatDate(db.created_at)}
                </div>
            </div>
            <div className="flex items-center gap-2">
                {type === 'mysql' && pmaUrl && (
                    <form method="POST" action={`${pmaUrl.replace(/\/$/, '')}/index.php`} target="_blank" className="inline-block">
                        <input type="hidden" name="pma_username" value={db.user?.db_username || ''} />
                        <input type="hidden" name="pma_password" value={db.user?.db_password_decrypted || ''} />
                        <input type="hidden" name="server" value="1" />
                        <input type="hidden" name="pma_servername" value={db.host || 'localhost'} />
                        <button type="submit"
                            className="w-8 h-8 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm"
                            title="Buka phpMyAdmin">
                            <i className="fa-solid fa-server"></i>
                        </button>
                    </form>
                )}
                <button
                    onClick={handleDelete}
                    className="w-8 h-8 rounded-lg flex items-center justify-center text-red-600 dark:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm"
                    title="Hapus Database"
                >
                    <i className="fa-regular fa-trash-can"></i>
                </button>
            </div>
        </div>
    );
}

function UserDbRow({ user, pmaUrl }) {
    const [expanded, setExpanded] = useState(false);
    const [showPassword, setShowPassword] = useState(false);

    return (
        <tr className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
            <td className="px-6 py-4 align-top border-r border-[#e5e5e5] dark:border-[#1a1a2e]">
                <div className="font-bold text-[#333] dark:text-white">{user.name}</div>
                <div className="text-xs text-[#999] dark:text-white/40 mb-3">{user.email}</div>
                
                <div className="bg-indigo-50/50 dark:bg-indigo-500/10 rounded-xl p-3 border border-indigo-100 dark:border-indigo-500/30">
                    <div className="text-[11px] font-bold text-indigo-800 dark:text-indigo-300 uppercase tracking-wider mb-2">Kredensial Database</div>
                    <div className="space-y-2 text-sm">
                        <div className="flex justify-between items-center">
                            <span className="text-[#999] dark:text-white/40">User:</span>
                            <span className="font-mono bg-white dark:bg-[#0d0d18] text-[#333] dark:text-white px-1.5 py-0.5 rounded border border-[#e5e5e5] dark:border-[#1a1a2e]">{user.db_username}</span>
                        </div>
                        <div className="flex justify-between items-center group">
                            <span className="text-[#999] dark:text-white/40">Pass:</span>
                            <span
                                className="font-mono bg-white dark:bg-[#0d0d18] text-[#333] dark:text-white px-1.5 py-0.5 rounded border border-[#e5e5e5] dark:border-[#1a1a2e] cursor-pointer transition-all duration-300"
                                style={{ filter: showPassword ? 'none' : 'blur(4px)' }}
                                onClick={() => setShowPassword(!showPassword)}
                                title="Klik untuk melihat"
                            >
                                {user.db_password_decrypted ?? 'Error'}
                            </span>
                        </div>
                        <div className="flex justify-between items-center">
                            <span className="text-[#999] dark:text-white/40">Host:</span>
                            <span className="font-mono bg-white dark:bg-[#0d0d18] text-[#333] dark:text-white px-1.5 py-0.5 rounded border border-[#e5e5e5] dark:border-[#1a1a2e]">{user.db_host ?? 'localhost'}:{user.db_port ?? 3306}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td className="px-6 py-4 align-top">
                <div className="space-y-2">
                    {user.hosting_databases?.map((db) => (
                        <DbRow key={db.id} db={{ ...db, user }} type="mysql" pmaUrl={pmaUrl} />
                    ))}
                    {user.hosting_pgsql_databases?.map((db) => (
                        <DbRow key={db.id} db={{ ...db, user }} type="pgsql" />
                    ))}
                    {user.hosting_nosql_databases?.map((db) => (
                        <DbRow key={db.id} db={{ ...db, user }} type="redis" />
                    ))}
                </div>
            </td>
        </tr>
    );
}

export default function Databases({ usersWithDatabases, users }) {
    const { props } = usePage();
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [selectedUserId, setSelectedUserId] = useState('');
    const { data, setData, post, processing, errors, reset } = useForm({
        user_id: '',
        db_name: '',
        db_username: '',
        db_password: '',
    });

    const pmaUrl = import.meta.env.VITE_PMA_URL || '';

    const paginationLinks = usersWithDatabases?.links || [];
    const hasPages = paginationLinks.length > 3;

    function handleCreate(e) {
        e.preventDefault();
        post(route('admin_hosting.databases.store'), {
            onSuccess: () => {
                setShowCreateModal(false);
                reset();
                setSelectedUserId('');
            },
        });
    }

    const selectedUser = users?.find(u => u.id == data.user_id);
    const prefix = data.user_id ? `ryz_${data.user_id}_` : 'ryz_.._';

    return (
        <DashboardLayout title="Semua Database">
            <div className="mb-1">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-orange-50 dark:bg-orange-500/20 flex items-center justify-center">
                            <i className="fa-solid fa-database text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <div>
                            <h1 className="text-lg font-bold text-[#333] dark:text-white">Semua Database</h1>
                            <p className="text-[13px] text-[#999] dark:text-white/40">Kelola semua database klien di server.</p>
                        </div>
                    </div>
                    <button
                        onClick={() => setShowCreateModal(true)}
                        className="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors shadow-sm"
                    >
                        <i className="fa-solid fa-plus mr-2"></i> Buat Database
                    </button>
                </div>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4 w-1/3">Informasi Klien & Kredensial</th>
                                <th className="px-6 py-4">Daftar Database</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {usersWithDatabases?.data?.length > 0 ? usersWithDatabases.data.map((user) => (
                                <UserDbRow key={user.id} user={user} pmaUrl={pmaUrl} />
                            )) : (
                                <tr>
                                    <td colSpan="2" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <div className="flex flex-col items-center justify-center gap-2">
                                            <i className="fa-solid fa-database text-3xl text-slate-300 dark:text-slate-400"></i>
                                            <p>Belum ada database yang dibuat.</p>
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

            {showCreateModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div className="relative w-full max-w-md m-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl" onClick={(e) => e.stopPropagation()}>
                        <div className="flex items-center justify-between p-4 md:p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <h3 className="text-lg font-bold text-[#333] dark:text-white">Buat Database Baru</h3>
                            <button onClick={() => { setShowCreateModal(false); reset(); setSelectedUserId(''); }}
                                className="text-[#999] dark:text-white/40 hover:text-[#333] dark:hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition">
                                <i className="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <form onSubmit={handleCreate}>
                            <div className="p-4 md:p-5 space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-[#333] dark:text-white mb-1">Pilih Klien</label>
                                    <select
                                        value={data.user_id}
                                        onChange={(e) => {
                                            setData('user_id', e.target.value);
                                            setSelectedUserId(e.target.value);
                                        }}
                                        required
                                        className="w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
                                    >
                                        <option value="">-- Pilih Klien --</option>
                                        {users?.map((u) => (
                                            <option key={u.id} value={u.id}>{u.name} ({u.email})</option>
                                        ))}
                                    </select>
                                    <p className="text-[11px] text-[#999] dark:text-white/40 mt-1">Prefix ryz_{'{id}'}_ akan ditambahkan otomatis.</p>
                                    {errors.user_id && <p className="text-xs text-red-500 mt-1">{errors.user_id}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-[#333] dark:text-white mb-1">Nama Database <span className="text-red-500">*</span></label>
                                    <div className="flex rounded-xl overflow-hidden border border-[#e5e5e5] dark:border-[#1a1a2e] focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                                        <span className="inline-flex items-center px-3 bg-[#fafafa] dark:bg-white/5 text-[#999] dark:text-white/40 text-sm font-mono border-r border-[#e5e5e5] dark:border-[#1a1a2e] whitespace-nowrap">
                                            {prefix}
                                        </span>
                                        <input
                                            type="text"
                                            value={data.db_name}
                                            onChange={(e) => setData('db_name', e.target.value)}
                                            required
                                            maxLength={15}
                                            placeholder="contoh: wp_blog"
                                            className="flex-1 font-mono w-full bg-white dark:bg-[#0d0d18] border-0 px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-0 outline-none"
                                        />
                                    </div>
                                    {errors.db_name && <p className="text-xs text-red-500 mt-1">{errors.db_name}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-[#333] dark:text-white mb-1">Username Database <span className="text-[#999] dark:text-white/40 font-normal">(opsional)</span></label>
                                    <div className="flex rounded-xl overflow-hidden border border-[#e5e5e5] dark:border-[#1a1a2e] focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                                        <span className="inline-flex items-center px-3 bg-[#fafafa] dark:bg-white/5 text-[#999] dark:text-white/40 text-sm font-mono border-r border-[#e5e5e5] dark:border-[#1a1a2e] whitespace-nowrap">
                                            {prefix}
                                        </span>
                                        <input
                                            type="text"
                                            value={data.db_username}
                                            onChange={(e) => setData('db_username', e.target.value)}
                                            maxLength={15}
                                            placeholder="Kosongkan jika klien sudah punya database"
                                            className="flex-1 font-mono w-full bg-white dark:bg-[#0d0d18] border-0 px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-0 outline-none"
                                        />
                                    </div>
                                    <p className="text-[11px] text-[#999] dark:text-white/40 mt-1">Kosongkan username & password jika klien sudah memiliki database sebelumnya.</p>
                                    {errors.db_username && <p className="text-xs text-red-500 mt-1">{errors.db_username}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-[#333] dark:text-white mb-1">Password Database <span className="text-[#999] dark:text-white/40 font-normal">(opsional)</span></label>
                                    <div className="flex rounded-xl overflow-hidden border border-[#e5e5e5] dark:border-[#1a1a2e] focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                                        <span className="inline-flex items-center px-3 bg-[#fafafa] dark:bg-white/5 text-[#999] dark:text-white/40 text-sm font-mono border-r border-[#e5e5e5] dark:border-[#1a1a2e] whitespace-nowrap">
                                            {prefix}
                                        </span>
                                        <input
                                            type="text"
                                            value={data.db_password}
                                            onChange={(e) => setData('db_password', e.target.value)}
                                            maxLength={32}
                                            placeholder="Kosongkan jika klien sudah punya database"
                                            className="flex-1 font-mono w-full bg-white dark:bg-[#0d0d18] border-0 px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-0 outline-none"
                                        />
                                    </div>
                                    {errors.db_password && <p className="text-xs text-red-500 mt-1">{errors.db_password}</p>}
                                </div>
                            </div>
                            <div className="px-4 md:px-5 py-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex justify-end gap-3">
                                <button type="button" onClick={() => { setShowCreateModal(false); reset(); setSelectedUserId(''); }}
                                    className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg hover:bg-[#fafafa] dark:hover:bg-white/5 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" disabled={processing}
                                    className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition disabled:opacity-50">
                                    {processing ? 'Membuat...' : 'Buat Database'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
