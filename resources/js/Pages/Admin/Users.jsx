import DashboardLayout from '../../Layouts/DashboardLayout';
import { router, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

const roleTabs = [
    { label: 'Semua Pengguna', role: '', color: 'bg-[#7c3aed] text-white shadow-sm', inactive: 'bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5' },
    { label: 'Klien Joki', role: 'user_joki', color: 'bg-blue-600 text-white shadow-sm', inactive: 'bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5' },
    { label: 'Klien Hosting', role: 'user_hosting', color: 'bg-emerald-600 text-white shadow-sm', inactive: 'bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5' },
    { label: 'Admin Joki/Hosting', role: 'admin', color: 'bg-amber-600 text-white shadow-sm', inactive: 'bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5' },
    { label: 'Superadmin', role: 'superadmin', color: 'bg-purple-600 text-white shadow-sm', inactive: 'bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5' },
];

function roleBadge(role) {
    if (role === 'user_joki') return <span className="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-300 border border-blue-200 dark:border-blue-500/40">Jasa Joki Code</span>;
    if (role === 'user_hosting') return <span className="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40">App Deployment</span>;
    return <span className="px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-300 border border-purple-200 dark:border-purple-500/40 uppercase">{role?.replace(/_/g, ' ')}</span>;
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const day = d.getDate();
    const month = months[d.getMonth()];
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${day} ${month} ${year}, ${hours}:${minutes}`;
}

export default function Users({ users }) {
    const { url } = usePage();
    const currentUrl = url || '';
    const params = new URLSearchParams(currentUrl.split('?')[1] || '');
    const currentRole = params.get('role') || '';
    const currentSearch = params.get('search') || '';

    const [search, setSearch] = useState(currentSearch);

    function handleSearch(e) {
        e.preventDefault();
        const query = {};
        if (currentRole) query.role = currentRole;
        if (search) query.search = search;
        router.get(route('superadmin.users.index'), query, { preserveState: true, replace: true });
    }

    function handleReset() {
        setSearch('');
        const query = {};
        if (currentRole) query.role = currentRole;
        router.get(route('superadmin.users.index'), query, { preserveState: true, replace: true });
    }

    function expiredHosting(user) {
        if (['user_hosting', 'superadmin', 'admin_hosting'].includes(user.role)) {
            const billing = user.hosting_billings?.[0];
            const plan = billing?.plan || 'free';
            if (billing?.next_due_date && plan.toLowerCase() !== 'free') {
                const d = new Date(billing.next_due_date);
                const isPast = d < new Date();
                const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                return <span className={`text-xs font-medium ${isPast ? 'text-red-500 dark:text-red-400' : 'text-emerald-500 dark:text-emerald-400'}`}>{`${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`}</span>;
            }
            return <span className="text-slate-400 dark:text-slate-500 text-xs italic">-</span>;
        }
        return <span className="text-slate-400 dark:text-slate-500 text-xs">-</span>;
    }

    const paginationLinks = users?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Manajemen Pengguna">
            <div className="space-y-0">
                <div className="mb-1">
                    <div className="flex items-center gap-3 mb-1">
                        <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                            <i className="fa-solid fa-users text-[#7c3aed] dark:text-[#a78bfa]"></i>
                        </div>
                        <div>
                            <h1 className="text-lg font-bold text-[#333] dark:text-white">Manajemen Pengguna</h1>
                            <p className="text-[13px] text-[#999] dark:text-white/40">Daftar semua klien dan admin di dalam sistem.</p>
                        </div>
                    </div>
                </div>

                <div className="flex overflow-x-auto gap-2 pb-2 hide-scrollbar mt-4">
                    {roleTabs.map((tab) => {
                        const isActive = currentRole === tab.role;
                        const cls = isActive ? tab.color : tab.inactive;
                        return (
                            <button
                                key={tab.role}
                                onClick={() => {
                                    const query = {};
                                    if (tab.role) query.role = tab.role;
                                    if (currentSearch) query.search = currentSearch;
                                    router.get(route('superadmin.users.index'), query, { preserveState: true, replace: true });
                                }}
                                className={`px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition ${cls}`}
                            >
                                {tab.label}
                            </button>
                        );
                    })}
                </div>

                <div className="flex flex-col sm:flex-row justify-between items-center mt-4 gap-4">
                    <form onSubmit={handleSearch} className="flex items-center w-full sm:w-auto">
                        <div className="relative w-full sm:w-64">
                            <div className="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <i className="fa-solid fa-search text-[#999] dark:text-white/40 text-sm"></i>
                            </div>
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="text-[#333] dark:text-white block ps-9 p-2 w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition"
                                placeholder="Cari nama atau email..."
                            />
                        </div>
                        <button type="submit" className="p-2 ms-2 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] shadow-sm transition">
                            Cari
                        </button>
                        {currentSearch && (
                            <button type="button" onClick={handleReset} className="p-2 ms-2 text-sm font-medium text-[#666] dark:text-white/60 bg-[#f5f0ff] dark:bg-white/5 rounded-lg hover:bg-[#ede9fe] dark:hover:bg-white/10 transition">
                                Reset
                            </button>
                        )}
                    </form>
                </div>

                <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm text-left">
                            <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                <tr>
                                    <th className="px-6 py-4">Nama Pengguna</th>
                                    <th className="px-6 py-4">Email Address</th>
                                    <th className="px-6 py-4">Role / Tipe Akun</th>
                                    <th className="px-6 py-4">Expired Hosting</th>
                                    <th className="px-6 py-4">Tanggal Daftar</th>
                                    <th className="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {users?.data?.length > 0 ? users.data.map((user) => (
                                    <tr key={user.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-6 py-4 font-medium text-[#333] dark:text-white flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-full bg-[#f5f0ff] dark:bg-[#7c3aed]/20 text-[#7c3aed] dark:text-[#a78bfa] flex items-center justify-center font-bold text-sm uppercase shadow-sm border border-[#e5e5e5] dark:border-[#1a1a2e]">
                                                {user.name?.charAt(0)?.toUpperCase()}
                                            </div>
                                            {user.name}
                                        </td>
                                        <td className="px-6 py-4 text-[#666] dark:text-white/60">{user.email}</td>
                                        <td className="px-6 py-4">{roleBadge(user.role)}</td>
                                        <td className="px-6 py-4">{expiredHosting(user)}</td>
                                        <td className="px-6 py-4 text-[#666] dark:text-white/60">{formatDate(user.created_at)}</td>
                                        <td className="px-6 py-4 text-center">
                                            <Link
                                                href={route('superadmin.users.show', user.hashid)}
                                                className="w-8 h-8 mx-auto rounded-lg flex items-center justify-center text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 hover:bg-[#7c3aed] hover:text-white transition-all duration-200 shadow-sm"
                                                title="Detail Profil"
                                            >
                                                <i className="fa-regular fa-eye"></i>
                                            </Link>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="6" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                            <i className="fa-solid fa-users-slash text-3xl mb-3 text-slate-300 dark:text-slate-400 block"></i>
                                            <p className="text-sm">Belum ada data pengguna.</p>
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
            </div>
        </DashboardLayout>
    );
}
