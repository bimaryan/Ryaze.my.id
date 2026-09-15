import DashboardLayout from '../../Layouts/DashboardLayout';
import { router, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

const statusTabs = [
    { label: 'Semua', value: '' },
    { label: 'Aktif', value: '1' },
    { label: 'Draft', value: '0' },
];

export default function Portfolios({ portfolios }) {
    const { url } = usePage();
    const params = new URLSearchParams(url.split('?')[1] || '');
    const currentStatus = params.get('status') || '';
    const currentSearch = params.get('search') || '';
    const [search, setSearch] = useState(currentSearch);
    const [deleteId, setDeleteId] = useState(null);

    function handleSearch(e) {
        e.preventDefault();
        const query = {};
        if (currentStatus) query.status = currentStatus;
        if (search) query.search = search;
        router.get(route('superadmin.portfolios.index'), query, { preserveState: true, replace: true });
    }

    function handleStatusFilter(status) {
        const query = {};
        if (status) query.status = status;
        if (currentSearch) query.search = currentSearch;
        router.get(route('superadmin.portfolios.index'), query, { preserveState: true, replace: true });
    }

    function toggleStatus(hashid) {
        router.patch(route('superadmin.portfolios.status.toggle', hashid), {}, { preserveScroll: true });
    }

    function handleDelete() {
        if (!deleteId) return;
        router.delete(route('superadmin.portfolios.destroy', deleteId), {
            onSuccess: () => setDeleteId(null),
        });
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    const paginationLinks = portfolios?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Manajemen Portofolio">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-briefcase text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Manajemen Portofolio</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola mahakarya terbaru yang akan ditampilkan di halaman depan.</p>
                    </div>
                </div>
            </div>

            <div className="flex flex-wrap items-center gap-2 mt-4">
                <Link href={route('superadmin.portfolios.create')} className="inline-flex items-center bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    <i className="fa-solid fa-plus mr-2"></i> Tambah Portofolio
                </Link>
            </div>

            <div className="flex flex-col sm:flex-row justify-between items-center mt-4 gap-4">
                <div className="flex items-center gap-3 w-full sm:w-auto">
                    <div className="flex bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg p-0.5">
                        {statusTabs.map((tab) => (
                            <button key={tab.value} onClick={() => handleStatusFilter(tab.value)} className={`px-3 py-1.5 text-xs font-medium rounded-md transition ${currentStatus === tab.value ? 'bg-white dark:bg-[#0d0d18]/60 shadow-sm text-[#333] dark:text-white' : 'text-[#999] dark:text-white/40 hover:text-[#666] dark:hover:text-white/60'}`}>
                                {tab.label}
                            </button>
                        ))}
                    </div>
                </div>

                <form onSubmit={handleSearch} className="flex items-center w-full sm:w-auto">
                    <div className="relative w-full sm:w-64">
                        <div className="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <i className="fa-solid fa-search text-[#999] dark:text-white/40 text-sm"></i>
                        </div>
                        <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} className="text-[#333] dark:text-white block ps-9 p-2 w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition" placeholder="Cari judul portofolio..." />
                    </div>
                    <button type="submit" className="p-2 ms-2 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#6d28d9] shadow-sm transition">
                        Cari
                    </button>
                    {currentSearch && (
                        <button type="button" onClick={() => { setSearch(''); handleStatusFilter(currentStatus); }} className="p-2 ms-2 text-sm font-medium text-[#666] dark:text-white/60 bg-[#f5f0ff] dark:bg-white/5 rounded-lg hover:bg-[#ede9fe] dark:hover:bg-white/10 transition">
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
                                <th className="px-6 py-4">Judul</th>
                                <th className="px-6 py-4">Tags</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4">Tgl Dibuat</th>
                                <th className="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {portfolios?.data?.length > 0 ? portfolios.data.map((portfolio) => (
                                <tr key={portfolio.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            {portfolio.image_path ? (
                                                <img src={`/storage/${portfolio.image_path}`} alt={portfolio.title} className="w-12 h-12 object-cover rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e]" />
                                            ) : (
                                                <div className="w-12 h-12 rounded-lg bg-[#f5f0ff] dark:bg-[#7c3aed]/10 flex items-center justify-center text-[#7c3aed]/60 border border-[#ede9fe] dark:border-[#7c3aed]/30">
                                                    <i className="fa-solid fa-image"></i>
                                                </div>
                                            )}
                                            <div className="flex flex-col min-w-0">
                                                <span className="font-medium text-[#333] dark:text-white truncate max-w-[250px]">{portfolio.title}</span>
                                                {portfolio.link_preview && (
                                                    <a href={portfolio.link_preview} target="_blank" rel="noopener noreferrer" className="text-[10px] text-[#7c3aed] dark:text-[#a78bfa] hover:underline mt-0.5 font-bold"><i className="fa-solid fa-link mr-1"></i>Live Preview</a>
                                                )}
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        {portfolio.tags && portfolio.tags.length > 0 ? (
                                            <div className="flex flex-wrap gap-1">
                                                {portfolio.tags.map((tag, i) => (
                                                    <span key={i} className="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#fafafa] dark:bg-white/5 text-[#666] dark:text-white/60 border border-[#e5e5e5] dark:border-[#1a1a2e]">{tag}</span>
                                                ))}
                                            </div>
                                        ) : (
                                            <span className="text-[#999] dark:text-white/40 italic text-xs">Tanpa tag</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4">
                                        {portfolio.is_active ? (
                                            <span className="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold uppercase rounded">Aktif</span>
                                        ) : (
                                            <span className="px-2 py-0.5 bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 text-[10px] font-bold uppercase rounded">Draft</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-[#999] dark:text-white/40">
                                        {formatDate(portfolio.created_at)}
                                    </td>
                                    <td className="px-6 py-4 text-center">
                                        <div className="flex items-center justify-center gap-2">
                                            <button onClick={() => toggleStatus(portfolio.hashid)} title={portfolio.is_active ? 'Jadikan Draft' : 'Aktifkan'} className={`p-1.5 rounded-lg transition ${portfolio.is_active ? 'text-emerald-500 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20' : 'text-[#999] dark:text-white/40 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10'}`}>
                                                <i className={`fa-solid ${portfolio.is_active ? 'fa-eye' : 'fa-eye-slash'}`}></i>
                                            </button>
                                            <Link href={route('superadmin.portfolios.edit', { hashid: portfolio.hashid })} className="p-1.5 text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 hover:bg-[#ede9fe] dark:hover:bg-[#7c3aed]/20 rounded-lg transition">
                                                <i className="fa-solid fa-pen-to-square"></i>
                                            </Link>
                                            <button onClick={() => setDeleteId(portfolio.hashid)} className="p-1.5 text-red-600 dark:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-lg transition">
                                                <i className="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="5" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <i className="fa-solid fa-briefcase text-3xl mb-3 text-slate-300 dark:text-slate-400 block"></i>
                                        <p>Belum ada data portofolio.</p>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {hasPages && (
                    <div className="px-6 py-4 border-t border-[#e5e5e5] dark:border-[#1a1a2e] flex items-center justify-center gap-1">
                        {paginationLinks.map((link, i) => (
                            <button key={i} disabled={!link.url} onClick={() => link.url && router.get(link.url, {}, { preserveState: true, replace: true })} className={`px-3 py-1.5 text-[13px] font-medium rounded-lg transition ${link.active ? 'bg-[#7c3aed] text-white shadow-sm' : link.url ? 'text-[#666] dark:text-white/60 hover:bg-[#f5f0ff] dark:hover:bg-white/5' : 'text-[#ccc] dark:text-white/20 cursor-not-allowed'}`} dangerouslySetInnerHTML={{ __html: link.label }} />
                        ))}
                    </div>
                )}
            </div>

            {deleteId && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div className="relative w-full max-w-sm m-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl p-6 text-center">
                        <div className="w-12 h-12 bg-red-50 dark:bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i className="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                        </div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Hapus Portofolio?</h3>
                        <p className="text-sm text-[#999] dark:text-white/40 mb-6">Data portofolio ini akan dihapus permanen dan tidak dapat dikembalikan.</p>
                        <div className="flex justify-center gap-3">
                            <button onClick={() => setDeleteId(null)} className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg hover:bg-[#f5f0ff] dark:hover:bg-white/5 transition">Batal</button>
                            <button onClick={handleDelete} className="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
