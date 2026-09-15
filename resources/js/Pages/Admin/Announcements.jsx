import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Announcements() {
    const { announcements } = usePage().props;
    const [search, setSearch] = useState(announcements?.search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/superadmin/announcements', { search }, { preserveState: true, replace: true });
    };

    const typeBadge = (type) => {
        const map = {
            info: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
            warning: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
            success: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${map[type] || map.info}`}>
                {type}
            </span>
        );
    };

    const statusBadge = (isActive) => {
        return isActive
            ? <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300">Aktif</span>
            : <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]">Nonaktif</span>;
    };

    return (
        <DashboardLayout title="Pengumuman">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Semua Pengumuman</h2>
                        <div className="flex items-center gap-2">
                            <form onSubmit={handleSearch} className="flex items-center gap-2">
                                <input
                                    type="text"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="Cari pengumuman..."
                                    className="px-3 py-1.5 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors w-48"
                                />
                                <button type="submit" className="px-3 py-1.5 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                                    <i className="fa-solid fa-search"></i>
                                </button>
                            </form>
                            <Link
                                href="/superadmin/announcements/create"
                                className="px-3 py-1.5 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors"
                            >
                                <i className="fa-solid fa-plus mr-1"></i> Baru
                            </Link>
                        </div>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Judul</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tipe</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {announcements?.data?.length > 0 ? announcements.data.map((a) => (
                                    <tr key={a.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3">
                                            <Link href={`/superadmin/announcements/${a.hashid || a.id}/edit`} className="text-[13px] font-semibold text-[#333] dark:text-white hover:text-[#7c3aed] transition-colors">
                                                {a.title}
                                            </Link>
                                        </td>
                                        <td className="px-5 py-3">{typeBadge(a.type)}</td>
                                        <td className="px-5 py-3">{statusBadge(a.is_active)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(a.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="4" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada pengumuman
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {announcements?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {announcements.from}-{announcements.to} dari {announcements.total} pengumuman
                            </p>
                            <div className="flex items-center gap-1">
                                {announcements.prev_page_url && (
                                    <Link href={announcements.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(announcements.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${announcements.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${announcements.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {announcements.next_page_url && (
                                    <Link href={announcements.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
