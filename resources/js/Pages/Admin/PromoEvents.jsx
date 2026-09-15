import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function PromoEvents() {
    const { promos } = usePage().props;
    const [search, setSearch] = useState(promos?.search || '');
    const [status, setStatus] = useState(promos?.status || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/superadmin/promo-events', { search, status }, { preserveState: true, replace: true });
    };

    const statusBadge = (isActive) => {
        return isActive
            ? <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300">Aktif</span>
            : <span className="text-[10px] font-bold uppercase px-2 py-0.5 bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]">Nonaktif</span>;
    };

    return (
        <DashboardLayout title="Promo Event">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Semua Promo Event</h2>
                        <div className="flex items-center gap-2">
                            <form onSubmit={handleFilter} className="flex items-center gap-2">
                                <select
                                    value={status}
                                    onChange={(e) => setStatus(e.target.value)}
                                    className="px-3 py-1.5 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Nonaktif</option>
                                </select>
                                <input
                                    type="text"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="Cari promo..."
                                    className="px-3 py-1.5 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors w-48"
                                />
                                <button type="submit" className="px-3 py-1.5 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                                    <i className="fa-solid fa-search"></i>
                                </button>
                            </form>
                            <Link
                                href="/superadmin/promo-events/create"
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
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Diskon</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Mulai</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Berakhir</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {promos?.data?.length > 0 ? promos.data.map((p) => (
                                    <tr key={p.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3">
                                            <Link href={`/superadmin/promo-events/${p.hashid || p.id}/edit`} className="text-[13px] font-semibold text-[#333] dark:text-white hover:text-[#7c3aed] transition-colors">
                                                {p.title}
                                            </Link>
                                        </td>
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#7c3aed]">{p.discount_percent}%</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">
                                            {new Date(p.start_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">
                                            {new Date(p.end_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                        <td className="px-5 py-3">{statusBadge(p.is_active)}</td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada promo event
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {promos?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {promos.from}-{promos.to} dari {promos.total} promo
                            </p>
                            <div className="flex items-center gap-1">
                                {promos.prev_page_url && (
                                    <Link href={promos.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(promos.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${promos.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${promos.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {promos.next_page_url && (
                                    <Link href={promos.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
