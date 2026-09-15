import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Users() {
    const { users } = usePage().props;
    const [search, setSearch] = useState(users?.search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/superadmin/users', { search }, { preserveState: true, replace: true });
    };

    const roleBadge = (role) => {
        const colors = {
            superadmin: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
            admin_joki: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            admin_hosting: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-300',
            user: 'bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]',
        };
        const label = (role || 'user').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${colors[role] || colors.user}`}>
                {label}
            </span>
        );
    };

    return (
        <DashboardLayout title="Data Pengguna">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Semua Pengguna</h2>
                        <form onSubmit={handleSearch} className="flex items-center gap-2">
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari pengguna..."
                                className="px-3 py-1.5 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors w-56"
                            />
                            <button type="submit" className="px-3 py-1.5 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                                <i className="fa-solid fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Nama</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Email</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Role</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Dibuat</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {users?.data?.length > 0 ? users.data.map((user) => (
                                    <tr key={user.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3">
                                            <Link href={`/superadmin/users/${user.hashid || user.id}`} className="text-[13px] font-semibold text-[#333] dark:text-white hover:text-[#7c3aed] transition-colors">
                                                {user.name}
                                            </Link>
                                        </td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{user.email}</td>
                                        <td className="px-5 py-3">{roleBadge(user.role)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(user.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="4" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada data pengguna
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {users?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {users.from}-{users.to} dari {users.total} pengguna
                            </p>
                            <div className="flex items-center gap-1">
                                {users.prev_page_url && (
                                    <Link
                                        href={users.prev_page_url}
                                        preserveState
                                        className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors"
                                    >
                                        Prev
                                    </Link>
                                )}
                                {[...Array(users.last_page)].map((_, i) => (
                                    <Link
                                        key={i + 1}
                                        href={`${users.path}?page=${i + 1}`}
                                        preserveState
                                        className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${
                                            users.current_page === i + 1
                                                ? 'bg-[#7c3aed] text-white border-[#7c3aed]'
                                                : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'
                                        }`}
                                    >
                                        {i + 1}
                                    </Link>
                                ))}
                                {users.next_page_url && (
                                    <Link
                                        href={users.next_page_url}
                                        preserveState
                                        className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors"
                                    >
                                        Next
                                    </Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
