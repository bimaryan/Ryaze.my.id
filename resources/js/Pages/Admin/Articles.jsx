import { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Articles() {
    const { articles, categories } = usePage().props;
    const [search, setSearch] = useState(articles?.search || '');
    const [status, setStatus] = useState(articles?.status || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/superadmin/articles', { search, status }, { preserveState: true, replace: true });
    };

    const statusBadge = (s) => {
        const map = {
            published: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
            draft: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
            archived: 'bg-[#f5f0ff] text-[#7c3aed] dark:bg-[#7c3aed]/20 dark:text-[#a78bfa]',
        };
        return (
            <span className={`text-[10px] font-bold uppercase px-2 py-0.5 ${map[s] || map.draft}`}>
                {s}
            </span>
        );
    };

    return (
        <DashboardLayout title="Artikel">
            <div className="space-y-6">
                <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e]">
                    <div className="px-5 py-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h2 className="text-sm font-bold text-[#333] dark:text-white">Semua Artikel</h2>
                        <div className="flex items-center gap-2">
                            <form onSubmit={handleFilter} className="flex items-center gap-2">
                                <select
                                    value={status}
                                    onChange={(e) => setStatus(e.target.value)}
                                    className="px-3 py-1.5 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                    <option value="archived">Archived</option>
                                </select>
                                <input
                                    type="text"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="Cari artikel..."
                                    className="px-3 py-1.5 text-[13px] border border-[#e5e5e5] dark:border-[#1a1a2e] bg-[#fafafa] dark:bg-white/[0.02] text-[#333] dark:text-white focus:outline-none focus:border-[#7c3aed] transition-colors w-48"
                                />
                                <button type="submit" className="px-3 py-1.5 bg-[#7c3aed] text-white text-[13px] font-semibold hover:bg-[#6d28d9] transition-colors">
                                    <i className="fa-solid fa-search"></i>
                                </button>
                            </form>
                            <Link
                                href="/superadmin/articles/create"
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
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Penulis</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Kategori</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Status</th>
                                    <th className="px-5 py-3 text-[11px] font-bold text-[#999] dark:text-white/40 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                                {articles?.data?.length > 0 ? articles.data.map((a) => (
                                    <tr key={a.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                        <td className="px-5 py-3">
                                            <Link href={`/superadmin/articles/${a.hashid || a.id}/edit`} className="text-[13px] font-semibold text-[#333] dark:text-white hover:text-[#7c3aed] transition-colors">
                                                {a.title}
                                            </Link>
                                        </td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{a.author?.name || '-'}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#666] dark:text-white/60">{a.category?.name || '-'}</td>
                                        <td className="px-5 py-3">{statusBadge(a.status)}</td>
                                        <td className="px-5 py-3 text-[13px] text-[#999] dark:text-white/40">
                                            {new Date(a.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-8 text-center text-sm text-[#999] dark:text-white/40">
                                            Tidak ada artikel
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    {articles?.last_page > 1 && (
                        <div className="px-5 py-3 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-between">
                            <p className="text-[12px] text-[#999] dark:text-white/40">
                                Menampilkan {articles.from}-{articles.to} dari {articles.total} artikel
                            </p>
                            <div className="flex items-center gap-1">
                                {articles.prev_page_url && (
                                    <Link href={articles.prev_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Prev</Link>
                                )}
                                {[...Array(articles.last_page)].map((_, i) => (
                                    <Link key={i + 1} href={`${articles.path}?page=${i + 1}`} preserveState className={`w-8 h-8 flex items-center justify-center text-[12px] font-medium border transition-colors ${articles.current_page === i + 1 ? 'bg-[#7c3aed] text-white border-[#7c3aed]' : 'border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed]'}`}>{i + 1}</Link>
                                ))}
                                {articles.next_page_url && (
                                    <Link href={articles.next_page_url} preserveState className="px-3 py-1.5 text-[12px] font-medium border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:border-[#7c3aed] hover:text-[#7c3aed] transition-colors">Next</Link>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
