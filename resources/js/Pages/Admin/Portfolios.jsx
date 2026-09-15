import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Portfolios() {
    const { portfolios } = usePage().props;
    const [search, setSearch] = useState(portfolios?.search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/superadmin/portfolios', { search }, { preserveState: true, replace: true });
    };

    return (
        <DashboardLayout title="Portofolio">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Semua Portofolio</h2>
                        <form onSubmit={handleSearch} className="flex items-center gap-2">
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari portofolio..."
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
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Judul</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Gambar</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {portfolios?.data?.length > 0 ? portfolios.data.map((p) => (
                                    <tr key={p.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3 text-[13px] font-semibold text-[#333] dark:text-white">{p.title}</td>
                                        <td className="px-5 py-3">
                                            {p.image ? (
                                                <img src={p.image} alt={p.title} className="w-16 h-10 object-cover border border-[#e5e5e5] dark:border-[#1a1a2e]" />
                                            ) : (
                                                <span className="text-[12px] text-[#999] dark:text-white/40">-</span>
                                            )}
                                        </td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(p.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="3" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada portofolio
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {portfolios?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {portfolios.from}-{portfolios.to} dari {portfolios.total} portofolio
                            </p>
                            <div className="flex items-center gap-1">
                                {portfolios.prev_page_url && (
                                    <Link href={portfolios.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(portfolios.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${portfolios.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${portfolios.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {portfolios.next_page_url && (
                                    <Link href={portfolios.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
