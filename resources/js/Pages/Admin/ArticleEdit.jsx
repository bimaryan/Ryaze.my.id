import DashboardLayout from '../../Layouts/DashboardLayout';
import { useForm, Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function ArticleEdit({ article, categories }) {
    const { data, setData, post, processing, errors } = useForm({
        title: article?.title || '',
        excerpt: article?.excerpt || '',
        body: article?.body || '',
        meta_title: article?.meta_title || '',
        meta_description: article?.meta_description || '',
        status: article?.status || 'draft',
        category_id: article?.category_id || '',
        is_featured: article?.is_featured || false,
        cover_image: null,
        tags: Array.isArray(article?.tags) ? article.tags.join(', ') : (article?.tags || ''),
        _method: 'PUT',
    });

    const [coverPreview, setCoverPreview] = useState(article?.cover_image ? `/storage/${article.cover_image}` : null);

    function handleChange(e) {
        const { name, value, type, checked, files } = e.target;
        if (type === 'file') {
            setData(name, files[0]);
            if (name === 'cover_image' && files[0]) {
                setCoverPreview(URL.createObjectURL(files[0]));
            }
        } else if (type === 'checkbox') {
            setData(name, checked);
        } else {
            setData(name, value);
        }
    }

    function handleSubmit(e) {
        e.preventDefault();
        post(route('superadmin.articles.update', { hashid: article.hashid }), { forceFormData: true });
    }

    const inputCls = "w-full bg-[#fafafa] dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl px-4 py-2.5 text-sm text-[#333] dark:text-white focus:ring-2 focus:ring-[#7c3aed]/20 focus:border-[#7c3aed] outline-none transition";

    function formatNumber(num) {
        return num?.toLocaleString() || '0';
    }

    function formatDateTime(dateStr) {
        if (!dateStr) return null;
        const d = new Date(dateStr);
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const day = d.getDate();
        const month = months[d.getMonth()];
        const year = d.getFullYear();
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${day} ${month} ${year}, ${hours}:${minutes}`;
    }

    return (
        <DashboardLayout title="Edit Artikel">
            <div className="mb-1">
                <div className="flex items-center gap-3 mb-1">
                    <div className="w-10 h-10 bg-[#f5f0ff] dark:bg-[#7c3aed]/20 flex items-center justify-center">
                        <i className="fa-solid fa-pen-fancy text-[#7c3aed] dark:text-[#a78bfa]"></i>
                    </div>
                    <div>
                        <h1 className="text-lg font-bold text-[#333] dark:text-white">Edit Artikel</h1>
                        <p className="text-[13px] text-[#999] dark:text-white/40">Perbarui konten artikel: {article?.title}</p>
                    </div>
                    <div className="ml-auto">
                        <Link href={route('superadmin.articles.index')} className="bg-white dark:bg-[#0d0d18]/60 border border-[#e5e5e5] dark:border-[#1a1a2e] text-[#666] dark:text-white/60 hover:bg-[#fafafa] dark:hover:bg-white/5 px-4 py-2 rounded-lg font-medium transition text-sm flex items-center gap-2 shadow-sm">
                            Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <form onSubmit={handleSubmit}>
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-sm p-6">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Konten Artikel</h3>

                            <div className="mb-5">
                                <label className="block mb-1.5 text-sm font-medium text-[#666] dark:text-white/60">Judul <span className="text-red-500 dark:text-red-400">*</span></label>
                                <input type="text" name="title" value={data.title} onChange={handleChange} required className={inputCls} />
                                {errors.title && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.title}</p>}
                            </div>

                            <div className="mb-5">
                                <label className="block mb-1.5 text-sm font-medium text-[#666] dark:text-white/60">Ringkasan</label>
                                <textarea name="excerpt" value={data.excerpt} onChange={handleChange} rows="3" className={inputCls} />
                                {errors.excerpt && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.excerpt}</p>}
                            </div>

                            <div className="mb-5">
                                <label className="block mb-1.5 text-sm font-medium text-[#666] dark:text-white/60">Konten <span className="text-red-500 dark:text-red-400">*</span></label>
                                <textarea name="body" value={data.body} onChange={handleChange} rows="20" className={`${inputCls} min-h-[400px] font-mono text-sm`} />
                                {errors.body && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.body}</p>}
                            </div>
                        </div>

                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-sm p-6">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">
                                <i className="fa-solid fa-magnifying-glass-chart text-[#7c3aed] dark:text-[#a78bfa] mr-2"></i>Pengaturan SEO
                            </h3>
                            <div className="mb-5">
                                <label className="block mb-1.5 text-sm font-medium text-[#666] dark:text-white/60">Meta Title</label>
                                <input type="text" name="meta_title" value={data.meta_title} onChange={handleChange} maxLength="70" className={inputCls} placeholder="Override judul untuk mesin pencari (maks 70 karakter)" />
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">{data.meta_title.length}/70 karakter</p>
                            </div>
                            <div>
                                <label className="block mb-1.5 text-sm font-medium text-[#666] dark:text-white/60">Meta Description</label>
                                <textarea name="meta_description" value={data.meta_description} onChange={handleChange} rows="2" maxLength="160" className={inputCls} placeholder="Deskripsi singkat untuk hasil pencarian Google (maks 160 karakter)" />
                                <p className="text-xs text-[#999] dark:text-white/40 mt-1">{data.meta_description.length}/160 karakter</p>
                            </div>
                        </div>
                    </div>

                    {/* Sidebar */}
                    <div className="lg:col-span-1 space-y-6">
                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-sm p-6">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Publikasi</h3>

                            <div className="mb-5">
                                <label className="block mb-1.5 text-sm font-medium text-[#666] dark:text-white/60">Status</label>
                                <select name="status" value={data.status} onChange={handleChange} className={inputCls}>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>

                            <div className="mb-5">
                                <label className="block mb-1.5 text-sm font-medium text-[#666] dark:text-white/60">Kategori</label>
                                <select name="category_id" value={data.category_id} onChange={handleChange} className={inputCls}>
                                    <option value="">Tanpa Kategori</option>
                                    {categories?.map((cat) => (
                                        <option key={cat.id} value={cat.id}>{cat.name}</option>
                                    ))}
                                </select>
                            </div>

                            <div className="flex items-center gap-3 mb-5">
                                <input type="checkbox" name="is_featured" checked={data.is_featured} onChange={handleChange} className="w-4 h-4 text-[#7c3aed] dark:text-[#a78bfa] bg-white dark:bg-[#0d0d18] border-[#e5e5e5] dark:border-[#1a1a2e] rounded focus:ring-[#7c3aed]" />
                                <label className="text-sm font-medium text-[#666] dark:text-white/60">Jadikan Sorotan</label>
                            </div>

                            <div className="text-xs text-[#999] dark:text-white/40 mb-5 space-y-1">
                                <p><i className="fa-solid fa-eye mr-1"></i> {formatNumber(article?.views_count)} kali dilihat</p>
                                {article?.published_at && (
                                    <p><i className="fa-solid fa-calendar mr-1"></i> Dipublikasi: {formatDateTime(article.published_at)}</p>
                                )}
                            </div>

                            <button type="submit" disabled={processing} className="w-full bg-[#7c3aed] hover:bg-[#6d28d9] text-white font-semibold text-sm py-3 rounded-lg transition shadow-sm disabled:opacity-50">
                                <i className="fa-solid fa-save mr-2"></i>{processing ? 'Menyimpan...' : 'Perbarui Artikel'}
                            </button>
                        </div>

                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-sm p-6">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Gambar Sampul</h3>
                            {coverPreview && <img src={coverPreview} alt="Cover" className="w-full h-40 object-cover rounded-lg border border-[#e5e5e5] dark:border-[#1a1a2e] mb-3" />}
                            <input type="file" name="cover_image" accept="image/*" onChange={handleChange} className="block w-full text-sm text-[#999] dark:text-white/40 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#f5f0ff] file:text-[#7c3aed] hover:file:bg-[#ede9fe] dark:file:bg-[#7c3aed]/10 dark:file:text-[#a78bfa] dark:hover:file:bg-[#7c3aed]/20 cursor-pointer" />
                            <p className="text-xs text-[#999] dark:text-white/40 mt-2">Kosongkan jika tidak ingin mengubah.</p>
                            {errors.cover_image && <p className="text-red-500 dark:text-red-400 text-xs mt-1">{errors.cover_image}</p>}
                        </div>

                        <div className="bg-white dark:bg-[#0d0d18] border border-[#e5e5e5] dark:border-[#1a1a2e] rounded-xl shadow-sm p-6">
                            <h3 className="text-sm font-bold text-[#333] dark:text-white mb-4 border-b border-[#e5e5e5] dark:border-[#1a1a2e] pb-2">Tags</h3>
                            <input type="text" name="tags" value={data.tags} onChange={handleChange} className={inputCls} placeholder="laravel, php, tutorial" />
                            <p className="text-xs text-[#999] dark:text-white/40 mt-2">Pisahkan dengan koma.</p>
                        </div>
                    </div>
                </div>
            </form>
        </DashboardLayout>
    );
}
