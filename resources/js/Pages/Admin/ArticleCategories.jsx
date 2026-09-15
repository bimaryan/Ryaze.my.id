import DashboardLayout from '../../Layouts/DashboardLayout';
import { router, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function ArticleCategories({ categories }) {
    const { url } = usePage();
    const params = new URLSearchParams(url.split('?')[1] || '');
    const [deleteId, setDeleteId] = useState(null);

    function handleDelete() {
        if (!deleteId) return;
        router.delete(route('superadmin.article_categories.destroy', { article_category: deleteId }), {
            onSuccess: () => setDeleteId(null),
        });
    }

    const paginationLinks = categories?.links || [];
    const hasPages = paginationLinks.length > 3;

    return (
        <DashboardLayout title="Kategori Artikel">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-folder-open text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Kategori Artikel</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Kelola kategori untuk mengelompokkan artikel blog.</p>
                    </div>
                </div>
            </div>

            <div className="flex flex-wrap items-center gap-2 mt-4">
                <Link href={route('superadmin.articles.index')} className="inline-flex items-center bg-white dark:bg-[#0d0d18]/60 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#fafafa] dark:hover:bg-white/5 px-4 py-2 rounded-lg font-medium transition text-sm flex items-center gap-2 shadow-sm">
                    <i className="fa-solid fa-arrow-left mr-1"></i> Artikel
                </Link>
                <Link href={route('superadmin.article_categories.create')} className="inline-flex items-center bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    <i className="fa-solid fa-plus mr-2"></i> Tambah Kategori
                </Link>
            </div>

            <div className="mt-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm text-left">
                        <thead className="text-[11px] font-bold uppercase tracking-wider text-[#999] dark:text-white/40 bg-[#fafafa] dark:bg-white/[0.02] border-b border-[#e5e5e5] dark:border-[#1a1a2e]">
                            <tr>
                                <th className="px-6 py-4">Nama</th>
                                <th className="px-6 py-4">Slug</th>
                                <th className="px-6 py-4">Deskripsi</th>
                                <th className="px-6 py-4">Jumlah Artikel</th>
                                <th className="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[#e5e5e5] dark:divide-[#1a1a2e]">
                            {categories?.data?.length > 0 ? categories.data.map((category) => (
                                <tr key={category.id} className="hover:bg-[#fafafa] dark:hover:bg-white/[0.02] transition-colors">
                                    <td className="px-6 py-4 font-medium text-[#333] dark:text-white">{category.name}</td>
                                    <td className="px-6 py-4 text-sm text-[#666] dark:text-white/60"><code className="bg-[#fafafa] dark:bg-white/5 px-2 py-0.5 rounded text-xs border border-[#e5e5e5] dark:border-[#1a1a2e]">{category.slug}</code></td>
                                    <td className="px-6 py-4 text-sm text-[#666] dark:text-white/60">{category.description ? (category.description.length > 60 ? category.description.substring(0, 60) + '...' : category.description) : '-'}</td>
                                    <td className="px-6 py-4 text-sm text-[#333] dark:text-white">{category.articles_count} artikel</td>
                                    <td className="px-6 py-4">
                                        <div className="flex items-center justify-center gap-2">
                                            <Link href={route('superadmin.article_categories.edit', { hashid: category.hashid })} className="p-1.5 text-[#7c3aed] dark:text-[#a78bfa] bg-[#f5f0ff] dark:bg-[#7c3aed]/10 hover:bg-[#ede9fe] dark:hover:bg-[#7c3aed]/20 rounded-lg transition">
                                                <i className="fa-solid fa-pen-to-square"></i>
                                            </Link>
                                            <button onClick={() => setDeleteId(category.hashid)} className="p-1.5 text-red-600 dark:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-lg transition">
                                                <i className="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan="5" className="px-6 py-12 text-center text-[#999] dark:text-white/40">
                                        <p className="font-medium">Belum ada kategori.</p>
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

            {deleteId && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div className="relative w-full max-w-sm m-4 bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-xl p-6 text-center">
                        <div className="w-12 h-12 bg-red-50 dark:bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i className="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                        </div>
                        <h3 className="text-lg font-bold text-[#333] dark:text-white mb-2">Hapus Kategori?</h3>
                        <p className="text-sm text-[#999] dark:text-white/40 mb-6">Kategori yang dihapus tidak dapat dikembalikan.</p>
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
