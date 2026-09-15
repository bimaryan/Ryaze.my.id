import DashboardLayout from '../../Layouts/DashboardLayout';
import { router, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

const statusTabs = [
    { label: 'Semua', value: '' },
    { label: 'Draft', value: 'draft' },
    { label: 'Published', value: 'published' },
];

export default function Articles({ articles, categories }) {
    const { url } = usePage();
    const params = new URLSearchParams(url.split('?')[1] || '');
    const currentStatus = params.get('status') || '';
    const currentSearch = params.get('search') || '';

    const [search, setSearch] = useState(currentSearch);
    const [showImportModal, setShowImportModal] = useState(false);
    const [showAIModal, setShowAIModal] = useState(false);
    const [deleteId, setDeleteId] = useState(null);

    const [importFile, setImportFile] = useState(null);
    const [aiTopic, setAiTopic] = useState('');
    const [aiCategory, setAiCategory] = useState('');
    const [aiPublish, setAiPublish] = useState(false);

    function handleSearch(e) {
        e.preventDefault();
        const query = {};
        if (currentStatus) query.status = currentStatus;
        if (search) query.search = search;
        router.get(route('superadmin.articles.index'), query, { preserveState: true, replace: true });
    }

    function handleStatusFilter(status) {
        const query = {};
        if (status) query.status = status;
        if (currentSearch) query.search = currentSearch;
        router.get(route('superadmin.articles.index'), query, { preserveState: true, replace: true });
    }

    function handleImport(e) {
        e.preventDefault();
        if (!importFile) return;
        router.post(route('superadmin.articles.import'), { file: importFile }, {
            forceFormData: true,
            onSuccess: () => {
                setShowImportModal(false);
                setImportFile(null);
            },
        });
    }

    function handleAIGenerate(e) {
        e.preventDefault();
        router.post(route('superadmin.articles.generate_ai'), {
            topic: aiTopic,
            category_id: aiCategory,
            publish: aiPublish ? 1 : 0,
        }, {
            onSuccess: () => {
                setShowAIModal(false);
                setAiTopic('');
                setAiCategory('');
                setAiPublish(false);
            },
        });
    }

    function toggleFeatured(hashid) {
        router.patch(route('superadmin.articles.featured', hashid), {}, { preserveScroll: true });
    }

    function toggleStatus(hashid) {
        router.patch(route('superadmin.articles.status', hashid), {}, { preserveScroll: true });
    }

    function handleDelete() {
        if (!deleteId) return;
        router.delete(route('superadmin.articles.destroy', deleteId), {
            onSuccess: () => setDeleteId(null),
        });
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    const paginationLinks = articles?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Manajemen Artikel">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-newspaper text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Manajemen Artikel</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola semua artikel dan konten blog yang dipublikasikan.</p>
                    </div>
                </div>
            </div>

            <div className="flex flex-wrap items-center gap-2 mt-4">
                <Link href={route('superadmin.article_categories.index')} className="inline-flex items-center bg-white dark:bg-[#0d0d18]/60 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#fafafa] dark:hover:bg-white/5 px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    <i className="fa-solid fa-folder mr-2"></i> Kategori
                </Link>
                <button onClick={() => setShowImportModal(true)} className="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    <i className="fa-solid fa-file-excel mr-2"></i> Import Excel
                </button>
                <button onClick={() => setShowAIModal(true)} className="inline-flex items-center bg-violet-600 hover:bg-violet-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    <i className="fa-solid fa-wand-magic-sparkles mr-2"></i> Buat dengan AI
                </button>
                <Link href={route('superadmin.articles.create')} className="inline-flex items-center bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    <i className="fa-solid fa-plus mr-2"></i> Tulis Artikel
                </Link>
            </div>

            <div className="flex flex-col sm:flex-row justify-between items-center mt-4 gap-4">
                <div className="flex items-center gap-3 w-full sm:w-auto">
                    <div className="flex bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg p-0.5">
                        {statusTabs.map((tab) => (
                            <button
                                key={tab.value}
                                onClick={() => handleStatusFilter(tab.value)}
                                className={`px-3 py-1.5 text-xs font-medium rounded-md transition ${
                                    currentStatus === tab.value
                                        ? 'bg-white dark:bg-[#0d0d18]/60 shadow-sm text-[#333] dark:text-white'
                                        : 'text-[#999] dark:text-white/40 hover:text-[#666] dark:hover:text-white/60'
                                }`}
                            >
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
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="text-[#333] dark:text-white block ps-9 p-2 w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition"
                            placeholder="Cari judul artikel..."
                        />
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
                                <th className="px-6 py-4">Artikel</th>
                                <th className="px-6 py-4">Kategori</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4">Views</th>
                                <th className="px-6 py-4">Tanggal</th>
                                <th className="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {articles?.data?.length > 0 ? articles.data.map((article) => (
                                <tr key={article.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            {article.cover_image ? (
                                                <img src={`/storage/${article.cover_image}`} alt={article.title} className="w-12 h-12 object-cover rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e]" />
                                            ) : (
                                                <div className="w-12 h-12 rounded-lg bg-[#f5f0ff] dark:bg-[#7c3aed]/10 flex items-center justify-center text-[#7c3aed]/60 border border-[#ede9fe] dark:border-[#7c3aed]/30">
                                                    <i className="fa-solid fa-file-lines"></i>
                                                </div>
                                            )}
                                            <div className="flex flex-col min-w-0">
                                                <span className="font-medium text-[#333] dark:text-white truncate max-w-[250px]">{article.title}</span>
                                                <span className="text-xs text-[#999] dark:text-white/40">{article.user?.name || '-'}</span>
                                                {article.is_featured && (
                                                    <span className="inline-flex items-center gap-1 text-[10px] text-amber-600 dark:text-amber-300 font-bold mt-0.5">
                                                        <i className="fa-solid fa-star"></i> Sorotan
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        {article.category ? (
                                            <span className="px-2 py-0.5 bg-[#fafafa] dark:bg-[#0d0d18] text-[#666] dark:text-white/60 text-xs font-medium rounded border border-[#e5e5e5] dark:border-[#1a1a2e]">{article.category.name}</span>
                                        ) : (
                                            <span className="text-xs text-[#999] dark:text-white/40">-</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4">
                                        {article.status === 'published' ? (
                                            <span className="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold uppercase rounded">Published</span>
                                        ) : article.status === 'draft' ? (
                                            <span className="px-2 py-0.5 bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 text-[10px] font-bold uppercase rounded">Draft</span>
                                        ) : (
                                            <span className="px-2 py-0.5 bg-[#fafafa] dark:bg-[#0d0d18] text-[#666] dark:text-white/60 text-[10px] font-bold uppercase rounded border border-[#e5e5e5] dark:border-[#1a1a2e]">Archived</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-[#666] dark:text-white/60">
                                        <i className="fa-solid fa-eye text-[#999] dark:text-white/40 mr-1"></i>{article.views_count?.toLocaleString() || 0}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-[#999] dark:text-white/40">
                                        {formatDate(article.created_at)}
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex items-center justify-center gap-2">
                                            <button
                                                onClick={() => toggleFeatured(article.hashid)}
                                                title={article.is_featured ? 'Hapus Sorotan' : 'Jadikan Sorotan'}
                                                className={`p-1.5 rounded-lg transition ${article.is_featured ? 'text-amber-500 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20' : 'text-[#999] dark:text-white/40 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-500/10'}`}
                                            >
                                                <i className="fa-solid fa-star"></i>
                                            </button>
                                            <button
                                                onClick={() => toggleStatus(article.hashid)}
                                                title={article.status === 'published' ? 'Draft' : 'Publish'}
                                                className={`p-1.5 rounded-lg transition ${article.status === 'published' ? 'text-emerald-500 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20' : 'text-[#999] dark:text-white/40 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10'}`}
                                            >
                                                <i className={`fa-solid ${article.status === 'published' ? 'fa-eye' : 'fa-eye-slash'}`}></i>
                                            </button>
                                            <Link
                                                href={route('superadmin.articles.edit', { hashid: article.hashid })}
                                                className="p-1.5 text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 hover:bg-[#ede9fe] dark:hover:bg-[#7c3aed]/20 rounded-lg transition"
                                            >
                                                <i className="fa-solid fa-pen-to-square"></i>
                                            </Link>
                                            <button
                                                onClick={() => setDeleteId(article.hashid)}
                                                className="p-1.5 text-red-600 dark:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-lg transition"
                                            >
                                                <i className="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="6" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <i className="fa-solid fa-newspaper text-4xl mb-3 text-slate-300 dark:text-slate-400 block"></i>
                                        <p className="font-medium">Belum ada artikel.</p>
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

            {/* Import Modal */}
            {showImportModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div className="relative w-full max-w-md m-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl">
                        <div className="flex items-center justify-between p-4 md:p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] rounded-t">
                            <h3 className="text-lg font-semibold text-[#333] dark:text-white">Import Artikel (Excel/CSV)</h3>
                            <button onClick={() => setShowImportModal(false)} className="text-[#999] dark:text-white/40 hover:text-[#333] dark:hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition">
                                <i className="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <form onSubmit={handleImport}>
                            <div className="p-4 md:p-5 space-y-4">
                                <div className="bg-[#f5f0ff] dark:bg-[#7c3aed]/10 text-[#7c3aed] dark:text-[#a78bfa] p-3 rounded-lg text-xs font-medium border border-[#ede9fe] dark:border-[#7c3aed]/30">
                                    Unduh template Excel untuk memastikan format kolom sudah benar sebelum mengunggah.
                                    <a href={route('superadmin.articles.template')} className="inline-block mt-2 underline font-bold"><i className="fa-solid fa-download"></i> Download Template</a>
                                </div>
                                <div>
                                    <label className="block mb-2 text-sm font-medium text-[#333] dark:text-white">Upload File Excel/CSV</label>
                                    <input
                                        type="file"
                                        accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                        onChange={(e) => setImportFile(e.target.files[0])}
                                        required
                                        className="block w-full text-sm text-[#333] dark:text-white border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg cursor-pointer bg-[#fafafa] dark:bg-[#0d0d18] focus:outline-none p-2.5"
                                    />
                                </div>
                            </div>
                            <div className="flex items-center p-4 md:p-5 border-t border-[#e5e5e5] dark:border-[#1a1a2e] rounded-b">
                                <button type="submit" className="text-white bg-[#7c3aed] hover:bg-[#6d28d9] font-medium rounded-lg text-sm px-5 py-2.5 transition">Import Data</button>
                                <button type="button" onClick={() => setShowImportModal(false)} className="py-2.5 px-5 ms-3 text-sm font-medium text-[#666] dark:text-white/60 hover:text-[#333] dark:hover:text-white bg-white dark:bg-[#0d0d18] rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e] hover:bg-[#fafafa] dark:hover:bg-white/5 transition-colors">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* AI Generate Modal */}
            {showAIModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div className="relative w-full max-w-lg m-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl">
                        <div className="flex items-center justify-between p-4 md:p-5 border-b border-[#e5e5e5] dark:border-[#1a1a2e] rounded-t">
                            <div>
                                <h3 className="text-lg font-semibold text-[#333] dark:text-white">Buat Artikel dengan AI</h3>
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">AI akan membuat isi, SEO, tag, dan gambar sampul.</p>
                            </div>
                            <button onClick={() => setShowAIModal(false)} className="text-[#999] dark:text-white/40 hover:bg-[#fafafa] dark:hover:bg-white/5 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition">
                                <i className="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <form onSubmit={handleAIGenerate}>
                            <div className="p-4 md:p-5 space-y-4">
                                <div>
                                    <label className="block mb-2 text-sm font-medium text-[#666] dark:text-white/60">Topik</label>
                                    <textarea
                                        value={aiTopic}
                                        onChange={(e) => setAiTopic(e.target.value)}
                                        rows="3"
                                        required
                                        minLength="5"
                                        maxLength="500"
                                        className="block w-full text-sm text-[#333] dark:text-white bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg p-3 focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 outline-none"
                                        placeholder="Contoh: Panduan memilih hosting untuk website UMKM"
                                    />
                                </div>
                                <div>
                                    <label className="block mb-2 text-sm font-medium text-[#666] dark:text-white/60">Kategori</label>
                                    <select value={aiCategory} onChange={(e) => setAiCategory(e.target.value)} className="block w-full text-sm text-[#333] dark:text-white bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg p-3">
                                        <option value="">Tanpa kategori</option>
                                        {categories?.map((cat) => (
                                            <option key={cat.id} value={cat.id}>{cat.name}</option>
                                        ))}
                                    </select>
                                </div>
                                <label className="flex items-start gap-3 text-sm text-[#666] dark:text-white/60">
                                    <input type="checkbox" checked={aiPublish} onChange={(e) => setAiPublish(e.target.checked)} className="mt-0.5 rounded border-[#e5e5e5] dark:border-[#1a1a2e] text-violet-600 dark:text-violet-300 focus:ring-violet-500" />
                                    <span><span className="font-medium text-[#333] dark:text-white">Langsung publikasikan</span><br /><span className="text-xs text-[#999] dark:text-white/40">Jika tidak dicentang, artikel dibuat sebagai draft untuk direview.</span></span>
                                </label>
                            </div>
                            <div className="flex items-center p-4 md:p-5 border-t border-[#e5e5e5] dark:border-[#1a1a2e] rounded-b">
                                <button type="submit" className="text-white bg-violet-600 hover:bg-violet-700 font-medium rounded-lg text-sm px-5 py-2.5 transition"><i className="fa-solid fa-wand-magic-sparkles mr-2"></i>Masukkan ke Antrean</button>
                                <button type="button" onClick={() => setShowAIModal(false)} className="py-2.5 px-5 ms-3 text-sm font-medium text-[#666] dark:text-white/60 hover:text-[#333] dark:hover:text-white bg-white dark:bg-[#0d0d18] rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e] hover:bg-[#fafafa] dark:hover:bg-white/5 transition-colors">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Delete Confirmation Modal */}
            {deleteId && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div className="relative w-full max-w-sm m-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl p-6 text-center">
                        <div className="w-12 h-12 bg-red-50 dark:bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i className="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                        </div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Hapus Artikel?</h3>
                        <p className="text-sm text-[#999] dark:text-white/40 mb-6">Artikel yang dihapus tidak dapat dikembalikan.</p>
                        <div className="flex justify-center gap-3">
                            <button onClick={() => setDeleteId(null)} className="px-4 py-2 text-sm font-medium text-[#666] dark:text-white/60 bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-lg hover:bg-[#f5f0ff] dark:hover:bg-white/5 transition">
                                Batal
                            </button>
                            <button onClick={handleDelete} className="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                                Ya, Hapus
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
